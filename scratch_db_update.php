<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

echo "Updating database settings...\n";

try {
    // 1. Update settings_website_info
    $stmt1 = $conn->prepare("UPDATE settings_website_info SET input_usd_toggle = 0, input_name = 'Fundle'");
    $stmt1->execute();
    echo "Updated settings_website_info: input_usd_toggle = 0, input_name = 'Fundle'\n";

    // 2. Update settings_shop_config
    $stmt2 = $conn->prepare("UPDATE settings_shop_config SET input_name = 'Fundle', input_currency_symbol = '₦'");
    $stmt2->execute();
    echo "Updated settings_shop_config: input_name = 'Fundle', input_currency_symbol = '₦'\n";
    
    // 3. Verify
    $stmt = $conn->query("SELECT input_usd_toggle, input_name FROM settings_website_info LIMIT 1");
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Verification - settings_website_info: " . json_encode($row1) . "\n";

    $stmt = $conn->query("SELECT input_name, input_currency_symbol FROM settings_shop_config LIMIT 1");
    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Verification - settings_shop_config: " . json_encode($row2) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
