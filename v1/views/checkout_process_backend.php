<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); die; }

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$hs = fn($v) => htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');

$firstName = $hs($data['first_name'] ?? '');
$lastName  = $hs($data['last_name']  ?? '');
$email     = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = $hs($data['phone']     ?? '');
$addr1     = $hs($data['address_1'] ?? '');
$city      = $hs($data['city']      ?? '');
$postal    = $hs($data['postal_code']?? '');
$country   = $hs($data['country']   ?? '');
$notes     = $hs($data['notes']     ?? '');
$shipping  = $hs($data['shipping_method'] ?? 'standard');

if (empty($firstName) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($addr1)) {
    echo json_encode(['success'=>false,'error'=>'Required fields missing']); die;
}

// Get cart
$sessionId = session_id();
$cartRows  = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);
if (empty($cartRows)) { echo json_encode(['success'=>false,'error'=>'Cart is empty']); die; }

// Calculate totals
$subtotal = 0;
$orderItems = [];
foreach ($cartRows as $row) {
    $p = selectContent($conn, "panel_products", ["hash_id" => $row['input_product_id'], "visibility" => "show"]);
    if (empty($p)) continue;
    $p     = $p[0];
    $price = (float)$p['input_price'];
    $qty   = (int)$row['input_quantity'];
    $lineTotal = $price * $qty;
    $subtotal += $lineTotal;
    $orderItems[] = [
        'product_id' => $p['hash_id'],
        'title'      => $p['input_title'],
        'variant'    => $row['input_variant'] ?? '',
        'qty'        => $qty,
        'price'      => $price,
        'total'      => $lineTotal,
        'image'      => $p['image_1'] ?? '',
    ];
}

$shipRate  = $shipping === 'express' ? 12.99 : (float)($shopConfig[0]['input_shipping_rate'] ?? 0);
$freeShip  = (float)($shopConfig[0]['input_free_shipping'] ?? 0);
if ($freeShip > 0 && $subtotal >= $freeShip) $shipRate = 0;
$tax       = $subtotal * ((float)($shopConfig[0]['input_tax_rate'] ?? 0) / 100);
$total     = $subtotal + $shipRate + $tax;

// Create order
$orderHash = 'ORD' . strtoupper(substr(uniqid(), -8));
insertSafe($conn, "read_orders", [
    'hash_id'          => $orderHash,
    'input_first_name' => $firstName,
    'input_last_name'  => $lastName,
    'input_email'      => $email,
    'input_phone'      => $phone,
    'text_address'     => "$addr1\n" . ($data['address_2'] ?? '') . "\n$city, " . ($data['state'] ?? '') . " $postal\n$country",
    'input_status'     => 'paid',
    'input_subtotal'   => number_format($subtotal, 2, '.', ''),
    'input_shipping'   => number_format($shipRate, 2, '.', ''),
    'input_tax'        => number_format($tax, 2, '.', ''),
    'input_total'      => number_format($total, 2, '.', ''),
    'input_payment_method' => 'card',
    'text_notes'       => $notes,
    'visibility'       => 'show',
    'date_created'     => date('Y-m-d'),
    'time_created'     => date('H:i:s'),
    'created_by'       => $sessionId,
]);

// Create order items
foreach ($orderItems as $item) {
    insertSafe($conn, "read_order_items", [
        'hash_id'          => uniqid('oi_', true),
        'tb'               => 'read_orders',
        'tb_link'          => $orderHash,
        'input_product_id' => $item['product_id'],
        'input_title'      => $item['title'],
        'input_variant'    => $item['variant'],
        'input_quantity'   => $item['qty'],
        'input_price'      => number_format($item['price'], 2, '.', ''),
        'input_total'      => number_format($item['total'], 2, '.', ''),
        'image_1'          => $item['image'],
        'visibility'       => 'show',
        'date_created'     => date('Y-m-d'),
        'time_created'     => date('H:i:s'),
        'created_by'       => $sessionId,
    ]);
}

// Clear cart
foreach ($cartRows as $row) {
    updateContent($conn, "read_cart", ["visibility" => "hide"], ["hash_id" => $row['hash_id']]);
}

// Send confirmation email
if (!empty($site_email_from) && !empty($site_email_password)) {
    try {
        require APP_PATH . '/phpm/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host       = $site_email_smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $site_email_from;
        $mail->Password   = $site_email_password;
        $mail->SMTPSecure = $site_email_smtp_secure_type;
        $mail->Port       = (int)$site_email_smtp_port;
        $mail->setFrom($site_email_from, $shop_name);
        $mail->addAddress($email, "$firstName $lastName");
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmed — #$orderHash | " . $shop_name;
        $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $siteBase  = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($baseUrl ?? '');
        $mail->Body = "<div style='font-family:Inter,Arial,sans-serif;max-width:600px;margin:0 auto;padding:32px;'>"
            . "<h2 style='color:#072708;'>Order Confirmed!</h2>"
            . "<p>Hi $firstName, your order <strong>#$orderHash</strong> has been placed. Total: <strong>" . htmlspecialchars($shop_symbol, ENT_QUOTES, 'UTF-8') . number_format($total, 2) . "</strong></p>"
            . "<a href='$siteBase/orders/$orderHash' style='display:inline-block;padding:12px 28px;background:#072708;color:white;text-decoration:none;border-radius:4px;font-weight:600;'>View Order</a>"
            . "</div>";
        $mail->send();
    } catch (Exception $e) { /* silent */ }
}

echo json_encode(['success' => true, 'order_id' => $orderHash]);
