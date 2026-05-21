<?php
include __DIR__ . "/.env/config.php";
require __DIR__ . "/v1/models/model.php";

echo "=== DB CHECK STATUS ===\n";

// 1. Check settings_website_info
try {
    $stmt = $conn->query("SELECT * FROM settings_website_info LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "settings_website_info:\n";
    if ($row) {
        echo " - input_usd_toggle: " . $row['input_usd_toggle'] . "\n";
        echo " - input_name: " . $row['input_name'] . "\n";
        echo " - image_1: " . $row['image_1'] . "\n";
    } else {
        echo " - NO ROW FOUND\n";
    }
} catch (Exception $e) {
    echo "ERROR settings_website_info: " . $e->getMessage() . "\n";
}

// 2. Check settings_shop_config
try {
    $stmt = $conn->query("SELECT * FROM settings_shop_config LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "settings_shop_config:\n";
    if ($row) {
        echo " - input_name: " . $row['input_name'] . "\n";
        echo " - input_currency_symbol: " . $row['input_currency_symbol'] . "\n";
        echo " - image_1 (logo): " . $row['image_1'] . "\n";
        echo " - image_2 (logo dark): " . $row['image_2'] . "\n";
    } else {
        echo " - NO ROW FOUND\n";
    }
} catch (Exception $e) {
    echo "ERROR settings_shop_config: " . $e->getMessage() . "\n";
}

// 3. Check footer settings
try {
    $stmt = $conn->query("SELECT * FROM settings_shop_footer LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "settings_shop_footer:\n";
    if ($row) {
        foreach ($row as $k => $v) {
            if (strpos($k, 'text') !== false || strpos($k, 'input') !== false) {
                echo " - $k: $v\n";
            }
        }
    } else {
        echo " - NO ROW FOUND\n";
    }
} catch (Exception $e) {
    echo "ERROR settings_shop_footer: " . $e->getMessage() . "\n";
}

// 4. Check products count
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM panel_products");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "panel_products count: " . $row['count'] . "\n";
} catch (Exception $e) {
    echo "ERROR panel_products: " . $e->getMessage() . "\n";
}

// 5. Check panel_slider
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM panel_slider");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "panel_slider count: " . $row['count'] . "\n";
} catch (Exception $e) {
    echo "ERROR panel_slider: " . $e->getMessage() . "\n";
}

// 6. Check columns of table panel_products and panel_slider
try {
    $stmt = $conn->query("DESCRIBE panel_products");
    echo "panel_products columns:\n";
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo " - " . $r['Field'] . " (" . $r['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "ERROR describing panel_products: " . $e->getMessage() . "\n";
}
