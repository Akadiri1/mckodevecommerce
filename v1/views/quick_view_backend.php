<?php
header('Content-Type: application/json');

try {
    $productId = htmlspecialchars(trim($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (empty($productId)) {
        echo json_encode(['error' => 'ID required']); die;
    }

    // $conn, $usdEnabled are already available from index.php
    $controller  = new ProductController($conn, $usdEnabled);
    $details     = $controller->fetchProductDetailsByHashId($productId);

    if (!$details) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']); die;
    }

    // Transform variants for the frontend renderQuickView
    $formattedVariants = [];
    if (!empty($details['variants'])) {
        foreach ($details['variants'] as $v) {
            $optName = !empty($v['options']) ? $v['options'][0]['option_name'] : 'Options';
            $formattedVariants[] = [
                'id'          => $v['id'],
                'input_name'  => $optName,
                'input_value' => !empty($v['options']) ? $v['options'][0]['value_name'] : 'Default',
                'input_price' => $usdEnabled ? $v['price_usd'] : $v['price_ngn'],
                'stock'       => $v['inventory']
            ];
        }
    }

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
        'variants'            => $formattedVariants,
        'in_stock'            => $details['base_inventory'] > 0
    ];

    ob_clean();
    echo json_encode($response);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
}
