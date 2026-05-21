<?php
error_reporting(0);
ob_end_clean();
header('Content-Type: application/json');

// Auth guard
if (empty($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true) ?: [];
$hashId = $data['hash_id'] ?? '';

if (empty($hashId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid address.']);
    exit;
}

$customerHash = $_SESSION['customer_hash'] ?? '';

// Delete the address, ensuring it belongs to the current user
$stmt = $conn->prepare("UPDATE read_user_addresses SET visibility = 'hide' WHERE hash_id = ? AND tb_link = ?");
$stmt->execute([$hashId, $customerHash]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Address not found or unauthorized.']);
}
