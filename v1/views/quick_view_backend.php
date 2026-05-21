<?php
header('Content-Type: application/json');

try {
    $productId = htmlspecialchars(trim($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (empty($productId)) {
        echo json_encode(['error' => 'ID required']); die;
    }

    $controller  = new ProductController($conn, $usdEnabled);
    $details     = $controller->fetchProductDetailsByHashId($productId);

    if (!$details) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']); die;
    }

    // details['variants'] already contains 'options' array with all categories/values
    $response = [
        'hash_id'             => $details['hash_id'],
        'input_title'         => $details['name'],
        'input_slug'          => cleans($details['name']),
        'select_category'     => $details['category_name'] ?? '',
        'input_price'         => $usdEnabled ? ($details['price_range_usd']['price'] ?? $details['price_range_usd']['min'] ?? 0) : ($details['price_range_ngn']['price'] ?? $details['price_range_ngn']['min'] ?? 0),
        'input_compare_price' => null,
        'input_rating'        => $details['input_rating'] ?? 4.5,
        'input_reviews_count' => count($details['reviews'] ?? []),
        'reviews_list'        => $details['reviews'] ?? [],
        'text_description'    => $details['description'],
        'image_1'             => $details['primary_image'],
        'images'              => $details['images'],
        'variants'            => $details['variants'] ?? [],
        'in_stock'            => $details['base_inventory'] > 0
    ];

    if (ob_get_level()) ob_clean();
    echo json_encode($response);
} catch (Exception $e) {
    if (ob_get_level()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
}
