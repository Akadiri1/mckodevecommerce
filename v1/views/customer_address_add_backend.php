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
$label     = htmlspecialchars(trim($data['label'] ?? 'Home'), ENT_QUOTES, 'UTF-8');
$firstname = htmlspecialchars(trim($data['firstname'] ?? ''), ENT_QUOTES, 'UTF-8');
$lastname  = htmlspecialchars(trim($data['lastname']  ?? ''), ENT_QUOTES, 'UTF-8');
$phone     = htmlspecialchars(trim($data['phone']     ?? ''), ENT_QUOTES, 'UTF-8');
$address   = htmlspecialchars(trim($data['address']   ?? ''), ENT_QUOTES, 'UTF-8');
$city      = htmlspecialchars(trim($data['city']      ?? ''), ENT_QUOTES, 'UTF-8');
$state     = htmlspecialchars(trim($data['state']     ?? ''), ENT_QUOTES, 'UTF-8');
$country   = htmlspecialchars(trim($data['country']   ?? ''), ENT_QUOTES, 'UTF-8');
$postcode  = htmlspecialchars(trim($data['postcode']  ?? ''), ENT_QUOTES, 'UTF-8');
$is_default= ($data['is_default'] ?? false) ? '1' : '0';

if (empty($firstname) || empty($lastname) || empty($address) || empty($city) || empty($state) || empty($country)) {
    echo json_encode(['success' => false, 'message' => 'Please fill out all required fields.']);
    exit;
}

$customerId = (int)$_SESSION['customer_id'];
$customerHash = $_SESSION['customer_hash'] ?? '';

// If this is default, unset other defaults for this user
if ($is_default === '1') {
    $conn->prepare("UPDATE read_user_addresses SET input_is_default = '0' WHERE tb_link = ?")
         ->execute([$customerHash]);
}

// Insert new address
insertSafe($conn, 'read_user_addresses', [
    'hash_id'         => uniqid('addr_', true),
    'tb'              => 'read_users',
    'tb_link'         => $customerHash,
    'input_label'     => $label,
    'input_firstname' => $firstname,
    'input_lastname'  => $lastname,
    'input_phone'     => $phone,
    'input_address'   => $address,
    'input_city'      => $city,
    'input_state'     => $state,
    'input_country'   => $country,
    'input_postcode'  => $postcode,
    'input_is_default'=> $is_default,
    'visibility'      => 'show',
    'date_created'    => date('Y-m-d'),
    'time_created'    => date('H:i:s'),
    'created_by'      => 'customer'
]);

echo json_encode(['success' => true]);
