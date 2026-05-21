<?php
header('Content-Type: application/json');

// 1. Get and decode the incoming JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    die;
}

// 2. Extract and validate form data
$firstName = trim($data['first_name'] ?? '');
$lastName  = trim($data['last_name'] ?? '');
$email     = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject   = trim($data['subject'] ?? 'No Subject');
$message   = trim($data['message'] ?? '');
$name      = trim($firstName . ' ' . $lastName);

if (empty($firstName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill out all required fields.']);
    die;
}


// 3. Fetch mailer settings from the database
$siteInfo = selectContent($conn, "settings_website_info", ["visibility" => "show"]);
$site     = !empty($siteInfo) ? $siteInfo[0] : [];

$smtpHost   = $site['input_email_smtp_host']        ?? null;
$smtpPort   = (int)($site['input_email_smtp_port']  ?? 587);
$smtpSecure = $site['input_email_smtp_secure_type'] ?? 'tls';
$smtpUser   = $site['input_email_from']             ?? null;
$smtpPass   = $site['input_email_password']         ?? null;
$fromName   = $site['input_name']                   ?? 'Venora';
$adminEmail = $site['input_email_from']             ?? null;

if (empty($smtpPass) || empty($adminEmail)) {
    error_log("Contact form failed: SMTP or admin email is not configured in settings_website_info.");
    echo json_encode(['success' => false, 'message' => 'The server is not configured to send emails. Please contact the site administrator.']);
    die;
}

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

    // Prepare variables for email bodies
    $name_h = htmlspecialchars($name);
    $email_h = htmlspecialchars($email);
    $subject_h = htmlspecialchars($subject);
    $message_h = nl2br(htmlspecialchars($message));
    $host_h = htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'your website');
    $fromName_h = htmlspecialchars($fromName);
    $year = date('Y');

    // 4. Send notification email to the site admin
    $mail->setFrom($adminEmail, $fromName . ' Contact Form');
    $mail->addAddress($adminEmail);
    $mail->addReplyTo($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission: ' . $subject_h;
    $mail->Body    = <<<HTML
        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6;">
            <h2 style="color: #072708;">New Contact Form Submission</h2>
            <p>You have received a new message from your website contact form.</p>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px; font-weight: bold; width: 120px;">Name:</td>
                    <td style="padding: 8px;">{$name_h}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px; font-weight: bold;">Email:</td>
                    <td style="padding: 8px;"><a href="mailto:{$email_h}">{$email_h}</a></td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px; font-weight: bold;">Subject:</td>
                    <td style="padding: 8px;">{$subject_h}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px; font-weight: bold; vertical-align: top;">Message:</td>
                    <td style="padding: 8px;">{$message_h}</td>
                </tr>
            </table>
            <p style="font-size: 12px; color: #777;">This email was sent from the contact form on {$host_h}.</p>
        </div>
HTML;
    
    $mail->send();

    // 5. Send confirmation email to the user
    $mail->clearAddresses();
    $mail->clearReplyTos();

    $mail->addAddress($email, $name);
    $mail->setFrom($adminEmail, $fromName);
    $mail->Subject = 'Thank you for contacting ' . $fromName_h;
    $mail->Body    = <<<HTML
        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background-color: #072708; color: #ffffff; padding: 20px; text-align: center;">
                <h1 style="margin: 0; font-size: 24px;">{$fromName_h}</h1>
            </div>
            <div style="padding: 30px;">
                <h2 style="color: #072708;">We've received your message.</h2>
                <p>Dear {$name_h},</p>
                <p>Thank you for getting in touch with us. We have received your message and will get back to you as soon as possible.</p>
                <p>For your reference, here is a copy of your message:</p>
                <div style="background-color: #f9f9f9; border-left: 3px solid #072708; padding: 15px; margin: 20px 0;">
                    <p><strong>Subject:</strong> {$subject_h}</p>
                    <p><strong>Message:</strong><br>{$message_h}</p>
                </div>
                <p>Sincerely,<br>The {$fromName_h} Team</p>
            </div>
            <div style="background-color: #f4f4f4; color: #777; padding: 15px; text-align: center; font-size: 12px;">
                &copy; {$year} {$fromName_h}. All Rights Reserved.
            </div>
        </div>
HTML;

    $mail->send();

    // 6. Return success response
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Contact form mailer failed: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'A server error occurred. We could not send your message.']);
}
