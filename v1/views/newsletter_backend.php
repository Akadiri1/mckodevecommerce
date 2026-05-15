<?php
header('Content-Type: application/json');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false]); die;
}
insertSafe($conn, "read_newsletter", [
    'hash_id'      => uniqid('nl_', true),
    'input_email'  => $email,
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => 'visitor',
]);
echo json_encode(['success' => true]);
