<?php
define('D_PATH', 'C:/wamp64/www/mckodevecommerce');
const APP_PATH = D_PATH . '/v1';
include D_PATH . '/.env/config.php';
require APP_PATH . '/models/model.php';
require APP_PATH . '/controllers/controller.php';

// First, get all products
$products = selectContent($conn, "panel_products", ["visibility" => "show"]);

if (empty($products)) {
    die("No products found.");
}

// Clear existing variants
$conn->exec("TRUNCATE TABLE `addition_product_variants`");

$sizes = ['Large', 'XLarge'];
$colors = ['Red', 'Blue', 'Green', 'Yellow'];
$order = 1;

foreach ($products as $p) {
    // Insert Size variants
    foreach ($sizes as $idx => $size) {
        insertSafe($conn, "addition_product_variants", [
            'hash_id' => uniqid('var_', true),
            'tb' => 'panel_products',
            'tb_link' => $p['hash_id'],
            'input_name' => 'Size',
            'input_value' => $size,
            'input_price' => $p['input_price'], // Same price or adjust
            'input_stock' => '999',
            'input_sku' => $p['input_sku'] . '-SZ-' . strtoupper(substr($size, 0, 2)),
            'input_order' => $order++,
            'visibility' => 'show',
            'date_created' => date('Y-m-d'),
            'time_created' => date('H:i:s'),
            'created_by' => 'system'
        ]);
    }
    
    // Insert Color variants
    foreach ($colors as $idx => $color) {
        insertSafe($conn, "addition_product_variants", [
            'hash_id' => uniqid('var_', true),
            'tb' => 'panel_products',
            'tb_link' => $p['hash_id'],
            'input_name' => 'Color',
            'input_value' => $color,
            'input_price' => $p['input_price'],
            'input_stock' => '999',
            'input_sku' => $p['input_sku'] . '-CL-' . strtoupper(substr($color, 0, 2)),
            'input_order' => $order++,
            'visibility' => 'show',
            'date_created' => date('Y-m-d'),
            'time_created' => date('H:i:s'),
            'created_by' => 'system'
        ]);
    }
}
echo "Successfully added Size and Color variants for " . count($products) . " products.\n";
