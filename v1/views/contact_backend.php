<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$data    = json_decode(file_get_contents('php://input'), true) ?: [];
$name    = htmlspecialchars(trim($data['name']    ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars(trim($data['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($data['message'] ?? ''), ENT_QUOTES, 'UTF-8');

if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    echo json_encode(['success'=>false,'error'=>'Required fields missing']); die;
}

insertSafe($conn, "read_contact_messages", [
    'hash_id'       => uniqid('msg_', true),
    'input_name'    => $name,
    'input_email'   => $email,
    'input_subject' => $subject,
    'text_message'  => $message,
    'visibility'    => 'show',
    'date_created'  => date('Y-m-d'),
    'time_created'  => date('H:i:s'),
    'created_by'    => 'visitor',
]);

// Email notification
if (!empty($site_email_from) && !empty($site_email_password)) {
    try {
        require APP_PATH . '/phpm/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $site_email_smtp_host; $mail->SMTPAuth = true;
        $mail->Username = $site_email_from; $mail->Password = $site_email_password;
        $mail->SMTPSecure = $site_email_smtp_secure_type; $mail->Port = (int)$site_email_smtp_port;
        $mail->setFrom($site_email_from, $shop_name);
        $mail->addAddress($site_email_from, $shop_name);
        $mail->addReplyTo($email, $name);
        $mail->isHTML(true);
        $mail->Subject = "New Contact: $subject — $name";
        $mail->Body = "<p><strong>From:</strong> $name ($email)</p><p><strong>Subject:</strong> $subject</p><p>$message</p>";
        $mail->send();
    } catch (Exception $e) { /* silent */ }
}

echo json_encode(['success' => true]);
