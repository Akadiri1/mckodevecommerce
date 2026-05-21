<?php
error_reporting(0);
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data            = json_decode(file_get_contents('php://input'), true) ?: [];
$token           = trim($data['token'] ?? '');
$password        = trim($data['password'] ?? '');
$confirmPassword = trim($data['confirm_password'] ?? '');

if (empty($token) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Verify token
$resets = selectContent($conn, 'verify', [
    'token'      => $token,
    'token_type' => 'password_reset',
    'visibility' => 'show'
]);

if (empty($resets)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired reset token.']);
    exit;
}

$reset = $resets[0];

// Check expiry
if (strtotime($reset['token_expiry']) < time()) {
    echo json_encode(['success' => false, 'message' => 'This reset link has expired.']);
    exit;
}

// Find user
$users = selectContent($conn, 'read_users', ['input_email' => $reset['input_email']]);
if (empty($users)) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$user = $users[0];

// Update password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
updateContent($conn, 'read_users',
    ['input_password' => $hashedPassword],
    ['id' => $user['id']]
);

// Mark reset token as used (hide it)
updateContent($conn, 'verify',
    ['visibility' => 'hide'],
    ['id' => $reset['id']]
);

echo json_encode(['success' => true]);
