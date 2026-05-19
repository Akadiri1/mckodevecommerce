<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$reference  = trim($data['reference']  ?? '');
$invoice_id = trim($data['invoice_id'] ?? '');

if (empty($reference) || empty($invoice_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing reference or invoice_id']);
    exit;
}

// ── 1. Verify with Paystack API ───────────────────────────────
$paystackSecret = getenv('PAYSTACK_SECRET_KEY') ?: '';
if (empty($paystackSecret)) {
    error_log("PAYSTACK_SECRET_KEY not set");
    echo json_encode(['success' => false, 'message' => 'Payment gateway not configured']);
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_HTTPHEADER     => ["Authorization: Bearer $paystackSecret", "Cache-Control: no-cache"],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);
$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if (!$response) {
    error_log("Paystack verify cURL error: $curlError");
    echo json_encode(['success' => false, 'message' => 'Cannot reach payment gateway']);
    exit;
}

$result = json_decode($response, true);
if (!isset($result['data']['status']) || $result['data']['status'] !== 'success') {
    echo json_encode(['success' => false, 'message' => 'Payment not confirmed by Paystack']);
    exit;
}

// ── 2. Update invoice + inventory (atomic) ────────────────────
try {
    $conn->beginTransaction();

    // Lock invoice row
    $lockStmt = $conn->prepare("SELECT * FROM invoice WHERE invoice_id = :id FOR UPDATE");
    $lockStmt->execute([':id' => $invoice_id]);
    $invoice  = $lockStmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        throw new Exception('Invoice not found');
    }

    if ($invoice['status'] === 'paid') {
        $conn->rollBack();
        echo json_encode(['success' => true, 'message' => 'Already paid']);
        exit;
    }

    // Mark paid
    $conn->prepare(
        "UPDATE invoice SET status = 'paid', paid_at = NOW(), paystack_ref = :ref WHERE invoice_id = :id"
    )->execute([':ref' => $reference, ':id' => $invoice_id]);

    // Finalise coupon usage
    $couponUsageRows = selectContent($conn, "ecommerce_coupon_usage", ["invoice_id" => $invoice_id]);
    if (!empty($couponUsageRows)) {
        $conn->prepare("UPDATE ecommerce_coupon_usage SET status = 'consumed' WHERE invoice_id = ?")
             ->execute([$invoice_id]);
    }

    // Reduce inventory from variants
    $customData = json_decode($invoice['custom'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $customData = @unserialize($invoice['custom']) ?: [];
    }

    foreach ((array)$customData as $item) {
        $qty = (int)($item['quantity'] ?? 0);
        if ($qty <= 0) continue;

        // Try variant_id directly (new cart system)
        $variantId = $item['variant_id'] ?? null;

        if (empty($variantId) && !empty($item['product_id'])) {
            // Resolve product hash → variant id
            $hashStmt = $conn->prepare("SELECT hash_id FROM panel_product WHERE id = :pid LIMIT 1");
            $hashStmt->execute([':pid' => $item['product_id']]);
            $productHash = $hashStmt->fetchColumn();
            if ($productHash) {
                $varStmt = $conn->prepare("SELECT id FROM variants WHERE product_hash_id = :h LIMIT 1");
                $varStmt->execute([':h' => $productHash]);
                $variantId = $varStmt->fetchColumn();
            }
        }

        // Also try by product_hash_id if available
        if (empty($variantId) && !empty($item['product_id'])) {
            $varStmt = $conn->prepare("SELECT id FROM variants WHERE product_hash_id = :h LIMIT 1");
            $varStmt->execute([':h' => $item['product_id']]);
            $variantId = $varStmt->fetchColumn();
        }

        if ($variantId) {
            $conn->prepare(
                "UPDATE variants SET input_inventory = GREATEST(0, input_inventory - :qty) WHERE id = :vid"
            )->execute([':qty' => $qty, ':vid' => $variantId]);
        }
    }

    // Clear cart (delete rows linked to this invoice)
    $conn->prepare("DELETE FROM cart WHERE invoice_id = :id")->execute([':id' => $invoice_id]);

    $conn->commit();

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Paystack verify DB error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}

// ── 3. Send confirmation emails ───────────────────────────────
if (!empty($site_email_from) && !empty($site_email_password)) {
    try {
        require_once APP_PATH . '/phpm/PHPMailerAutoload.php';

        // --- Customer email ---
        $mail = new PHPMailer(true);
        $mail->isSMTP(); $mail->Host = $site_email_smtp_host; $mail->SMTPAuth = true;
        $mail->Username = $site_email_from; $mail->Password = $site_email_password;
        $mail->SMTPSecure = $site_email_smtp_secure_type; $mail->Port = (int)$site_email_smtp_port;
        $mail->setFrom($site_email_from, $shop_name);
        $mail->addAddress($invoice['email'], $invoice['name']);
        $mail->isHTML(true);
        $mail->Subject = "Payment Confirmed — Order #{$invoice_id}";

        $custBody = "
        <div style='font-family:sans-serif;max-width:560px;margin:0 auto;'>
          <div style='background:#072708;padding:24px;text-align:center;'>
            <h1 style='color:#fff;margin:0;font-size:22px;letter-spacing:3px;'>" . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') . "</h1>
          </div>
          <div style='padding:28px;'>
            <h2 style='color:#072708;'>Payment Confirmed!</h2>
            <p>Hi " . htmlspecialchars($invoice['name'], ENT_QUOTES, 'UTF-8') . ", thank you for your order.</p>
            <table style='width:100%;border-collapse:collapse;font-size:14px;margin:20px 0;'>
              <tr style='background:#f9f9f7;'>
                <td style='padding:8px 12px;font-weight:600;'>Invoice</td>
                <td style='padding:8px 12px;'>#" . htmlspecialchars($invoice_id, ENT_QUOTES, 'UTF-8') . "</td>
              </tr>
              <tr>
                <td style='padding:8px 12px;font-weight:600;'>Amount Paid</td>
                <td style='padding:8px 12px;'>₦" . number_format((float)$invoice['amount_due'], 2) . "</td>
              </tr>
              <tr style='background:#f9f9f7;'>
                <td style='padding:8px 12px;font-weight:600;'>Payment Ref</td>
                <td style='padding:8px 12px;'>" . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . "</td>
              </tr>
            </table>
            <p>We'll process and ship your order shortly.</p>
          </div>
          <div style='padding:16px 28px;border-top:1px solid #eee;text-align:center;'>
            <p style='color:#aaa;font-size:12px;margin:0;'>&copy; " . date('Y') . " " . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') . "</p>
          </div>
        </div>";
        $mail->Body = $custBody;
        $mail->send();
    } catch (Exception $e) {
        error_log("Customer confirmation email error: " . $e->getMessage());
    }

    try {
        // --- Admin email ---
        $adminMail = new PHPMailer(true);
        $adminMail->isSMTP(); $adminMail->Host = $site_email_smtp_host; $adminMail->SMTPAuth = true;
        $adminMail->Username = $site_email_from; $adminMail->Password = $site_email_password;
        $adminMail->SMTPSecure = $site_email_smtp_secure_type; $adminMail->Port = (int)$site_email_smtp_port;
        $adminMail->setFrom($site_email_from, $shop_name);
        $adminMail->addAddress($site_email_from);
        $adminMail->isHTML(true);
        $adminMail->Subject = "New Order Paid: #{$invoice_id}";
        $adminMail->Body    = "
        <h3>New Paid Order</h3>
        <ul>
          <li><strong>Invoice:</strong> {$invoice_id}</li>
          <li><strong>Customer:</strong> {$invoice['name']}</li>
          <li><strong>Email:</strong> {$invoice['email']}</li>
          <li><strong>Phone:</strong> {$invoice['phonenumber']}</li>
          <li><strong>Amount:</strong> ₦" . number_format((float)$invoice['amount_due'], 2) . "</li>
          <li><strong>Ref:</strong> {$reference}</li>
        </ul>";
        $adminMail->send();
    } catch (Exception $e) {
        error_log("Admin order email error: " . $e->getMessage());
    }
}

echo json_encode(['success' => true, 'message' => 'Payment verified and order confirmed.']);
