<?php
error_reporting(0);
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$catsRaw  = $_GET['cats']  ?? '';
$page     = max(1, (int)($_GET['page']  ?? 1));
$perPage  = max(1, (int)($_GET['limit'] ?? 6));
$cats     = array_values(array_filter(array_map('trim', explode(',', $catsRaw))));
$catsLow  = array_map('strtolower', $cats);

$sym      = htmlspecialchars($shop_symbol ?? '$', ENT_QUOTES, 'UTF-8');
$baseU    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$addToCartIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg";

try {
    $allProducts = selectContentAsc($conn, "panel_product", ["visibility" => "show"], "input_order", 200);
} catch (Exception $e) {
    echo json_encode(['count' => 0, 'products' => [], 'has_more' => false, 'error' => 'db']); die;
}

// Pre-index variant prices
$_fVars = selectContent($conn, "variants", []);
$_fPriceIdx = [];
foreach ($_fVars as $_v) {
    $h = $_v['product_hash_id'];
    if (!isset($_fPriceIdx[$h])) $_fPriceIdx[$h] = $usdEnabled ? (float)$_v['input_price_usd'] : (float)$_v['input_price_ngn'];
}

// Pre-index category ID → name for filtering
$_fCats = selectContent($conn, "selection_product_category", ["visibility" => "show"]);
$_fCatById = [];
foreach ($_fCats as $_c) { $_fCatById[(string)$_c['id']] = strtolower($_c['input_title'] ?? ''); }

// Pre-index which products have variants
$_fHasVariants = [];
foreach ($_fVars as $_fv) { $_fHasVariants[$_fv['product_hash_id']] = true; }

foreach ($allProducts as &$_p) {
    $_p['input_price']    = $_fPriceIdx[$_p['hash_id']] ?? 0;
    $_p['_category_name'] = $_fCatById[(string)($_p['select_product_category'] ?? '')] ?? '';
    $_p['has_variants']   = isset($_fHasVariants[$_p['hash_id']]) ? 'true' : 'false';
}
unset($_p);

if (!empty($catsLow)) {
    $allProducts = array_values(array_filter($allProducts, function($p) use ($catsLow) {
        return in_array($p['_category_name'], $catsLow);
    }));
}

// Pagination
$totalCount = count($allProducts);
$offset     = ($page - 1) * $perPage;
$pageProducts = array_slice($allProducts, $offset, $perPage);
$hasMore    = ($offset + $perPage) < $totalCount;

$output = array_map(function($p) use ($sym, $baseU, $addToCartIcon) {
    $imgSrc = htmlspecialchars($p['image_2'] ?? '', ENT_QUOTES, 'UTF-8');
    $url    = $baseU . '/products/' . ($p['hash_id'] ?? '') . '/' . cleans($p['input_product_name'] ?? '');
    $title  = htmlspecialchars($p['input_product_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $cat    = htmlspecialchars($p['_category_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $price  = $sym . number_format((float)($p['input_price'] ?? 0), 2);

    $hid = $p['hash_id'] ?? '';
    return [
        'hash_id' => $hid,
        'html'    => '
<div class="product-card-wrap">
  <a class="product-link w-inline-block" href="' . $url . '">
    <div class="product-card">
      <div class="product-card-img">
        <img alt="' . $title . '" class="all-img" loading="lazy" src="' . $imgSrc . '">
        <div class="product-float">
          <img alt="" class="all-img" src="' . $imgSrc . '">
        </div>

        <!-- Quick View commented out — Add to Cart already opens modal -->

        <!-- Wishlist -->
        <button class="wishlist-btn-card" data-id="' . $hid . '"
                onclick="event.preventDefault();event.stopPropagation();if(window.Venora)window.Venora.toggleWishlist(\'' . $hid . '\',this);"
                style="position:absolute;top:12px;left:12px;z-index:15;background:white;border:none;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.1);opacity:0;transition:opacity 0.3s ease;">
          <img src="' . $baseU . '/assets/img/icons/heart-outline.svg" style="width:18px;height:18px;" alt="Wishlist">
        </button>

        <!-- Add to Cart overlay -->
        <div class="add-to-card-02" data-product-id="' . $hid . '" data-has-variants="' . ($p['has_variants'] ?? 'false') . '"
             onclick="event.preventDefault();event.stopPropagation();if(window.Venora)window.Venora.cartAddItem(\'' . $hid . '\',\'\',1,null,this);">
          <img alt="" class="add-to-card-icon" src="' . $addToCartIcon . '">
          <div class="p-01">Add to cart</div>
        </div>
      </div>
      <div class="product-card-bottom">
        <div class="color-gray"><div class="p-02 caps">' . $cat . '</div></div>
        <div class="product-name-price">
          <div class="heading-06">' . $title . '</div>
          <div class="heading-07">' . $price . '</div>
        </div>
      </div>
    </div>
  </a>
</div>',
    ];
}, $pageProducts);

echo json_encode([
    'count'    => $totalCount,
    'page'     => $page,
    'has_more' => $hasMore,
    'products' => $output,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
