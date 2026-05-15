<?php
error_reporting(0);
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$catsRaw = $_GET['cats'] ?? '';
$cats    = array_values(array_filter(array_map('trim', explode(',', $catsRaw))));
$catsLow = array_map('strtolower', $cats); // case-insensitive compare

$sym   = htmlspecialchars($shop_symbol ?? '$', ENT_QUOTES, 'UTF-8');
$baseU = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$addToCartIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg";

try {
    $allProducts = selectContentDesc($conn, "panel_products", ["visibility" => "show"], "id", 100);
} catch (Exception $e) {
    echo json_encode(['count' => 0, 'products' => [], 'error' => 'db']); die;
}

if (!empty($catsLow)) {
    $allProducts = array_values(array_filter($allProducts, function($p) use ($catsLow) {
        $pCat = strtolower(trim($p['select_category'] ?? ''));
        return in_array($pCat, $catsLow);
    }));
}

$output = array_map(function($p) use ($sym, $baseU, $addToCartIcon) {
    $imgSrc   = htmlspecialchars($p['image_1'] ?? '', ENT_QUOTES, 'UTF-8');
    $imgHover = htmlspecialchars($p['image_2'] ?? $p['image_1'] ?? '', ENT_QUOTES, 'UTF-8');
    $url      = $baseU . '/products/' . ($p['hash_id'] ?? '') . '/' . cleans($p['input_title'] ?? '');
    $title    = htmlspecialchars($p['input_title'] ?? '', ENT_QUOTES, 'UTF-8');
    $cat      = htmlspecialchars($p['select_category'] ?? '', ENT_QUOTES, 'UTF-8');
    $price    = $sym . number_format((float)($p['input_price'] ?? 0), 2);
    $cmpPrice = !empty($p['input_compare_price'])
        ? '<span style="text-decoration:line-through;color:#b5b5b5;font-size:13px;margin-right:6px;">' . $sym . number_format((float)$p['input_compare_price'], 2) . '</span>'
        : '';
    $hover = ($imgHover && $imgHover !== $imgSrc)
        ? '<div class="product-float"><img alt="" class="all-img" loading="lazy" src="' . $imgHover . '"></div>'
        : '';

    return [
        'hash_id' => $p['hash_id'] ?? '',
        'html'    => '
<div class="w-dyn-item" role="listitem">
  <a class="product-link w-inline-block" href="' . $url . '">
    <div class="product-card">
      <div class="product-card-img">
        <img alt="' . $title . '" class="all-img" loading="lazy" src="' . $imgSrc . '">' .
        $hover . '
        <div class="add-to-card-02" data-product-id="' . ($p['hash_id'] ?? '') . '"
             onclick="event.preventDefault();event.stopPropagation();if(window.Venora)window.Venora.cartAddItem(\'' . ($p['hash_id'] ?? '') . '\',\'\',1,null,this);">
          <img alt="" class="add-to-card-icon" src="' . $addToCartIcon . '">
          <div class="p-01">Add to cart</div>
        </div>
      </div>
      <div class="product-card-bottom">
        <div class="color-gray"><div class="p-02 caps">' . $cat . '</div></div>
        <div class="product-name-price">
          <div class="heading-06">' . $title . '</div>
          <div class="heading-07">' . $cmpPrice . $price . '</div>
        </div>
      </div>
    </div>
  </a>
</div>'
    ];
}, $allProducts);

echo json_encode(['count' => count($output), 'products' => $output]);
