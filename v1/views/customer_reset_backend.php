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
$token           = trim($data['token']            ?? '');
$password        = trim($data['password']         ?? '');
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

// Validate token
$resets = selectContent($conn, 'read_password_resets', [
    'reset_token' => $token,
    'is_used'     => '0',
    'visibility'  => 'show',
]);

if (empty($resets)) {
    echo json_encode(['success' => false, 'message' => 'This reset link is invalid or has already been used.']);
    exit;
}

$reset     = $resets[0];
$createdAt = strtotime($reset['date_created'] . ' ' . $reset['time_created']);

if ((time() - $createdAt) > 3600) {
    echo json_encode(['success' => false, 'message' => 'This reset link has expired. Please request a new one.']);
    exit;
}

// Find user by email
$users = selectContent($conn, 'read_users', ['input_email' => $reset['input_email'], 'visibility' => 'show']);

if (empty($users)) {
    echo json_encode(['success' => false, 'message' => 'User account not found.']);
    exit;
}

$user = $users[0];

// Update password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

updateContent($conn, 'read_users',
    ['input_password' => $hashedPassword],
    ['id' => $user['id']]
);

// Mark reset token as used
updateContent($conn, 'read_password_resets',
    ['is_used' => '1'],
    ['hash_id' => $reset['hash_id']]
);

echo json_encode(['success' => true]);
