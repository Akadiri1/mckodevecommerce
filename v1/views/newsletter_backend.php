<?php
header('Content-Type: application/json');

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    die;
}

// Check if email already subscribed
$existing = selectContent($conn, "read_newsletter", ["input_email" => $email, "visibility" => "show"]);
if (!empty($existing)) {
    echo json_encode(['success' => false, 'message' => 'This email is already subscribed.']);
    die;
}

// Insert subscriber
insertSafe($conn, "read_newsletter", [
    'hash_id'      => uniqid('nl_', true),
    'input_email'  => $email,
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => 'visitor',
]);

// Send welcome email
$siteInfo = selectContent($conn, "settings_website_info", ["visibility" => "show"]);
$site     = !empty($siteInfo) ? $siteInfo[0] : [];

$smtpHost     = $site['input_email_smtp_host']        ?? 'smtp.gmail.com';
$smtpPort     = (int)($site['input_email_smtp_port']  ?? 587);
$smtpSecure   = $site['input_email_smtp_secure_type'] ?? 'tls';
$smtpUser     = $site['input_email_from']             ?? '';
$smtpPass     = $site['input_email_password']         ?? '';
$fromName     = $site['input_name']                   ?? 'Venora';
$fromEmail    = $site['input_email_from']             ?? 'hello@venora.com';

if (!empty($smtpPass)) {
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
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to ' . $fromName . ' — You\'re In!';
        $mail->Body    = '
<!DOCTYPE html><html><body style="font-family:sans-serif;background:#f6f6f6;margin:0;padding:20px;">
<div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;">
  <div style="background:#072708;padding:32px;text-align:center;">
    <h1 style="color:#ffffff;margin:0;font-size:28px;letter-spacing:4px;">VENORA</h1>
  </div>
  <div style="padding:32px;">
    <h2 style="color:#072708;margin-top:0;">Welcome to the Venora family!</h2>
    <p style="color:#5c5f6a;line-height:1.7;">
      Thank you for subscribing. You\'ll be the first to know about new product launches,
      exclusive offers, and skincare tips from Venora.
    </p>
    <p style="color:#5c5f6a;line-height:1.7;">Your natural beauty, expressed with care.</p>
    <a href="https://' . ($_SERVER['HTTP_HOST'] ?? 'venora.com') . '/products"
       style="display:inline-block;margin-top:16px;padding:14px 28px;background:#072708;color:#ffffff;text-decoration:none;border-radius:4px;font-size:14px;">
      Shop Now
    </a>
  </div>
  <div style="padding:16px 32px;border-top:1px solid #eee;text-align:center;">
    <p style="color:#b5b5b5;font-size:12px;margin:0;">&copy; ' . date('Y') . ' Venora. All Rights Reserved.</p>
  </div>
</div>
</body></html>';
        $mail->send();
    } catch (Exception $e) {
        error_log("Newsletter welcome email failed: " . $e->getMessage());
    }
}

echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
