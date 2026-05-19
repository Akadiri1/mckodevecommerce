<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

// The addToCart function in cart_functions.php handles validation, inventory, and session/user logic.
// It already sends its own JSON response and exits.
addToCart($data);
