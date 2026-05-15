<?php
error_reporting(0);
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true) ?: [];
$email    = filter_var(trim($data['email']    ?? ''), FILTER_SANITIZE_EMAIL);
$password = trim($data['password'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

// Look up user
$users = selectContent($conn, 'read_users', ['input_email' => $email, 'visibility' => 'show']);

if (empty($users)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    exit;
}

$user = $users[0];

// Check password
if (!password_verify($password, $user['input_password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    exit;
}

// Check email verified
if (($user['input_verify'] ?? '0') !== '1') {
    echo json_encode(['success' => false, 'message' => 'Please verify your email address before signing in.']);
    exit;
}

// Set session
$_SESSION['customer_id']   = $user['id'];
$_SESSION['customer_hash'] = $user['hash_id'];
$_SESSION['customer_name'] = htmlspecialchars($user['input_firstname'] ?? '', ENT_QUOTES, 'UTF-8');

echo json_encode(['success' => true]);
