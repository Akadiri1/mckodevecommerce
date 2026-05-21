<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

header('Content-Type: text/plain');

try {
    $stmt = $conn->query("DESCRIBE website_status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
