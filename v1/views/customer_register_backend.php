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
$firstname       = htmlspecialchars(trim($data['firstname']        ?? ''), ENT_QUOTES, 'UTF-8');
$lastname        = htmlspecialchars(trim($data['lastname']         ?? ''), ENT_QUOTES, 'UTF-8');
$email           = filter_var(trim($data['email']           ?? ''), FILTER_SANITIZE_EMAIL);
$password        = trim($data['password']        ?? '');
$confirmPassword = trim($data['confirm_password'] ?? '');

// Validate required fields
if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Validate password match
if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

// Validate password length
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Check if email already exists
$existing = selectContent($conn, 'read_users', ['input_email' => $email]);
if (!empty($existing)) {
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
    exit;
}

// Hash password and generate identifiers
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$hashId         = 'usr_' . uniqid('', true);

// Insert user
insertContent($conn, 'read_users', [
    'hash_id'          => $hashId,
    'input_firstname'  => $firstname,
    'input_lastname'   => $lastname,
    'input_email'      => $email,
    'input_password'   => $hashedPassword,
    'input_phone'      => '',
    'input_verify'     => '0',
    'visibility'       => 'show',
    'date_created'     => date('Y-m-d'),
    'time_created'     => date('H:i:s'),
    'created_by'       => 'register',
]);

// Generate 6-char OTP token
$verifyToken = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

// Insert into verify table
insertContent($conn, 'verify', [
    'hash_id'      => uniqid('vrf_', true),
    'input_email'  => $email,
    'verify_token' => $verifyToken,
    'token_type'   => 'email_verify',
    'token_expiry' => date('Y-m-d H:i:s', strtotime('+24 hours')),
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
]);

// Send verification email
$verifyLink = $baseUrl . '/customer-verify?token=' . urlencode($verifyToken);

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
        $mail->addAddress($email, $firstname . ' ' . $lastname);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your email — ' . $shop_name;

        $fromName_h = htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8');
        $firstName_h = htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8');
        $verifyLink_h = htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8');
        $year = date('Y');

        $mail->Body = <<<HTML
            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <div style="background-color: #072708; color: #ffffff; padding: 20px; text-align: center;">
                    <h1 style="margin: 0; font-size: 24px;">{$fromName_h}</h1>
                </div>
                <div style="padding: 30px;">
                    <h2 style="color: #072708;">Welcome to {$fromName_h}!</h2>
                    <p>Hi {$firstName_h},</p>
                    <p>Thank you for creating an account with us. We're excited to have you as part of our community!</p>
                    <p>Before you can start shopping, please verify your email address by clicking the button below:</p>
                    
                    <div style="text-align: center; margin: 32px 0;">
                        <a href="{$verifyLink_h}" style="display:inline-block; padding:16px 36px; background:#072708; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:700; font-size:15px;">
                          Verify My Account
                        </a>
                    </div>
                    
                    <p style="font-size: 12px; color: #777;">
                        If you're having trouble clicking the button, copy and paste the link below into your web browser:<br>
                        <a href="{$verifyLink_h}" style="color:#072708;">{$verifyLink_h}</a>
                    </p>
                    
                    <p>Sincerely,<br>The {$fromName_h} Team</p>
                </div>
                <div style="background-color: #f4f4f4; color: #777; padding: 15px; text-align: center; font-size: 12px;">
                    <p style="margin:0;">If you did not create this account, you can safely ignore this email.</p>
                    <p style="margin:5px 0;">&copy; {$year} {$fromName_h}. All Rights Reserved.</p>
                </div>
            </div>
HTML;

        $mail->send();
    } catch (Exception $e) {
        error_log("Signup verification email failed: " . $mail->ErrorInfo);
    }
}

echo json_encode(['success' => true, 'message' => 'Account created! Please check your email to verify your address.']);
