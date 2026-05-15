<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$data      = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = htmlspecialchars(trim($data['product_id'] ?? ''), ENT_QUOTES, 'UTF-8');
if (empty($productId)) { ob_clean(); echo json_encode(['success'=>false]); die; }

if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

$added = false;
if (in_array($productId, $_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = array_values(array_filter($_SESSION['wishlist'], fn($id) => $id !== $productId));
} else {
    $_SESSION['wishlist'][] = $productId;
    $added = true;
}

ob_clean();
echo json_encode(['success' => true, 'added' => $added, 'count' => count($_SESSION['wishlist'])]);
