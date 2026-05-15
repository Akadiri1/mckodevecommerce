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
    'tb'           => 'read_users',
    'tb_link'      => $hashId,
    'verify_token' => $verifyToken,
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => $email,
]);

// Send verification email
$verifyLink = $baseUrl . '/customer-verify?token=' . urlencode($verifyToken);

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
        $mail->addAddress($email, $firstname . ' ' . $lastname);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your email — ' . $shop_name;
        $mail->Body    = "<div style='font-family:Inter,Arial,sans-serif;max-width:600px;margin:0 auto;padding:32px;'>"
            . "<h2 style='color:#072708;'>Welcome to " . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') . "!</h2>"
            . "<p style='color:#444;'>Hi " . htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') . ", thanks for creating an account.</p>"
            . "<p style='color:#444;'>Please verify your email address by clicking the button below:</p>"
            . "<a href='" . htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8') . "' style='display:inline-block;margin:20px 0;padding:14px 28px;background:#072708;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;'>Verify Email</a>"
            . "<p style='color:#888;font-size:13px;'>Or copy and paste this link: " . htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8') . "</p>"
            . "<p style='color:#888;font-size:13px;'>If you did not create this account, you can safely ignore this email.</p>"
            . "</div>";
        $mail->send();
    } catch (Exception $e) {
        // Silent - do not expose SMTP errors to client
    }
}

echo json_encode(['success' => true, 'message' => 'Account created! Please check your email to verify your address.']);
