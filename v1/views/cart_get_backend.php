<?php
header('Content-Type: application/json');

try {
    $cartData = getCartItems();
    if (!($cartData['success'] ?? false)) {
        echo json_encode(['success' => false, 'error' => $cartData['error'] ?? 'Unknown error']);
        exit;
    }
    $usdEnabled = isset($usdEnabled) ? $usdEnabled : false; // Should be available from index.php

    $items = [];
    foreach ($cartData['cart_items'] as $item) {
        $items[] = [
            'id'      => $item['cart_id'], // Using database ID for updates/removes
            'hash_id' => $item['cart_hash_id'],
            'name'    => $item['product_name'],
            'variant' => $item['variant_options'],
            'price'   => $usdEnabled ? $item['price_usd'] : $item['price_ngn'],
            'qty'     => $item['quantity'],
            'image'   => $item['image'],
            'url'     => '/products/' . $item['product_id'] . '/' . cleans($item['product_name']),
        ];
    }

    $count = getCartCount($_SESSION['user_id'] ?? null);
    $subtotal = $usdEnabled ? $cartData['total_usd'] : $cartData['total_ngn'];

    ob_clean();
    echo json_encode([
        'success'  => true,
        'items'    => $items,
        'count'    => $count,
        'subtotal' => $subtotal,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
