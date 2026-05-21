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

$smtpHost     = 'smtp.gmail.com';
$smtpPort     = 465;              // SWITCHED TO 465
$smtpSecure   = 'ssl';            // SWITCHED TO SSL
$smtpUser     = $site['input_email_from']     ?? '';
$smtpPass     = $site['input_email_password'] ?? '';
$fromName     = $site['input_name']           ?? 'Venora';
$fromEmail    = $site['input_email_from']     ?? 'hello@venora.com';

if (!empty($smtpPass)) {
    $smtpPass = str_replace(' ', '', $smtpPass); // Remove spaces

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

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to ' . $fromName . '!';

        $fromName_h = htmlspecialchars($fromName);
        $year = date('Y');
        $host = htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'venora.com');

        $mail->Body    = <<<HTML
            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <div style="background-color: #072708; color: #ffffff; padding: 20px; text-align: center;">
                    <h1 style="margin: 0; font-size: 24px;">{$fromName_h}</h1>
                </div>
                <div style="padding: 30px;">
                    <h2 style="color: #072708;">Welcome! You're on the list.</h2>
                    <p>Thank you for subscribing to the {$fromName_h} newsletter.</p>
                    <p>You'll be the first to know about our new product launches, exclusive offers, and the latest skincare tips and insights from our experts.</p>
                    <p>We're excited to have you as part of our community.</p>
                    <a href="https://{$host}/products" style="display:inline-block;margin-top:16px;padding:14px 28px;background:#072708;color:#ffffff;text-decoration:none;border-radius:4px;font-size:14px;">
                      Explore Our Products
                    </a>
                </div>
                <div style="background-color: #f4f4f4; color: #777; padding: 15px; text-align: center; font-size: 12px;">
                    <p style="margin:0;">You received this email because you subscribed to our newsletter.</p>
                    <p style="margin:5px 0;">&copy; {$year} {$fromName_h}. All Rights Reserved.</p>
                </div>
            </div>
HTML;
        $mail->send();
    } catch (Exception $e) {
        error_log("Newsletter welcome email failed: " . $mail->ErrorInfo);
    }
}

echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
