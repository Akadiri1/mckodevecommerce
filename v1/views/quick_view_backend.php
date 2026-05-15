<?php
header('Content-Type: application/json');

$productId = htmlspecialchars(trim($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8');
if (empty($productId)) { 
    http_response_code(400); 
    ob_clean(); 
    echo json_encode(['error'=>'ID required']); die; 
}

$p = selectContent($conn, "panel_products", ["hash_id" => $productId, "visibility" => "show"]);
if (empty($p)) { 
    http_response_code(404); 
    ob_clean(); 
    echo json_encode(['error'=>'Product not found']); die; 
}
$p = $p[0];

// Gallery
$galleryRaw = selectContent($conn, "addition_product_images", ["tb_link" => $productId, "visibility" => "show"]);
$images = array_merge(
    array_filter([$p['image_1'] ?? null]),
    array_filter([$p['image_2'] ?? null]),
    array_column($galleryRaw, 'image_1')
);
$images = array_values(array_unique(array_map('trim', array_filter($images))));
if (empty($images)) $images = ['/assets/img/icons/cart.svg'];

// Variants
$variantsRaw = selectContentAsc($conn, "addition_product_variants", ["tb_link" => $productId, "visibility" => "show"], "input_order", 20);
$variants = array_map(function($v) {
    return [
        'id'          => $v['hash_id'],
        'input_name'  => $v['input_name'],
        'input_value' => $v['input_value'],
        'input_price' => $v['input_price'] ?? null,
        'stock'       => (int)($v['input_stock'] ?? 999),
    ];
}, $variantsRaw);

// Reviews count and data
$reviews    = selectContent($conn, "read_reviews", ["input_product_id" => $productId, "visibility" => "show"]);
$reviewCount= count($reviews);
$avgRating  = $reviewCount > 0
    ? round(array_sum(array_column($reviews, 'input_rating')) / $reviewCount, 1)
    : (float)($p['input_rating'] ?? 4.5);

$reviewsList = array_map(function($r) {
    return [
        'name' => $r['input_reviewer_name'],
        'text' => $r['text_review'],
        'rating' => (int)$r['input_rating']
    ];
}, $reviews);

ob_clean();
echo json_encode([
    'hash_id'             => $p['hash_id'],
    'input_title'         => $p['input_title'],
    'input_slug'          => cleans($p['input_title']),
    'select_category'     => $p['select_category'] ?? '',
    'input_price'         => $p['input_price'],
    'input_compare_price' => $p['input_compare_price'] ?? null,
    'input_rating'        => $avgRating,
    'input_reviews_count' => $reviewCount,
    'reviews_list'        => $reviewsList,
    'text_description'    => $p['text_description'] ?? '',
    'image_1'             => $p['image_1'] ?? '',
    'images'              => $images,
    'variants'            => $variants,
    'in_stock'            => (int)($p['input_stock'] ?? 999) > 0,
]);
