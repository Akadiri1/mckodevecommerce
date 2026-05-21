<?php

class ProductController
{
    private $pdo;
    private $usdEnabled;
    private $globalDiscount = null;

    /**
     * @param PDO  $pdo
     * @param bool $usdEnabled  Pass true if settings_website_info.input_usd_toggle == 1
     */
    public function __construct(PDO $pdo, bool $usdEnabled = true)
    {
        $this->pdo        = $pdo;
        $this->usdEnabled = $usdEnabled;
        $this->loadGlobalDiscount();
    }

    private function loadGlobalDiscount()
    {
        $sql  = "SELECT * FROM settings_global_discount
                 WHERE id = 1
                 AND is_active = 1
                 AND (expires_at IS NULL OR expires_at > NOW())
                 LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->globalDiscount = $row;
        }
    }

    private function calculateDiscount(float $originalPrice): float
    {
        if (!$this->globalDiscount) return $originalPrice;

        $discountAmount = ($this->globalDiscount['discount_type'] === 'percentage')
            ? ($this->globalDiscount['discount_value'] / 100) * $originalPrice
            : $this->globalDiscount['discount_value'];

        return max(0, $originalPrice - $discountAmount);
    }

    /**
     * Fetches products with smart filtering (search, price, category, attributes).
     * Uses panel_product exactly as in demo16.
     */
    public function fetchProducts(array $options = []): array
    {
        $options = array_merge([
            'search'            => null,
            'category_id'       => null,
            'attribute_filters' => [],
            'min_price'         => null,
            'max_price'         => null,
            'limit'             => 20,
            'offset'            => 0,
            'currency'          => 'NGN',
        ], $options);

        $params       = [];
        $whereClauses = [];
        $joins        = [];

        $priceColumn = ($options['currency'] === 'USD' && $this->usdEnabled)
            ? 'v.input_price_usd'
            : 'v.input_price_ngn';

        // 1. Base query — uses panel_product (renamed from panel_products)
        $sql = "SELECT DISTINCT
                    pp.id AS product_id,
                    pp.hash_id AS product_hash_id,
                    pp.input_product_name AS name,
                    pp.text_description AS description,
                    pp.select_product_category AS category_name,
                    pp.image_2 AS primary_image
                FROM panel_product pp";

        // 3. Variant join
        $hasAttributeFilter = !empty($options['attribute_filters']);
        $hasPriceFilter     = is_numeric($options['min_price']) || is_numeric($options['max_price']);
        $variantJoinType    = ($hasAttributeFilter || $hasPriceFilter) ? "INNER JOIN" : "LEFT JOIN";
        $joins[]            = "$variantJoinType variants v ON pp.hash_id = v.product_hash_id";

        // 4. Search
        if (!empty($options['search'])) {
            $joins[] = "LEFT JOIN variant_values_link vvl_search ON v.id = vvl_search.variant_id";
            $joins[] = "LEFT JOIN product_option_values pov_search ON vvl_search.value_id = pov_search.id";
            $joins[] = "LEFT JOIN product_options po_search ON pov_search.option_id = po_search.id";

            $words = preg_split('/\s+/', trim($options['search']), -1, PREG_SPLIT_NO_EMPTY);
            $wordClauses = [];
            foreach ($words as $i => $word) {
                $p             = ":search_word_{$i}";
                $wordClauses[] = "(pp.input_product_name LIKE {$p}
                                 OR pp.text_description LIKE {$p}
                                 OR po_search.option_name LIKE {$p}
                                 OR pov_search.value_name LIKE {$p})";
                $params[$p]    = '%' . $word . '%';
            }
            if ($wordClauses) {
                $whereClauses[] = "(" . implode(" AND ", $wordClauses) . ")";
            }
        }

        // 5. Category filter
        if (!empty($options['category_id'])) {
            $whereClauses[]         = "pp.select_product_category = :category_id";
            $params[':category_id'] = $options['category_id'];
        }

        // 6. Price filter
        if (is_numeric($options['min_price'])) {
            $whereClauses[]       = "({$priceColumn} >= :min_price)";
            $params[':min_price'] = (float) $options['min_price'];
        }
        if (is_numeric($options['max_price'])) {
            $whereClauses[]       = "({$priceColumn} <= :max_price)";
            $params[':max_price'] = (float) $options['max_price'];
        }

        // 7. Attribute filters
        if ($hasAttributeFilter && is_array($options['attribute_filters'])) {
            foreach ($options['attribute_filters'] as $option_id => $value_id_string) {
                if (empty($value_id_string)) continue;
                $value_ids = array_filter(array_map('intval', explode(',', $value_id_string)));
                if (empty($value_ids)) continue;

                $alias   = "vvl_f{$option_id}";
                $joins[] = "INNER JOIN variant_values_link {$alias} ON v.id = {$alias}.variant_id";

                $placeholders = [];
                foreach ($value_ids as $idx => $vid) {
                    $p              = ":filter_val_{$option_id}_{$idx}";
                    $placeholders[] = $p;
                    $params[$p]     = $vid;
                }
                $whereClauses[] = "{$alias}.value_id IN (" . implode(',', $placeholders) . ")";
            }
        }

        // 8. Assemble
        $joins = array_unique($joins);
        $sql  .= " " . implode(" ", $joins);

        $whereClauses[] = "pp.visibility = 'show'";
        if ($whereClauses) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
        $sql .= " ORDER BY pp.id DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit',  (int) $options['limit'],  PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $options['offset'], PDO::PARAM_INT);
            $stmt->execute();

            $products       = [];
            $productHashIds = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[$row['product_hash_id']] = [
                    'id'             => (int) $row['product_id'],
                    'hash_id'        => $row['product_hash_id'],
                    'name'           => $row['name'],
                    'description'    => $row['description'],
                    'category_name'  => $row['category_name'],
                    'primary_image'  => $row['primary_image'],
                    'base_inventory' => 0,
                    'images'         => [],
                    'variants'       => [],
                    'has_variants'   => false,
                ];
                $productHashIds[] = $row['product_hash_id'];
            }

            if (empty($products)) return [];

            $products = $this->attachImages($products, $productHashIds);
            $products = $this->attachVariants($products, $productHashIds);

            return array_values($products);

        } catch (PDOException $e) {
            throw new Exception("fetchProducts Error: " . $e->getMessage());
        }
    }

    /**
     * Total product count for pagination.
     */
    public function fetchProductCount(array $options = []): int
    {
        $options = array_merge([
            'search'            => null,
            'category_id'       => null,
            'attribute_filters' => [],
            'min_price'         => null,
            'max_price'         => null,
            'currency'          => 'NGN',
        ], $options);

        $params       = [];
        $whereClauses = [];
        $joins        = [];

        $priceColumn = ($options['currency'] === 'USD' && $this->usdEnabled)
            ? 'v.input_price_usd'
            : 'v.input_price_ngn';

        $sql = "SELECT COUNT(DISTINCT pp.id) AS total_count FROM panel_product pp";

        $hasAttributeFilter = !empty($options['attribute_filters']);
        $hasPriceFilter     = is_numeric($options['min_price']) || is_numeric($options['max_price']);
        $variantJoinType    = ($hasAttributeFilter || $hasPriceFilter) ? "INNER JOIN" : "LEFT JOIN";
        $joins[]            = "$variantJoinType variants v ON pp.hash_id = v.product_hash_id";

        if (!empty($options['search'])) {
            $joins[] = "LEFT JOIN variant_values_link vvl_search ON v.id = vvl_search.variant_id";
            $joins[] = "LEFT JOIN product_option_values pov_search ON vvl_search.value_id = pov_search.id";
            $joins[] = "LEFT JOIN product_options po_search ON pov_search.option_id = po_search.id";

            $words       = preg_split('/\s+/', trim($options['search']), -1, PREG_SPLIT_NO_EMPTY);
            $wordClauses = [];
            foreach ($words as $i => $word) {
                $p             = ":search_word_{$i}";
                $wordClauses[] = "(pp.input_product_name LIKE {$p}
                                 OR pp.text_description LIKE {$p}
                                 OR po_search.option_name LIKE {$p}
                                 OR pov_search.value_name LIKE {$p})";
                $params[$p]    = '%' . $word . '%';
            }
            if ($wordClauses) {
                $whereClauses[] = "(" . implode(" AND ", $wordClauses) . ")";
            }
        }

        if (!empty($options['category_id'])) {
            $whereClauses[]         = "pp.select_product_category = :category_id";
            $params[':category_id'] = $options['category_id'];
        }

        if (is_numeric($options['min_price'])) {
            $whereClauses[]       = "({$priceColumn} >= :min_price)";
            $params[':min_price'] = (float) $options['min_price'];
        }
        if (is_numeric($options['max_price'])) {
            $whereClauses[]       = "({$priceColumn} <= :max_price)";
            $params[':max_price'] = (float) $options['max_price'];
        }

        if ($hasAttributeFilter && is_array($options['attribute_filters'])) {
            foreach ($options['attribute_filters'] as $option_id => $value_id_string) {
                if (empty($value_id_string)) continue;
                $value_ids = array_filter(array_map('intval', explode(',', $value_id_string)));
                if (empty($value_ids)) continue;

                $alias   = "vvl_f{$option_id}";
                $joins[] = "INNER JOIN variant_values_link {$alias} ON v.id = {$alias}.variant_id";

                $placeholders = [];
                foreach ($value_ids as $idx => $vid) {
                    $p              = ":filter_val_{$option_id}_{$idx}";
                    $placeholders[] = $p;
                    $params[$p]     = $vid;
                }
                $whereClauses[] = "{$alias}.value_id IN (" . implode(',', $placeholders) . ")";
            }
        }

        $joins = array_unique($joins);
        if ($joins) $sql .= " " . implode(" ", $joins);

        $whereClauses[] = "pp.visibility = 'show'";
        if ($whereClauses) $sql .= " WHERE " . implode(" AND ", $whereClauses);

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("fetchProductCount Error: " . $e->getMessage());
        }
    }

    private function attachImages(array $products, array $hashIds): array
    {
        if (empty($hashIds)) return $products;

        $placeholders = implode(',', array_fill(0, count($hashIds), '?'));
        $stmt         = $this->pdo->prepare(
            "SELECT asset_hash_id, image_1 FROM images WHERE asset_hash_id IN ($placeholders) ORDER BY id ASC"
        );
        $stmt->execute($hashIds);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($products[$row['asset_hash_id']])) {
                $products[$row['asset_hash_id']]['images'][] = fixImagePath($row['image_1']);
            }
        }
        return $products;
    }

    private function attachVariants(array $products, array $hashIds): array
    {
        if (empty($hashIds)) return $products;

        $placeholders = implode(',', array_fill(0, count($hashIds), '?'));
        $sql = "SELECT v.*, pov.id AS value_id, pov.value_name, po.id AS option_id, po.option_name
                FROM variants v
                LEFT JOIN variant_values_link vvl ON v.id = vvl.variant_id
                LEFT JOIN product_option_values pov ON vvl.value_id = pov.id
                LEFT JOIN product_options po ON pov.option_id = po.id
                WHERE v.product_hash_id IN ($placeholders)
                ORDER BY v.id, po.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($hashIds);

        $variantsData = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hashId    = $row['product_hash_id'];
            $variantId = $row['id'];

            if (!isset($variantsData[$hashId][$variantId])) {
                $origNgn      = (float) $row['input_price_ngn'];
                $origUsd      = $this->usdEnabled ? (float) $row['input_price_usd'] : 0.0;
                $variantEntry = [
                    'id'           => (int) $variantId,
                    'price_ngn'    => $origNgn,
                    'price_usd'    => $origUsd,
                    'inventory'    => (int) $row['input_inventory'],
                    'weight_in_kg' => (float) $row['input_weight_in_kg'],
                    'image'        => fixImagePath($row['image_1']),
                    'options'      => [],
                ];

                if ($this->globalDiscount) {
                    $discNgn = $this->calculateDiscount($origNgn);
                    $discUsd = $this->calculateDiscount($origUsd);
                    if ($discNgn < $origNgn) {
                        $variantEntry['base_price_ngn'] = $origNgn;
                        $variantEntry['base_price_usd'] = $origUsd;
                        $variantEntry['price_ngn']      = $discNgn;
                        $variantEntry['price_usd']      = $discUsd;
                        $variantEntry['discount_info']  = $this->buildDiscountInfo();
                    }
                }

                $variantsData[$hashId][$variantId] = $variantEntry;
            }

            if ($row['option_name'] && $row['value_name']) {
                $variantsData[$hashId][$variantId]['options'][] = [
                    'option_id'  => (int) $row['option_id'],
                    'value_id'   => (int) $row['value_id'],
                    'value_name' => $row['value_name'],
                ];
            }
        }

        foreach ($variantsData as $hashId => $variants) {
            if (!isset($products[$hashId])) continue;

            $products[$hashId]['variants']     = array_values($variants);
            $products[$hashId]['has_variants'] = !empty($variants);

            if (!empty($variants)) {
                $pricesNgn = array_column($variants, 'price_ngn');
                $pricesUsd = array_column($variants, 'price_usd');

                $products[$hashId]['price_range_ngn'] = $this->buildRange($pricesNgn);
                $products[$hashId]['price_range_usd'] = ($this->usdEnabled && !empty($pricesUsd))
                    ? $this->buildRange($pricesUsd)
                    : ['price' => 0];
            } else {
                $products[$hashId]['price_range_ngn'] = ['price' => 0];
                $products[$hashId]['price_range_usd'] = ['price' => 0];
            }
        }

        return $products;
    }

    public function fetchProductDetailsByHashId(string $productHashId): ?array
    {
        $sql = "SELECT
                    pp.id AS product_id,
                    pp.hash_id,
                    pp.input_product_name,
                    pp.text_description,
                    pp.image_2 AS primary_image
                FROM panel_product pp
                WHERE pp.hash_id = :hash_id
                AND pp.visibility = 'show'
                LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':hash_id', $productHashId, PDO::PARAM_STR);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) return null;

            $result = [
                'id'             => (int) $product['product_id'],
                'hash_id'        => $product['hash_id'],
                'name'           => $product['input_product_name'],
                'description'    => $product['text_description'],
                'primary_image'  => fixImagePath($product['primary_image']),
                'base_inventory' => 0,
                'images'         => [],
                'variants'       => [],
                'has_variants'   => false,
            ];

            if ($this->globalDiscount) {
                $result['discount_info'] = $this->buildDiscountInfo();
            }

            $result['images']       = $this->fetchProductImages($productHashId);
            $result['variants']     = $this->fetchProductVariants($productHashId);
            $result['has_variants'] = !empty($result['variants']);

            if (!empty($result['variants'])) {
                $pricesNgn   = array_column($result['variants'], 'price_ngn');
                $pricesUsd   = array_column($result['variants'], 'price_usd');
                $inventories = array_column($result['variants'], 'inventory');

                $result['price_range_ngn']  = $this->buildRange($pricesNgn);
                $result['price_range_usd']  = ($this->usdEnabled && !empty($pricesUsd))
                    ? $this->buildRange($pricesUsd)
                    : ['price' => 0];
                $result['base_inventory']   = array_sum($inventories);

                if ($this->globalDiscount) {
                    $baseNgn = array_column($result['variants'], 'base_price_ngn');
                    $baseUsd = array_column($result['variants'], 'base_price_usd');
                    $result['base_price_range_ngn'] = !empty($baseNgn) ? $this->buildRange($baseNgn) : null;
                    $result['base_price_range_usd'] = ($this->usdEnabled && !empty($baseUsd))
                        ? $this->buildRange($baseUsd)
                        : null;
                }
            } else {
                $result['price_range_ngn'] = ['price' => 0];
                $result['price_range_usd'] = ['price' => 0];
            }

            return $result;

        } catch (PDOException $e) {
            error_log("fetchProductDetailsByHashId error: " . $e->getMessage());
            throw new Exception("Could not fetch product details.");
        }
    }

    private function fetchProductImages(string $hashId): array
    {
        $stmt = $this->pdo->prepare("SELECT image_1 FROM images WHERE asset_hash_id = ? ORDER BY id ASC");
        $stmt->execute([$hashId]);
        $imgs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('fixImagePath', $imgs);
    }

    private function fetchProductVariants(string $hashId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, input_price_ngn AS price_ngn, input_price_usd AS price_usd,
                    input_inventory AS inventory, input_weight_in_kg AS weight_kg, image_1 AS image
             FROM variants WHERE product_hash_id = ? ORDER BY id"
        );
        $stmt->execute([$hashId]);

        $variants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vid     = $row['id'];
            $origNgn = (float) $row['price_ngn'];
            $origUsd = $this->usdEnabled ? (float) $row['price_usd'] : 0.0;

            $entry = [
                'id'           => (int) $vid,
                'price_ngn'    => $origNgn,
                'price_usd'    => $origUsd,
                'inventory'    => (int) $row['inventory'],
                'weight_in_kg' => (float) $row['weight_kg'],
                'image'        => fixImagePath($row['image']),
                'options'      => [],
            ];

            if ($this->globalDiscount) {
                $discNgn = $this->calculateDiscount($origNgn);
                $discUsd = $this->calculateDiscount($origUsd);
                if ($discNgn < $origNgn) {
                    $entry['base_price_ngn'] = $origNgn;
                    $entry['base_price_usd'] = $origUsd;
                    $entry['price_ngn']      = $discNgn;
                    $entry['price_usd']      = $discUsd;
                    $entry['discount_info']  = $this->buildDiscountInfo();
                }
            }

            $variants[$vid] = $entry;
        }

        if (empty($variants)) return [];

        $vids         = array_keys($variants);
        $placeholders = implode(',', array_fill(0, count($vids), '?'));
        $stmt         = $this->pdo->prepare(
            "SELECT vvl.variant_id, po.id AS option_id, po.option_name, pov.id AS value_id, pov.value_name
             FROM variant_values_link vvl
             INNER JOIN product_option_values pov ON vvl.value_id = pov.id
             INNER JOIN product_options po ON pov.option_id = po.id
             WHERE vvl.variant_id IN ($placeholders)
             ORDER BY vvl.variant_id, po.id"
        );
        $stmt->execute($vids);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vid = $row['variant_id'];
            if (isset($variants[$vid])) {
                $variants[$vid]['options'][] = [
                    'option_id'   => (int) $row['option_id'],
                    'option_name' => $row['option_name'],
                    'value_id'    => (int) $row['value_id'],
                    'value_name'  => $row['value_name'],
                ];
            }
        }

        return array_values($variants);
    }

    public function fetchSpecificVariant(string $productHashId, array $selectedValueIds): array
    {
        if (empty($selectedValueIds)) return ['error' => 'No options selected'];

        $valueCount   = count($selectedValueIds);
        $placeholders = implode(',', array_fill(0, $valueCount, '?'));

        $sql = "SELECT v.id,
                    v.input_price_ngn AS price_ngn,
                    v.input_price_usd AS price_usd,
                    v.input_inventory AS inventory,
                    v.input_weight_in_kg AS weight_kg,
                    v.image_1 AS image,
                    COUNT(vvl.value_id) AS match_count
                FROM variants v
                INNER JOIN variant_values_link vvl ON v.id = vvl.variant_id
                WHERE v.product_hash_id = ?
                AND vvl.value_id IN ($placeholders)
                GROUP BY v.id
                HAVING match_count = ?
                LIMIT 1";

        try {
            $params = array_merge([$productHashId], $selectedValueIds, [$valueCount]);
            $stmt   = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$variant) return ['error' => 'Variant not found or out of stock'];

            $origNgn     = (float) $variant['price_ngn'];
            $origUsd     = $this->usdEnabled ? (float) $variant['price_usd'] : 0.0;
            $variantData = [
                'id'        => (int) $variant['id'],
                'price_ngn' => $origNgn,
                'price_usd' => $origUsd,
                'inventory' => (int) $variant['inventory'],
                'weight_kg' => (float) $variant['weight_kg'],
                'image'     => $variant['image'],
            ];

            if ($this->globalDiscount) {
                $discNgn = $this->calculateDiscount($origNgn);
                $discUsd = $this->calculateDiscount($origUsd);
                if ($discNgn < $origNgn) {
                    $variantData['base_price_ngn'] = $origNgn;
                    $variantData['base_price_usd'] = $origUsd;
                    $variantData['price_ngn']      = $discNgn;
                    $variantData['price_usd']      = $discUsd;
                    $variantData['discount_info']  = $this->buildDiscountInfo();
                }
            }

            return ['variant' => $variantData];

        } catch (PDOException $e) {
            error_log("fetchSpecificVariant error: " . $e->getMessage());
            throw new Exception("Could not fetch variant.");
        }
    }

    public function fetchMasterAttributes(): array
    {
        try {
            $stmt       = $this->pdo->query(
                "SELECT po.id AS option_id, po.option_name, pov.id AS value_id, pov.value_name
                 FROM product_options po
                 LEFT JOIN product_option_values pov ON po.id = pov.option_id
                 ORDER BY po.option_name, pov.value_name"
            );
            $attributes = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $oid = $row['option_id'];
                if (!isset($attributes[$oid])) {
                    $attributes[$oid] = ['id' => (int) $oid, 'name' => $row['option_name'], 'values' => []];
                }
                if ($row['value_id']) {
                    $attributes[$oid]['values'][] = ['id' => (int) $row['value_id'], 'name' => $row['value_name']];
                }
            }

            return array_values($attributes);

        } catch (PDOException $e) {
            error_log("fetchMasterAttributes error: " . $e->getMessage());
            throw new Exception("Could not fetch attributes.");
        }
    }

    public function fetchShippingLocations(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT * FROM panel_shipping_locations WHERE is_active = 1 ORDER BY input_location_name ASC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("fetchShippingLocations error: " . $e->getMessage());
            return [];
        }
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function buildRange(array $prices): array
    {
        if (empty($prices)) return ['price' => 0];
        $min = min($prices);
        $max = max($prices);
        return ($min !== $max) ? ['min' => $min, 'max' => $max] : ['price' => $min];
    }

    private function buildDiscountInfo(): array
    {
        $label  = $this->globalDiscount['discount_label'] ?? 'Sale';
        $type   = $this->globalDiscount['discount_type']  ?? 'percentage';
        $value  = $this->globalDiscount['discount_value'] ?? 0;
        $label .= ($type === 'percentage') ? " ({$value}%)" : " ({$value})";
        $label .= " Off";
        return ['label' => $label, 'expires_at' => $this->globalDiscount['expires_at']];
    }
}
