<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Log errors, don't show them in output
ob_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $data  = json_decode(file_get_contents('php://input'), true) ?: [];
    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    // Always return success for security (don't reveal if account exists)
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_clean();
        echo json_encode(['success' => true]);
        exit;
    }

    // Look up user
    $users = selectContent($conn, 'read_users', ['input_email' => $email, 'visibility' => 'show']);

    if (empty($users)) {
        ob_clean();
        echo json_encode(['success' => true]);
        exit;
    }

    $user  = $users[0];
    $token = bin2hex(random_bytes(32));

    // Insert password reset record into 'verify' table
    insertContent($conn, 'verify', [
        'hash_id'      => uniqid('rst_', true),
        'input_email'  => $email,
        'token'        => $token,
        'token_type'   => 'password_reset',
        'token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        'visibility'   => 'show',
        'date_created' => date('Y-m-d'),
        'time_created' => date('H:i:s'),
    ]);

    // Send reset email
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $absoluteBase = $protocol . "://" . $host . $baseUrl;
    $resetLink = $absoluteBase . '/customer-reset-password?token=' . urlencode($token);

    // Use Correct settings from DB
    $siteInfo = selectContent($conn, "settings_website_info", ["visibility" => "show"]);
    $site     = !empty($siteInfo) ? $siteInfo[0] : [];

    $smtpHost   = 'smtp.gmail.com';
    $smtpPort   = 465;
    $smtpSecure = 'ssl';
    $smtpUser   = $site['input_email_from']     ?? null;
    $smtpPass   = $site['input_email_password'] ?? null;

    if (!empty($smtpPass)) {
        $smtpPass = str_replace(' ', '', $smtpPass);

        try {
            require_once APP_PATH . '/phpm/PHPMailerAutoload.php';
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port       = $smtpPort;
            $mail->CharSet    = 'UTF-8';

            // Local server compatibility
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom($smtpUser, $shop_name);
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

    ob_clean();
    echo json_encode(['success' => true]);

} catch (Exception $err) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $err->getMessage()]);
}
