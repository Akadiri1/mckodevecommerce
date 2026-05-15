<?php
error_reporting(0);
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$data  = json_decode(file_get_contents('php://input'), true) ?: [];
$email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);

// Always return success — security: don't reveal if email exists
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => true]);
    exit;
}

// Look up user
$users = selectContent($conn, 'read_users', ['input_email' => $email, 'visibility' => 'show']);

if (empty($users)) {
    // User not found — still return success
    echo json_encode(['success' => true]);
    exit;
}

$user  = $users[0];
$token = bin2hex(random_bytes(32));

// Insert password reset record
insertContent($conn, 'read_password_resets', [
    'hash_id'      => uniqid('rst_', true),
    'input_email'  => $email,
    'reset_token'  => $token,
    'is_used'      => '0',
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => $email,
]);

// Send reset email
$resetLink = $baseUrl . '/customer-reset-password?token=' . urlencode($token);
$firstname = htmlspecialchars($user['input_firstname'] ?? 'there', ENT_QUOTES, 'UTF-8');

if (!empty($site_email_from) && !empty($site_email_password)) {
    try {
        require APP_PATH . '/phpm/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host       = $site_email_smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $site_email_from;
        $mail->Password   = $site_email_password;
        $mail->SMTPSecure = $site_email_smtp_secure_type;
        $mail->Port       = (int)$site_email_smtp_port;
        $mail->setFrom($site_email_from, $shop_name);
        $mail->addAddress($email, $firstname);
        $mail->isHTML(true);
        $mail->Subject = 'Reset your password — ' . $shop_name;
        $mail->Body    = "<div style='font-family:Inter,Arial,sans-serif;max-width:600px;margin:0 auto;padding:32px;'>"
            . "<h2 style='color:#072708;'>Reset Your Password</h2>"
            . "<p style='color:#444;'>Hi " . $firstname . ", we received a request to reset your password.</p>"
            . "<p style='color:#444;'>Click the button below to choose a new password. This link will expire in 1 hour.</p>"
            . "<a href='" . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . "' style='display:inline-block;margin:20px 0;padding:14px 28px;background:#072708;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;'>Reset Password</a>"
            . "<p style='color:#888;font-size:13px;'>Or copy and paste: " . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . "</p>"
            . "<p style='color:#888;font-size:13px;'>If you did not request a password reset, please ignore this email.</p>"
            . "</div>";
        $mail->send();
    } catch (Exception $e) {
        // Silent
    }
}

echo json_encode(['success' => true]);
