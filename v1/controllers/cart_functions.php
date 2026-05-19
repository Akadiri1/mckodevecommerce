<?php
/**
 * Cart and invoice helper functions ported from demo16.
 * Uses the `cart`, `variants`, and `panel_product` tables.
 */

function jsonResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function updateInvoice($invoice_id, $status) {
    global $conn;
    updateContent($conn, "invoice", ["status" => $status], ["invoice_id" => $invoice_id]);
}

function handlePaystackWebhook($data) {
    global $conn;
    $event = $data['event'] ?? null;

    if ($event === 'charge.success') {
        $reference   = $data['data']['reference'] ?? null;
        $invoice_info = selectContent($conn, 'invoice', ['invoice_id' => $reference]);
        $invoice_info = $invoice_info[0] ?? null;

        if ($invoice_info) {
            if ($invoice_info['amount_due'] * 100 == $data['data']['amount']) {
                updateInvoice($reference, 'paid');
            } else {
                error_log("Paystack webhook amount mismatch for invoice: {$reference}");
            }
        }
    }
}

/**
 * Returns cart items with full product and variant details for the current user.
 */
function getCartItems() {
    global $conn;
    $userId = $_SESSION['user_id'] ?? $_COOKIE['user_id'] ?? null;

    if (!$userId) {
        return [
            'success'    => true,
            'cart_items' => [],
            'total_ngn'  => 0,
            'total_usd'  => 0,
            'cart_count' => 0
        ];
    }

    try {
        $stmt = $conn->prepare("
            SELECT
                c.id AS cart_id,
                c.hash_id AS cart_hash_id,
                c.product_id,
                c.variant_id,
                c.quantity,
                c.date_created,
                c.time_created,
                p.id AS product_db_id,
                p.input_product_name,
                p.image_2 AS product_image,
                v.input_price_ngn,
                v.input_price_usd,
                v.input_inventory,
                v.input_weight_in_kg,
                v.image_1 AS variant_image
            FROM cart c
            LEFT JOIN panel_product p ON (c.product_id COLLATE utf8mb4_unicode_ci) = p.hash_id
            LEFT JOIN variants v ON c.variant_id = v.id
            WHERE (c.user_id COLLATE utf8mb4_unicode_ci) = :user_id
            ORDER BY c.date_created DESC, c.time_created DESC
        ");
        $stmt->execute([':user_id' => $userId]);

        $cartItems = [];
        $totalNgn  = 0;
        $totalUsd  = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = [
                'cart_id'        => (int) $row['cart_id'],
                'cart_hash_id'   => $row['cart_hash_id'],
                'product_id'     => $row['product_id'],
                'product_db_id'  => (int) ($row['product_db_id'] ?? 0),
                'product_name'   => $row['input_product_name'] ?? 'Unknown Product',
                'quantity'       => (int) $row['quantity'],
                'image'          => !empty($row['variant_image']) ? $row['variant_image'] : ($row['product_image'] ?? '/assets/img/icons/cart.svg'),
                'variant_id'     => $row['variant_id'],
                'date_added'     => ($row['date_created'] ?? '') . ' ' . ($row['time_created'] ?? ''),
                'price_ngn'      => (float) ($row['input_price_ngn'] ?? 0),
                'price_usd'      => (float) ($row['input_price_usd'] ?? 0),
                'inventory'      => (int) ($row['input_inventory'] ?? 0),
                'weight'         => (float) ($row['input_weight_in_kg'] ?? 0),
            ];

            // Fetch variant option labels (e.g. "Variants: 50ml, Normal")
            $options      = [];
            $optionsArray = [];

            if (!empty($row['variant_id'])) {
                $ids = array_map('intval', array_filter(explode(',', $row['variant_id'])));
                if (!empty($ids)) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $variantStmt = $conn->prepare("
                        SELECT po.option_name, pov.value_name
                        FROM variant_values_link vvl
                        INNER JOIN product_option_values pov ON vvl.value_id = pov.id
                        INNER JOIN product_options po ON pov.option_id = po.id
                        WHERE vvl.variant_id IN ($placeholders)
                        ORDER BY po.id
                    ");
                    $variantStmt->execute($ids);

                    $values = [];
                    while ($opt = $variantStmt->fetch(PDO::FETCH_ASSOC)) {
                        $values[]       = $opt['value_name'];
                        $optionsArray[] = ['option_name' => $opt['option_name'], 'value_name' => $opt['value_name']];
                    }
                    if (!empty($values)) {
                        $options[] = 'Variants: ' . implode(', ', $values);
                    }
                }
            }

            $item['variant_options']       = !empty($options) ? implode(', ', $options) : '';
            $item['variant_options_array'] = $optionsArray;
            $item['has_variant']           = !empty($optionsArray);

            $item['subtotal_ngn'] = $item['price_ngn'] * $item['quantity'];
            $item['subtotal_usd'] = $item['price_usd'] * $item['quantity'];

            if ($item['inventory'] === 0) {
                $item['stock_status'] = 'out_of_stock';
            } elseif ($item['inventory'] < 5) {
                $item['stock_status'] = 'low_stock';
            } else {
                $item['stock_status'] = 'in_stock';
            }

            $item['quantity_exceeds_inventory'] = $item['quantity'] > $item['inventory'];

            $cartItems[] = $item;
            $totalNgn   += $item['subtotal_ngn'];
            $totalUsd   += $item['subtotal_usd'];
        }

        return [
            'success'    => true,
            'cart_items' => $cartItems,
            'total_ngn'  => round($totalNgn, 2),
            'total_usd'  => round($totalUsd, 2),
            'cart_count' => count($cartItems),
            'user_id'    => $userId
        ];

    } catch (PDOException $e) {
        error_log("Get cart error: " . $e->getMessage());
        return [
            'success' => false,
            'error'   => "Database error: " . $e->getMessage(),
            'cart_items' => [],
            'total_ngn'  => 0,
            'total_usd'  => 0,
            'cart_count' => 0
        ];
    }
}

