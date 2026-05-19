<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

// removeCartItem in cart_functions.php handles session/user logic and returns JSON.
removeCartItem($data);
