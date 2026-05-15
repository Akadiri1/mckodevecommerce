<?php
error_reporting(0);
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── Auth guard — must be signed in ───────────────────────────
if (empty($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'auth' => false, 'message' => 'You must be signed in to leave a review.']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = htmlspecialchars(trim($data['product_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$name      = htmlspecialchars(trim($data['name']       ?? ''), ENT_QUOTES, 'UTF-8');
$title     = htmlspecialchars(trim($data['title']      ?? ''), ENT_QUOTES, 'UTF-8');
$rating    = min(5, max(1, (int)($data['rating']       ?? 0)));
$review    = htmlspecialchars(trim($data['review']     ?? ''), ENT_QUOTES, 'UTF-8');

// ── Server-side validation ────────────────────────────────────
if (empty($productId)) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']); exit;
}
if (empty($name) || mb_strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter your name (min 2 characters).']); exit;
}
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please select a rating between 1 and 5.']); exit;
}
if (empty($review) || mb_strlen($review) < 10) {
    echo json_encode(['success' => false, 'message' => 'Review must be at least 10 characters.']); exit;
}
if (mb_strlen($review) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Review is too long (max 2000 characters).']); exit;
}

// ── Check not already reviewed this product ───────────────────
$existing = selectContent($conn, 'read_reviews', [
    'input_product_id' => $productId,
    'created_by'       => (string)$_SESSION['customer_id'],
    'visibility'       => 'show',
]);
if (!empty($existing)) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']); exit;
}

// ── Insert review ─────────────────────────────────────────────
insertSafe($conn, 'read_reviews', [
    'hash_id'             => uniqid('rev_', true),
    'input_product_id'    => $productId,
    'input_reviewer_name' => $name,
    'input_title'         => $title,
    'input_rating'        => $rating,
    'text_review'         => $review,
    'visibility'          => 'show',
    'date_created'        => date('Y-m-d'),
    'time_created'        => date('H:i:s'),
    'created_by'          => (string)$_SESSION['customer_id'],
]);

echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