/**
 * Lightweight cart count for header/navbar badge.
 */
function getCartCount($userId = null) {
    global $conn;
    $userId = $userId ?? $_SESSION['user_id'] ?? null;
    if (!$userId) return 0;

    try {
        $stmt = $conn->prepare("SELECT SUM(quantity) AS count FROM cart WHERE (user_id COLLATE utf8mb4_unicode_ci) = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Get cart count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Add a product/variant to the cart. Expects POST JSON with hash_id, variant_id, quantity.
 */
function addToCart($data) {
    global $conn;
    $productHashId = $data['hash_id'] ?? $data['product_id'] ?? null;
    $variantId     = $data['variant_id'] ?? null;
    $quantity      = (int) ($data['quantity'] ?? 1);
    $userId        = $_SESSION['user_id'] ?? null;

    if (!$productHashId || $quantity < 1) {
        echo json_encode(['success' => false, 'error' => 'Invalid request data']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Validate product exists
        $stmt = $conn->prepare("SELECT id, hash_id FROM panel_product WHERE hash_id = :hash_id LIMIT 1");
        $stmt->execute([':hash_id' => $productHashId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
            exit;
        }

        // Validate variant inventory
        if ($variantId) {
            $idsToCheck = explode(',', $variantId);
            $allValid = true;
            $minStock = 999999;
            
            foreach ($idsToCheck as $vid) {
                $vid = trim($vid);
                if (empty($vid)) continue;
                
                $stmt = $conn->prepare("SELECT id, input_inventory FROM variants WHERE id = :variant_id AND product_hash_id = :hash_id LIMIT 1");
                $stmt->execute([':variant_id' => $vid, ':hash_id' => $productHashId]);
                $variant = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$variant) {
                    $allValid = false;
                    break;
                }
                $minStock = min($minStock, (int)$variant['input_inventory']);
            }

            if (!$allValid) {
                echo json_encode(['success' => false, 'error' => 'Invalid variant selected']);
                exit;
            }
            if ($minStock < $quantity) {
                echo json_encode(['success' => false, 'error' => 'Insufficient stock for one or more selected options']);
                exit;
            }
        } else {
            // No specific variant ID — check inventory of the first/base variant
            $stmt = $conn->prepare("SELECT input_inventory FROM variants WHERE product_hash_id = :hash_id LIMIT 1");
            $stmt->execute([':hash_id' => $productHashId]);
            $baseVariant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($baseVariant && (int)$baseVariant['input_inventory'] < $quantity) {
                echo json_encode(['success' => false, 'error' => 'Insufficient stock']);
                exit;
            }
        }

        // Check if already in cart
        $checkStmt = $conn->prepare("
            SELECT id, quantity FROM cart
            WHERE (user_id COLLATE utf8mb4_unicode_ci) = :user_id
            AND (product_id COLLATE utf8mb4_unicode_ci) = :product_id
            AND variant_id <=> :variant_id
            LIMIT 1
        ");
        $checkStmt->execute([
            ':user_id'    => $userId,
            ':product_id' => $productHashId,
            ':variant_id' => $variantId
        ]);
        $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            $newQuantity = (int) $existingItem['quantity'] + $quantity;
            $conn->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id")
                ->execute([':quantity' => $newQuantity, ':id' => $existingItem['id']]);
        } else {
            $cartHashId = 'cart_' . bin2hex(random_bytes(16));
            $conn->prepare("
                INSERT INTO cart (hash_id, user_id, product_id, variant_id, quantity, date_created, time_created)
                VALUES (:hash_id, :user_id, :product_id, :variant_id, :quantity, CURDATE(), CURTIME())
            ")->execute([
                ':hash_id'    => $cartHashId,
                ':user_id'    => $userId,
                ':product_id' => $productHashId,
                ':variant_id' => $variantId,
                ':quantity'   => $quantity
            ]);
        }

        $cartCount = getCartCount($userId);
        $conn->commit();
        echo json_encode(['success' => true, 'cart_count' => $cartCount]);

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

/**
 * Update the quantity of an existing cart item.
 */
function updateCartQuantity($data) {
    global $conn;
    $cartId      = $data['cart_id']  ?? null;
    $newQuantity = (int) ($data['quantity'] ?? 0);
    $userId      = $_SESSION['user_id'] ?? null;

    $cartData = $conn->prepare("SELECT * FROM cart WHERE id = :id LIMIT 1");
    $cartData->execute([':id' => $cartId]);
    $cartItem = $cartData->fetch(PDO::FETCH_ASSOC);

    if (!$cartItem) {
        echo json_encode(['success' => false, 'error' => 'Cart item not found']);
        exit;
    }

    $productHashId = $cartItem['product_id'];
    $variantId     = $cartItem['variant_id'];

    if (!$productHashId || $newQuantity < 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid request data or quantity']);
        exit;
    }

    try {
        $conn->beginTransaction();

        if ($variantId) {
            $idsToCheck = explode(',', $variantId);
            $allValid = true;
            $minStock = 999999;

            foreach ($idsToCheck as $vid) {
                $vid = trim($vid);
                if (empty($vid)) continue;

                $stmt = $conn->prepare("SELECT id, input_inventory FROM variants WHERE id = :variant_id AND product_hash_id = :hash_id LIMIT 1");
                $stmt->execute([':variant_id' => $vid, ':hash_id' => $productHashId]);
                $variant = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$variant) {
                    $allValid = false;
                    break;
                }
                $minStock = min($minStock, (int)$variant['input_inventory']);
            }

            if (!$allValid) { echo json_encode(['success' => false, 'error' => 'Invalid variant selected']); exit; }
            if ($minStock < $newQuantity) {
                echo json_encode(['success' => false, 'error' => 'Insufficient stock for requested quantity']);
                exit;
            }
        }

        $checkStmt = $conn->prepare("
            SELECT id FROM cart
            WHERE (user_id COLLATE utf8mb4_unicode_ci) = :user_id 
            AND (product_id COLLATE utf8mb4_unicode_ci) = :product_id 
            AND variant_id <=> :variant_id 
            LIMIT 1
        ");
        $checkStmt->execute([
            ':user_id'    => $userId,
            ':product_id' => $productHashId,
            ':variant_id' => $variantId
        ]);
        $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($newQuantity === 0) {
            $conn->prepare("DELETE FROM cart WHERE id = :id")->execute([':id' => $existingItem['id'] ?? $cartId]);
        } elseif ($existingItem) {
            $conn->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id")
                ->execute([':quantity' => $newQuantity, ':id' => $existingItem['id']]);
        }

        $cartCount = getCartCount($userId);
        $conn->commit();
        echo json_encode(['success' => true, 'cart_count' => $cartCount]);

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

/**
 * Remove a specific cart item by cart row id.
 */
function removeCartItem($data) {
    global $conn;
    $cartId = $data['cart_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$cartId) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = :id AND (user_id COLLATE utf8mb4_unicode_ci) = :user_id");
        $stmt->execute([':id' => $cartId, ':user_id' => $userId]);

        $cartCount = getCartCount($userId);
        echo json_encode(['success' => true, 'cart_count' => $cartCount]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
