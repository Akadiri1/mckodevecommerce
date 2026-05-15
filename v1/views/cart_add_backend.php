<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$productId = htmlspecialchars(trim($data['product_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$variantId = htmlspecialchars(trim($data['variant_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$qty       = max(1, (int)($data['quantity'] ?? 1));

if (empty($productId)) { echo json_encode(['success'=>false,'error'=>'Product ID required']); die; }

// Verify product exists
$product = selectContent($conn, "panel_products", ["hash_id" => $productId, "visibility" => "show"]);
if (empty($product)) { ob_clean(); echo json_encode(['success'=>false,'error'=>'Product not found']); die; }
$product = $product[0];

// Check stock
if ((int)($product['input_stock'] ?? 999) <= 0) {
    echo json_encode(['success'=>false,'error'=>'Product is out of stock']); die;
}

// Variant info
$variantLabel = '';
if (!empty($variantId)) {
    $ids = explode(',', $variantId);
    $labels = [];
    foreach ($ids as $id) {
        $variant = selectContent($conn, "addition_product_variants", ["hash_id" => trim($id)]);
        if (!empty($variant)) $labels[] = $variant[0]['input_value'];
    }
    $variantLabel = implode(' / ', $labels);
}

// Check if already in cart for this session
$sessionId = session_id();
$existing  = selectContent($conn, "read_cart", [
    "input_session_id" => $sessionId,
    "input_product_id" => $productId,
    "input_variant_id" => $variantId,
    "visibility"       => "show"
]);

if (!empty($existing)) {
    $newQty = (int)$existing[0]['input_quantity'] + $qty;
    updateContent($conn, "read_cart", ["input_quantity" => $newQty], ["id" => $existing[0]['id']]);
} else {
    insertSafe($conn, "read_cart", [
        'hash_id'          => uniqid('cart_', true),
        'input_session_id' => $sessionId,
        'input_product_id' => $productId,
        'input_variant_id' => $variantId,
        'input_variant'    => $variantLabel,
        'input_quantity'   => $qty,
        'input_price'      => $product['input_price'],
        'visibility'       => 'show',
        'date_created'     => date('Y-m-d'),
        'time_created'     => date('H:i:s'),
        'created_by'       => $sessionId,
    ]);
}

$allCart   = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);
$cartCount = (int)array_sum(array_column($allCart, 'input_quantity'));

ob_clean();
echo json_encode(['success' => true, 'cart_count' => $cartCount, 'product_name' => $product['input_title']]);
