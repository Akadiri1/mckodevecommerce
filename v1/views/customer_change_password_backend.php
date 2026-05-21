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

$data    = json_decode(file_get_contents('php://input'), true) ?: [];
$curPass = $data['current_password'] ?? '';
$newPass = $data['new_password'] ?? '';

if (empty($curPass) || empty($newPass)) {
    echo json_encode(['success' => false, 'message' => 'Both current and new passwords are required.']);
    exit;
}

$customerId = (int)$_SESSION['customer_id'];

// Fetch current user record to verify password
$users = selectContent($conn, 'read_users', ['id' => $customerId, 'visibility' => 'show']);
if (empty($users)) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}
$user = $users[0];

// Verify current password
// Assuming passwords are hashed using password_hash/password_verify
if (!password_verify($curPass, $user['input_password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
    exit;
}

// Update with new hashed password
$hashed = password_hash($newPass, PASSWORD_DEFAULT);

updateContent($conn, 'read_users',
    ['input_password' => $hashed],
    ['id' => $customerId]
);

echo json_encode(['success' => true]);
