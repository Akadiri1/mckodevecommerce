<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

try {
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
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if ($password !== $confirmPassword) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    if (strlen($password) < 8) {
        ob_clean();
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
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid or expired reset token.']);
        exit;
    }

    $reset = $resets[0];

    // Check expiry
    if (strtotime($reset['token_expiry']) < time()) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'This reset link has expired.']);
        exit;
    }

    // Find user
    $users = selectContent($conn, 'read_users', ['input_email' => $reset['input_email']]);
    if (empty($users)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    $user = $users[0];

    // Update password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Log for debugging
    error_log("Updating password for UID: " . $user['id'] . " with hash prefix: " . substr($hashedPassword, 0, 10));

    updateContent($conn, 'read_users',
        ['input_password' => $hashedPassword],
        ['id' => $user['id']]
    );

    // Mark reset token as used (hide it)
    updateContent($conn, 'verify',
        ['visibility' => 'hide'],
        ['id' => $reset['id']]
    );

    ob_clean();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
