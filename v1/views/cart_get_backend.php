<?php
header('Content-Type: application/json');

try {
    $cartData = getCartItems();
    
    if (!($cartData['success'] ?? false)) {
        echo json_encode(['success' => false, 'error' => $cartData['error'] ?? 'Unknown error']);
        exit;
    }

    // Clean any previous output buffers to ensure pure JSON
    if (ob_get_level()) ob_clean();
    
    echo json_encode($cartData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
