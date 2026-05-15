<?php
error_reporting(0);
// Close any open output buffers so nothing corrupts the JSON
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$q     = htmlspecialchars(trim($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
$limit = min(12, max(1, (int)($_GET['limit'] ?? 8)));

if (strlen($q) < 1) { echo json_encode(['products' => []]); die; }

try {
    $allProducts = selectContent($conn, "panel_products", ["visibility" => "show"]);
} catch (Exception $e) {
    echo json_encode(['products' => [], 'error' => 'db']); die;
}

$results = array_filter($allProducts, function($p) use ($q) {
    return stripos($p['input_title'] ?? '', $q) !== false
        || stripos($p['select_category'] ?? '', $q) !== false
        || stripos($p['text_description'] ?? '', $q) !== false;
});

$results = array_slice(array_values($results), 0, $limit);
$output  = array_map(function($p) {
    return [
        'hash_id'     => $p['hash_id'] ?? '',
        'input_title' => $p['input_title'] ?? '',
        'input_slug'  => cleans($p['input_title'] ?? ''),
        'input_price' => $p['input_price'] ?? '0',
        'image_1'     => $p['image_1'] ?? '',
    ];
}, $results);

echo json_encode(['products' => $output]);
