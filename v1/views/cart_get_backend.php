<?php
header('Content-Type: application/json');

$sessionId = session_id();
$cartRows  = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);

$items     = [];
$subtotal  = 0;

foreach ($cartRows as $row) {
    $p = selectContent($conn, "panel_products", ["hash_id" => $row['input_product_id'], "visibility" => "show"]);
    if (empty($p)) continue;
    $p     = $p[0];
    $price = (float)$p['input_price'];
    $qty   = (int)$row['input_quantity'];
    $subtotal += $price * $qty;
    $items[] = [
        'id'      => $row['hash_id'],
        'name'    => $p['input_title'],
        'variant' => $row['input_variant'] ?? '',
        'price'   => $price,
        'qty'     => $qty,
        'image'   => $p['image_1'] ?? '',
        'url'     => '/products/' . $p['hash_id'] . '/' . cleans($p['input_title']),
    ];
}

$count = (int)array_sum(array_column($cartRows, 'input_quantity'));

ob_clean();
echo json_encode([
    'items'    => $items,
    'count'    => $count,
    'subtotal' => $subtotal,
]);
