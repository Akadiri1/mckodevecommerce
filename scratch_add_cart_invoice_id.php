<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

try {
    $conn->exec("ALTER TABLE cart ADD COLUMN invoice_id VARCHAR(225) DEFAULT NULL");
    echo "SUCCESS: Added 'invoice_id' column to cart table!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
