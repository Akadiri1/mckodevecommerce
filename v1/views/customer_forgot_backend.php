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

if (!empty($site_email_from) && !empty($site_email_password)) {
    try {
        require_once APP_PATH . '/phpm/PHPMailerAutoload.php';
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $site_email_smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $site_email_from;
        $mail->Password   = $site_email_password;
        $mail->SMTPSecure = $site_email_smtp_secure_type;
        $mail->Port       = (int)$site_email_smtp_port;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($site_email_from, $shop_name);
        $mail->addAddress($email, htmlspecialchars($user['input_firstname'] . ' ' . $user['input_lastname']));
        $mail->isHTML(true);
        $mail->Subject = 'Reset your password — ' . $shop_name;

        $fromName_h  = htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8');
        $firstName_h = htmlspecialchars($user['input_firstname'], ENT_QUOTES, 'UTF-8');
        $resetLink_h = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');
        $year = date('Y');

        $mail->Body = <<<HTML
            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <div style="background-color: #072708; color: #ffffff; padding: 20px; text-align: center;">
                    <h1 style="margin: 0; font-size: 24px;">{$fromName_h}</h1>
                </div>
                <div style="padding: 30px;">
                    <h2 style="color: #072708;">Reset Your Password</h2>
                    <p>Hi {$firstName_h},</p>
                    <p>We received a request to reset the password for your account. No changes have been made yet.</p>
                    <p>You can reset your password by clicking the button below. This link is only valid for **1 hour**.</p>
                    
                    <div style="text-align: center; margin: 32px 0;">
                        <a href="{$resetLink_h}" style="display:inline-block; padding:16px 36px; background:#072708; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:700; font-size:15px;">
                          Reset My Password
                        </a>
                    </div>
                    
                    <p style="font-size: 12px; color: #777;">
                        If you're having trouble clicking the button, copy and paste the link below into your web browser:<br>
                        <a href="{$resetLink_h}" style="color:#072708;">{$resetLink_h}</a>
                    </p>
                    
                    <p style="margin-top: 24px;">If you did not request a password reset, you can safely ignore this email.</p>
                    <p>Sincerely,<br>The {$fromName_h} Team</p>
                </div>
                <div style="background-color: #f4f4f4; color: #777; padding: 15px; text-align: center; font-size: 12px;">
                    &copy; {$year} {$fromName_h}. All Rights Reserved.
                </div>
            </div>
HTML;

        $mail->send();
    } catch (Exception $e) {
        error_log("Password reset email failed: " . $mail->ErrorInfo);
    }
}

echo json_encode(['success' => true]);
