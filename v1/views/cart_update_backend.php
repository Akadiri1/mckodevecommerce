<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$data   = json_decode(file_get_contents('php://input'), true) ?: [];
$cartId = htmlspecialchars(trim($data['cart_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$qty    = max(1, (int)($data['quantity'] ?? 1));
if (empty($cartId)) { ob_clean(); echo json_encode(['success'=>false]); die; }

updateContent($conn, "read_cart", ["input_quantity" => $qty], ["hash_id" => $cartId]);

$sessionId = session_id();
$allCart   = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);
$cartCount = (int)array_sum(array_column($allCart, 'input_quantity'));

ob_clean();
echo json_encode(['success' => true, 'cart_count' => $cartCount]);
