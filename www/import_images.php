<?php
/**
 * Temporary Auto-Importer script for Venora Skincare Product Gallery Images
 */
require_once dirname(__DIR__) . '/.env/config.php';
require_once dirname(__DIR__) . '/v1/models/model.php';

header('Content-Type: application/json');

try {
    // 1. Delete existing images for Venora products to prevent duplication
    $deleteStmt = $conn->prepare("DELETE FROM `images` WHERE `asset_hash_id` LIKE 'vnr-%'");
    $deleteStmt->execute();

    // 2. Define the exact product gallery mapping (matching physically present files under assets/img/products/)
    $galleryData = [
        // 1. Radiance Boost Serum
        'vnr-srs-001' => [
            '/assets/img/products/radiance-serum-1.webp',
            '/assets/img/products/radiance-serum-2.webp'
        ],
        // 2. Anti-Aging Eye Cream
        'vnr-eye-001' => [
            '/assets/img/products/anti-aging-cream-1.webp',
            '/assets/img/products/anti-aging-cream-2.webp'
        ],
        // 3. Refreshing Gel Cleanser
        'vnr-cln-001' => [
            '/assets/img/products/gel-cleanser-1.webp',
            '/assets/img/products/gel-cleanser-2.webp'
        ],
        // 4. Hydrasilk Moisturizer
        'vnr-mos-001' => [
            '/assets/img/products/hydrasilk-1.webp',
            '/assets/img/products/hydrasilk-2.webp',
            '/assets/img/products/hydrasilk-3.webp'
        ],
        // 5. Velvet Night Cream
        'vnr-nit-001' => [
            '/assets/img/products/velvet-cream-1.webp',
            '/assets/img/products/velvet-cream-2.webp'
        ],
        // 6. Luminous Day Cream
        'vnr-day-001' => [
            '/assets/img/products/luminous-day-1.webp',
            '/assets/img/products/luminous-day-2.webp'
        ],
        // 7. Brightening Eye Serum
        'vnr-eye-002' => [
            '/assets/img/products/brightening-serum-1.webp',
            '/assets/img/products/brightening-serum-2.webp'
        ],
        // 8. Gentle Foaming Cleanser
        'vnr-fcl-001' => [
            '/assets/img/products/foaming-cleanser-1.webp',
            '/assets/img/products/foaming-cleanser-2.webp'
        ],
        // 9. Deep Hydration Serum
        'vnr-dhy-001' => [
            '/assets/img/products/deep-hydration-1.webp',
            '/assets/img/products/deep-hydration-2.webp'
        ],
        // 10. Pore Perfect Treatment
        'vnr-por-001' => [
            '/assets/img/products/pore-perfect-1.webp',
            '/assets/img/products/pore-perfect-2.webp'
        ],
        // 11. Hydrating Milk Cleanser
        'vnr-mcl-001' => [
            '/assets/img/products/milk-cleanser-1.webp',
            '/assets/img/products/milk-cleanser-2.webp'
        ],
        // 12. Soothing Night Eye Cream
        'vnr-ney-001' => [
            '/assets/img/products/night-eye-cream-1.webp',
            '/assets/img/products/night-eye-cream-2.webp'
        ],
    ];

    // 3. Prepare the INSERT statement
    $insertStmt = $conn->prepare("
        INSERT INTO `images` (`image_hash_id`, `asset_hash_id`, `image_1`, `date_created`, `time_created`) 
        VALUES (:image_hash_id, :asset_hash_id, :image_1, :date_created, :time_created)
    ");

    $count = 0;
    $date = date('Y-m-d');
    $time = date('H:i:s');

    foreach ($galleryData as $productHashId => $imagesList) {
        foreach ($imagesList as $index => $imagePath) {
            // Generate a clean unique image_hash_id
            $imageHashId = 'img-' . substr($productHashId, 4) . '-' . ($index + 1) . '-' . bin2hex(random_bytes(2));
            
            $insertStmt->execute([
                ':image_hash_id' => $imageHashId,
                ':asset_hash_id' => $productHashId,
                ':image_1'       => $imagePath,
                ':date_created'  => $date,
                ':time_created'  => $time
            ]);
            $count++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Successfully imported {$count} gallery images for Venora skincare products!"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
