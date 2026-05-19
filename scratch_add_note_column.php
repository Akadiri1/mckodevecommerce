<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

try {
    $conn->exec("ALTER TABLE invoice ADD COLUMN note TEXT NULL AFTER address");
    echo "SUCCESS: Added 'note' column to invoice table!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
