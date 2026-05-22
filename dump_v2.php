<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

header('Content-Type: text/plain');

try {
    echo "--- Variants ---\n";
    $stmt = $conn->query("SELECT * FROM variants LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { print_r($row); }

    echo "\n--- Values Link ---\n";
    $stmt = $conn->query("SELECT * FROM variant_values_link LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { print_r($row); }
    
} catch (Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }
