<?php
require_once __DIR__ . "/v1/models/model.php";

try {
    echo "Starting collation fix...\n";
    
    // 1. Alter the table default charset and collation
    $conn->exec("ALTER TABLE `cart` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Table 'cart' converted to utf8mb4_unicode_ci.\n";
    
    // 2. Ensure specific columns are definitely using the correct collation
    $conn->exec("ALTER TABLE `cart` MODIFY `user_id` CHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("ALTER TABLE `cart` MODIFY `product_id` VARCHAR(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("ALTER TABLE `cart` MODIFY `variant_id` CHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "Columns modified successfully.\n";
    echo "Collation fix complete.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
