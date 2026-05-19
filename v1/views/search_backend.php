<?php
error_reporting(0);
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$q     = htmlspecialchars(trim($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
$limit = min(12, max(1, (int)($_GET['limit'] ?? 8)));

if (strlen($q) < 1) { echo json_encode(['products' => []]); die; }

try {
    $allProducts = selectContent($conn, "panel_product", ["visibility" => "show"]);
} catch (Exception $e) {
    echo json_encode(['products' => [], 'error' => 'db']); die;
}

// Pre-index variant prices
$_sVars = selectContent($conn, "variants", []);
$_sPriceIdx = [];
foreach ($_sVars as $_v) {
    $h = $_v['product_hash_id'];
    if (!isset($_sPriceIdx[$h])) $_sPriceIdx[$h] = $usdEnabled ? (float)$_v['input_price_usd'] : (float)$_v['input_price_ngn'];
}

$results = array_filter($allProducts, function($p) use ($q) {
    return stripos($p['input_product_name'] ?? '', $q) !== false
        || stripos($p['select_product_category'] ?? '', $q) !== false
        || stripos($p['text_description'] ?? '', $q) !== false;
});

$results = array_slice(array_values($results), 0, $limit);
$output  = array_map(function($p) use ($_sPriceIdx) {
    return [
        'hash_id'     => $p['hash_id'] ?? '',
        'input_title' => $p['input_product_name'] ?? '',
        'input_slug'  => cleans($p['input_product_name'] ?? ''),
        'input_price' => $_sPriceIdx[$p['hash_id']] ?? 0,
        'image_1'     => $p['image_2'] ?? '',
    ];
}, $results);

echo json_encode(['products' => $output]);
