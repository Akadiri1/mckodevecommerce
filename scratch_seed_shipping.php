<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

try {
    $conn->exec("TRUNCATE TABLE panel_shipping_locations");
    
    $stmt = $conn->prepare("INSERT INTO `panel_shipping_locations` (`id`, `hash_id`, `input_location_name`, `input_shipping_fee`, `input_shipping_fee_usd`, `input_estimated_delivery_time`, `is_active`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $locations = [
        [1, 'shiploc_698d62e3a8204', 'Lagos (Island)', 500.00, 1.00, '3-5 days', 1],
        [2, 'shiploc_698d62e3a8206', 'Lagos (Mainland)', 300.00, 0.50, '4-6 days', 1],
        [3, 'shiploc_698d62e3a8207', 'Abuja', 700.00, 1.50, '5-7 days', 1],
        [4, 'shiploc_698d62e3a8208', 'Port Harcourt', 600.00, 1.20, '4-6 days', 1]
    ];
    
    foreach ($locations as $loc) {
        $stmt->execute($loc);
    }
    
    echo "SUCCESS: Seeded 4 shipping locations!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
