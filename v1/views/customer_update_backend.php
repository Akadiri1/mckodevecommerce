<?php
error_reporting(0);
ob_end_clean();
header('Content-Type: application/json');

// Auth guard
if (empty($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true) ?: [];
$firstname = htmlspecialchars(trim($data['firstname'] ?? ''), ENT_QUOTES, 'UTF-8');
$lastname  = htmlspecialchars(trim($data['lastname']  ?? ''), ENT_QUOTES, 'UTF-8');
$phone     = htmlspecialchars(trim($data['phone']     ?? ''), ENT_QUOTES, 'UTF-8');

if (empty($firstname) || empty($lastname)) {
    echo json_encode(['success' => false, 'message' => 'First name and last name are required.']);
    exit;
}

$customerId = (int)$_SESSION['customer_id'];

updateContent($conn, 'read_users',
    [
        'input_firstname' => $firstname,
        'input_lastname'  => $lastname,
        'input_phone'     => $phone,
    ],
    ['id' => $customerId]
);

// Update session name
$_SESSION['customer_name'] = $firstname;

echo json_encode(['success' => true]);
