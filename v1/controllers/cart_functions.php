<?php
/**
 * Cart and invoice helper functions ported from demo16.
 * Uses the `cart`, `variants`, and `panel_product` tables.
 */

function formatPrice($amount, $symbol = null) {
    global $shop_symbol;
    $s = $symbol ?? $shop_symbol ?? '₦';
    return $s . number_format((float)$amount, 2, '.', ',');
}

function jsonResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function updateInvoice($invoice_id, $status) {
    global $conn;
    updateContent($conn, "invoice", ["status" => $status], ["invoice_id" => $invoice_id]);
}

function handlePaystackWebhook($data) {
    global $conn;
    $event = $data['event'] ?? null;
    if ($event === 'charge.success') {
        $reference   = $data['data']['reference'] ?? null;
        $invoice_info = selectContent($conn, 'invoice', ['invoice_id' => $reference]);
        $invoice_info = $invoice_info[0] ?? null;
        if ($invoice_info) {
            if ($invoice_info['amount_due'] * 100 == $data['data']['amount']) {
                updateInvoice($reference, 'paid');
            } else {
                error_log("Paystack webhook amount mismatch for invoice: {$reference}");
            }
        }
    }
}

/**
 * Robust User ID resolver for cart logic.
 */
function getCartUserId() {
    if (!empty($_SESSION['customer_id'])) return (string)$_SESSION['customer_id'];
    if (!empty($_SESSION['user_id'])) return (string)$_SESSION['user_id'];
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['user_id'] = session_id();
    return $_SESSION['user_id'];
}

/**
 * Returns cart items with full product and variant details for the current user.
 */
