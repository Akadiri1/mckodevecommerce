<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$data      = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = htmlspecialchars(trim($data['product_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$name      = htmlspecialchars(trim($data['name']       ?? ''), ENT_QUOTES, 'UTF-8');
$rating    = min(5, max(1, (int)($data['rating'] ?? 5)));
$review    = htmlspecialchars(trim($data['review']     ?? ''), ENT_QUOTES, 'UTF-8');

if (empty($productId) || empty($name) || empty($review)) {
    echo json_encode(['success'=>false,'error'=>'Required fields missing']); die;
}

insertSafe($conn, "read_reviews", [
    'hash_id'              => uniqid('rev_', true),
    'input_product_id'     => $productId,
    'input_reviewer_name'  => $name,
    'input_rating'         => $rating,
    'text_review'          => $review,
    'visibility'           => 'show',
    'date_created'         => date('Y-m-d'),
    'time_created'         => date('H:i:s'),
    'created_by'           => 'visitor',
]);

ob_clean();
echo json_encode(['success' => true]);
