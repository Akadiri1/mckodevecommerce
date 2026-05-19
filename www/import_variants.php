<?php
/**
 * Temporary Auto-Importer script for Venora Skincare Product Multiple Variants (Size and Skin Type)
 */
require_once dirname(__DIR__) . '/.env/config.php';
require_once dirname(__DIR__) . '/v1/models/model.php';

header('Content-Type: application/json');

try {
    // 1. Ensure the product options and values exist in the database
    // Option: Size (ID: 1), Skin Type (ID: 2)
    $conn->exec("INSERT IGNORE INTO `product_options` (`id`, `option_name`) VALUES (1, 'Size'), (2, 'Skin Type')");
    // Values: 30ml (1), 50ml (2), Normal (3), Oily (4), Dry (5), Sensitive (6)
    $conn->exec("INSERT IGNORE INTO `product_option_values` (`id`, `option_id`, `value_name`) VALUES 
        (1, 1, '30ml'), 
        (2, 1, '50ml'),
        (3, 2, 'Normal'),
        (4, 2, 'Oily'),
        (5, 2, 'Dry'),
        (6, 2, 'Sensitive')");

    // 2. Wipe existing variants and variant link tables to prevent duplicates
    $conn->exec("TRUNCATE TABLE `variants`");
    $conn->exec("TRUNCATE TABLE `variant_values_link`");

    // 3. Define the products base prices and SKUs
    $productsData = [
        ['hash' => 'vnr-srs-001', 'ngn' => 75000.00,  'usd' => 50.00,  'sku' => 'VNR-SRM-001'],
        ['hash' => 'vnr-eye-001', 'ngn' => 45000.00,  'usd' => 30.00,  'sku' => 'VNR-EYE-001'],
        ['hash' => 'vnr-cln-001', 'ngn' => 45000.00,  'usd' => 30.00,  'sku' => 'VNR-CLN-001'],
        ['hash' => 'vnr-mos-001', 'ngn' => 105000.00, 'usd' => 70.00,  'sku' => 'VNR-MOS-001'],
        ['hash' => 'vnr-nit-001', 'ngn' => 97500.00,  'usd' => 65.00,  'sku' => 'VNR-NIT-001'],
        ['hash' => 'vnr-day-001', 'ngn' => 90000.00,  'usd' => 60.00,  'sku' => 'VNR-DAY-001'],
        ['hash' => 'vnr-eye-002', 'ngn' => 82500.00,  'usd' => 55.00,  'sku' => 'VNR-EYS-001'],
        ['hash' => 'vnr-fcl-001', 'ngn' => 42000.00,  'usd' => 28.00,  'sku' => 'VNR-FCL-001'],
        ['hash' => 'vnr-dhy-001', 'ngn' => 112500.00, 'usd' => 75.00,  'sku' => 'VNR-DHY-001'],
        ['hash' => 'vnr-por-001', 'ngn' => 67500.00,  'usd' => 45.00,  'sku' => 'VNR-POR-001'],
        ['hash' => 'vnr-mcl-001', 'ngn' => 48000.00,  'usd' => 32.00,  'sku' => 'VNR-MCL-001'],
        ['hash' => 'vnr-ney-001', 'ngn' => 60000.00,  'usd' => 40.00,  'sku' => 'VNR-NEY-001'],
    ];

    // 4. Pre-compile the prepared insert statements
    $insertVariant = $conn->prepare("
        INSERT INTO `variants` (`id`, `product_hash_id`, `input_price_ngn`, `input_price_usd`, `input_inventory`, `sku`, `image_1`, `input_weight_in_kg`) 
        VALUES (:id, :product_hash_id, :input_price_ngn, :input_price_usd, :input_inventory, :sku, NULL, :input_weight_in_kg)
    ");

    $insertLink = $conn->prepare("
        INSERT INTO `variant_values_link` (`variant_id`, `value_id`) 
        VALUES (:variant_id, :value_id)
    ");

    $variantId = 1;
    $vCount = 0;
    $lCount = 0;

    foreach ($productsData as $prod) {
        $hash = $prod['hash'];
        $baseNgn = $prod['ngn'];
        $baseUsd = $prod['usd'];
        $baseSku = $prod['sku'];

        // Option Value Mappings for 6 variants per product:
        // Variant 1: Size - 30ml (value_id: 1)
        // Variant 2: Size - 50ml (value_id: 2)
        // Variant 3: Skin Type - Normal (value_id: 3)
        // Variant 4: Skin Type - Oily (value_id: 4)
        // Variant 5: Skin Type - Dry (value_id: 5)
        // Variant 6: Skin Type - Sensitive (value_id: 6)
        
        $variantsConfig = [
            ['val_id' => 1, 'suffix' => 'SZ-30ML',  'price_scale' => 0.8,  'weight' => '0.1'],
            ['val_id' => 2, 'suffix' => 'SZ-50ML',  'price_scale' => 1.0,  'weight' => '0.15'],
            ['val_id' => 3, 'suffix' => 'SK-NORMAL','price_scale' => 1.0,  'weight' => '0.15'],
            ['val_id' => 4, 'suffix' => 'SK-OILY',  'price_scale' => 1.0,  'weight' => '0.15'],
            ['val_id' => 5, 'suffix' => 'SK-DRY',   'price_scale' => 1.0,  'weight' => '0.15'],
            ['val_id' => 6, 'suffix' => 'SK-SENSIT','price_scale' => 1.0,  'weight' => '0.15'],
        ];

        foreach ($variantsConfig as $conf) {
            $priceNgn = round($baseNgn * $conf['price_scale'], -2);
            $priceUsd = round($baseUsd * $conf['price_scale'], 1);

            $insertVariant->execute([
                ':id' => $variantId,
                ':product_hash_id' => $hash,
                ':input_price_ngn' => $priceNgn,
                ':input_price_usd' => $priceUsd,
                ':input_inventory' => 999,
                ':sku' => $baseSku . '-' . $conf['suffix'],
                ':input_weight_in_kg' => $conf['weight']
            ]);
            $vCount++;

            $insertLink->execute([
                ':variant_id' => $variantId,
                ':value_id' => $conf['val_id']
            ]);
            $lCount++;

            $variantId++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Successfully imported {$vCount} variants (Size and Skin Type options) and {$lCount} option value links for the 12 Venora skincare products!"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