function getCartItems() {
    global $conn;
    $userId = getCartUserId();

    try {
        $stmt = $conn->prepare("
            SELECT
                c.id AS cart_id,
                c.hash_id AS cart_hash_id,
                c.product_id,
                c.variant_id,
                c.quantity,
                p.id AS product_db_id,
                p.input_product_name,
                p.image_2 AS product_image
            FROM cart c
            LEFT JOIN panel_product p ON (c.product_id COLLATE utf8mb4_unicode_ci) = p.hash_id
            WHERE (c.user_id COLLATE utf8mb4_unicode_ci) = :user_id
            ORDER BY c.date_created DESC, c.time_created DESC
        ");
        $stmt->execute([':user_id' => $userId]);

        $totalNgn = 0;
        $totalUsd = 0;
        $totalQty = 0;
        $cartItems = [];

        foreach ($rows as $row) {
            $item = [
                'cart_id'        => (int) $row['cart_id'],
                'product_id'     => $row['product_id'],
                'product_name'   => $row['input_product_name'] ?? 'Unknown Product',
                'quantity'       => (int) $row['quantity'],
                'image'          => fixImagePath($row['product_image'] ?? '/assets/img/icons/cart.svg'),
                'variant_id'     => $row['variant_id'],
                'price_ngn'      => 0,
                'price_usd'      => 0,
                'inventory'      => 999,
            ];
            $totalQty += $item['quantity'];
            // 1. Resolve all selected variant IDs (comma separated)
            $vIds = array_filter(explode(',', $row['variant_id'] ?? ''));
            $optionsArray = [];
            
            if (!empty($vIds)) {
                $placeholders = implode(',', array_fill(0, count($vIds), '?'));
                $vStmt = $conn->prepare("
                    SELECT v.input_price_ngn, v.input_price_usd, v.input_inventory, v.image_1, po.option_name, pov.value_name
                    FROM variants v
                    LEFT JOIN variant_values_link vvl ON v.id = vvl.variant_id
                    LEFT JOIN product_option_values pov ON vvl.value_id = pov.id
                    LEFT JOIN product_options po ON pov.option_id = po.id
                    WHERE v.id IN ($placeholders)
                ");
                $vStmt->execute($vIds);

                $first = true;
                while ($vRow = $vStmt->fetch(PDO::FETCH_ASSOC)) {
                    $item['price_ngn'] += (float)$vRow['input_price_ngn'];
                    $item['price_usd'] += (float)$vRow['input_price_usd'];
                    $item['inventory'] = min($item['inventory'], (int)$vRow['input_inventory']);
                    if (!empty($vRow['image_1']) && $first) {
                        $item['image'] = fixImagePath($vRow['image_1']);
                        $first = false;
                    }
                    if (!empty($vRow['value_name'])) {
                        $label = !empty($vRow['option_name']) ? $vRow['option_name'] . ': ' : '';
                        $optionsArray[] = $label . $vRow['value_name']; 
                    }
                }
                } else {
                // Base price fallback if no variants selected
                $fbStmt = $conn->prepare("SELECT input_price_ngn, input_price_usd FROM variants WHERE (product_hash_id COLLATE utf8mb4_unicode_ci) = ? LIMIT 1");
                $fbStmt->execute([$row['product_id']]);
                $fb = $fbStmt->fetch(PDO::FETCH_ASSOC);
                if ($fb) {
                    $item['price_ngn'] = (float)$fb['input_price_ngn'];
                    $item['price_usd'] = (float)$fb['input_price_usd'];
                }
                }

                $item['variant_options']    = !empty($optionsArray) ? implode(', ', $optionsArray) : '';            $item['total_ngn']          = $item['price_ngn'] * $item['quantity'];
            $item['total_usd']          = $item['price_usd'] * $item['quantity'];
            $item['formatted_price']    = formatPrice($item['price_ngn'] ?: $item['price_usd']);

            $cartItems[] = $item;
            $totalNgn   += $item['total_ngn'];
            $totalUsd   += $item['total_usd'];
        }

        return [
            'success'    => true,
            'items'      => $cartItems,
            'cart_items' => $cartItems, // compatibility
            'total_ngn'  => round($totalNgn, 2),
            'total_usd'  => round($totalUsd, 2),
            'subtotal'   => round($totalNgn ?: $totalUsd, 2),
            'count'      => count($cartItems),
            'cart_count' => count($cartItems),
            'total_quantity' => $totalQty
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage(), 'items' => []];
    }
}

function getCartCount($userId = null) {
    global $conn;
    $userId = $userId ?? getCartUserId();
    try {
        $stmt = $conn->prepare("SELECT SUM(quantity) AS count FROM cart WHERE (user_id COLLATE utf8mb4_unicode_ci) = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);
    } catch (PDOException $e) { return 0; }
}

function addToCart($data) {
    global $conn;
    $productHashId = $data['hash_id'] ?? $data['product_id'] ?? null;
    $variantId     = $data['variant_id'] ?? null; 
    $quantity      = (int) ($data['quantity'] ?? 1);
    $userId        = getCartUserId();

    if (!$productHashId || $quantity < 1) jsonResponse(['success' => false, 'error' => 'Invalid request']);

    try {
        $conn->beginTransaction();

        // 1. Resolve Variant ID if empty (fallback to first available)
        if (empty($variantId)) {
            $stmt = $conn->prepare("SELECT id FROM variants WHERE (product_hash_id COLLATE utf8mb4_unicode_ci) = ? LIMIT 1");
            $stmt->execute([$productHashId]);
            $variantId = $stmt->fetchColumn() ?: null;
        }

        if (!$variantId) jsonResponse(['success' => false, 'error' => 'Product variant not found']);

        // 2. Check existing using NULL-safe equality and collation fix
        $checkStmt = $conn->prepare("SELECT id, quantity FROM cart WHERE (user_id COLLATE utf8mb4_unicode_ci) = ? AND (product_id COLLATE utf8mb4_unicode_ci) = ? AND (variant_id COLLATE utf8mb4_unicode_ci) <=> ? LIMIT 1");
        $checkStmt->execute([$userId, $productHashId, $variantId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?")->execute([$quantity, $existing['id']]);
        } else {
            $conn->prepare("INSERT INTO cart (hash_id, user_id, product_id, variant_id, quantity, date_created, time_created) VALUES (?,?,?,?,?,CURDATE(),CURTIME())")
                 ->execute(['cart_'.bin2hex(random_bytes(8)), $userId, $productHashId, $variantId, $quantity]);
        }

        $count = getCartCount($userId);
        $conn->commit();
        jsonResponse(['success' => true, 'cart_count' => $count]);
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        jsonResponse(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateCartQuantity($data) {
    global $conn;
    $cartId = $data['cart_id'] ?? null;
    $qty    = (int)($data['quantity'] ?? 0);
    $userId = getCartUserId();
    if (!$cartId || $qty < 0) jsonResponse(['success' => false, 'error' => 'Invalid data']);
    
    $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND (user_id COLLATE utf8mb4_unicode_ci) = ?")->execute([$qty, $cartId, $userId]);
    if ($qty === 0) $conn->prepare("DELETE FROM cart WHERE id = ? AND (user_id COLLATE utf8mb4_unicode_ci) = ?")->execute([$cartId, $userId]);
    
    jsonResponse(['success' => true, 'cart_count' => getCartCount($userId)]);
}

function removeCartItem($data) {
    global $conn;
    $cartId = $data['cart_id'] ?? null;
    $userId = getCartUserId();
    $conn->prepare("DELETE FROM cart WHERE id = ? AND (user_id COLLATE utf8mb4_unicode_ci) = ?")->execute([$cartId, $userId]);
    jsonResponse(['success' => true, 'cart_count' => getCartCount($userId)]);
}
