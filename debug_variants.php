<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";
require __DIR__ . "/v1/controllers/controller.php";
require __DIR__ . "/v1/controllers/ProductController.php";

header('Content-Type: application/json');

$controller = new ProductController($conn, true);
$details = $controller->fetchProductDetailsByHashId('vnr-ney-001');

echo json_encode($details['variants']);
