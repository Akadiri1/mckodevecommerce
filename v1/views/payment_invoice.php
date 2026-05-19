<?php
$page_title = "Invoice";
$bodyClass  = "page-light-navbar";

// ── Load invoice ──────────────────────────────────────────────
$invId   = htmlspecialchars(trim($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8');
if (empty($invId)) { header("Location: " . ($baseUrl ?? '') . "/"); exit; }

$invRows = selectContent($conn, "invoice", ["invoice_id" => $invId, "status" => "Unpaid"]);
if (empty($invRows)) {
    header("Location: " . ($baseUrl ?? '') . "/purchases?invoice_id=" . urlencode($invId));
    exit;
}
$inv = $invRows[0];

// ── Price breakdown ───────────────────────────────────────────
$subtotalNgn = (float)$inv['subtotal_amount'];
$vatNgn      = (float)$inv['tax_amount'];
$vatPct      = (float)$inv['tax_percentage'];
$discountNgn = (float)$inv['discount_amount'];
$shippingNgn = (float)$inv['shipping_amount'];
$finalNgn    = $subtotalNgn + $vatNgn + $shippingNgn - $discountNgn;

$subtotalUsd = (float)$inv['subtotal_amount2'];
$finalUsd    = (float)$inv['amount_due2'];

$shippingMeta = json_decode($inv['shipping_data'] ?? '{}', true);
$shippingName = !empty($shippingMeta['location_name']) ? ' — ' . $shippingMeta['location_name'] : '';

// ── Address ───────────────────────────────────────────────────
$addrData    = @unserialize($inv['address']);
$fullAddress = 'Not provided';
if ($addrData) {
    $parts = array_filter([
        $addrData['address'] ?? '',
        $addrData['city']    ?? '',
        $addrData['state']   ?? '',
        is_array($addrData['country'] ?? '') ? ($addrData['country']['name'] ?? '') : ($addrData['country'] ?? ''),
        $addrData['zip_code'] ?? '',
    ]);
    $fullAddress = implode(', ', $parts);
}

// ── Settings ──────────────────────────────────────────────────
$siteInfo    = selectContent($conn, "settings_website_info", ["visibility" => "show"]);
$si          = !empty($siteInfo) ? $siteInfo[0] : [];
$bankName    = $si['input_bank_name']           ?? '';
$bankAccNo   = $si['input_bank_account_number'] ?? '';
$bankAccName = $si['input_bank_account_name']   ?? '';
$bankDetails = $si['text_bank_details']         ?? '';
$bankLabel   = $si['input_bank_label']          ?? "I've Paid — Send Evidence via WhatsApp";
$whatsappNo  = preg_replace('/[^0-9]/', '', $si['input_whatsapp_number'] ?? '');

// ── AJAX: bank transfer notify ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');

    $protocol     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $baseHost     = $protocol . $_SERVER['HTTP_HOST'];
    $purchasesUrl = $baseHost . ($baseUrl ?? '') . "/purchases?invoice_id=" . urlencode($invId) . "&transfer_pending=1";

    $msg = "Hello! I've placed an order and made a bank transfer.\n\n"
         . "*Invoice:* {$inv['invoice_id']}\n"
         . "*Name:* {$inv['name']}\n"
         . "*Product(s):* {$inv['title']}\n"
         . "*Subtotal:* ₦" . number_format($subtotalNgn, 2) . "\n";
    if ($vatNgn > 0)      $msg .= "*VAT ({$vatPct}%):* +₦" . number_format($vatNgn, 2) . "\n";
    if ($shippingNgn > 0) $msg .= "*Shipping:* +₦" . number_format($shippingNgn, 2) . "\n";
    if ($discountNgn > 0) $msg .= "*Discount:* -₦" . number_format($discountNgn, 2) . "\n";
    $msg .= "*Total Due:* ₦" . number_format($finalNgn, 2) . "\n\n";
    $msg .= "*Ship to:* $fullAddress\n";
    if (!empty($inv['note'])) $msg .= "*Note:* {$inv['note']}\n";
    $msg .= "\nView order: $purchasesUrl";

    $whatsappUrl = "https://api.whatsapp.com/send?phone={$whatsappNo}&text=" . rawurlencode($msg);

    // Admin email
    if (!empty($site_email_from) && !empty($site_email_password)) {
        try {
            require_once APP_PATH . '/phpm/PHPMailerAutoload.php';
            $mail = new PHPMailer(true);
            $mail->isSMTP(); $mail->Host = $site_email_smtp_host; $mail->SMTPAuth = true;
            $mail->Username = $site_email_from; $mail->Password = $site_email_password;
            $mail->SMTPSecure = $site_email_smtp_secure_type; $mail->Port = (int)$site_email_smtp_port;
            $mail->setFrom($site_email_from, $shop_name);
            $mail->addAddress($site_email_from);
            $mail->isHTML(true);
            $mail->Subject = "Bank Transfer Order — {$inv['invoice_id']}";
            $mail->Body    = "<div style='font-family:sans-serif;max-width:520px;'>"
                           . "<h2 style='color:#072708;'>New Bank Transfer Order</h2>"
                           . "<p><strong>Invoice:</strong> {$inv['invoice_id']}</p>"
                           . "<p><strong>Customer:</strong> {$inv['name']} ({$inv['email']})</p>"
                           . "<p><strong>Total:</strong> ₦" . number_format($finalNgn, 2) . "</p>"
                           . "<p><strong>Ship to:</strong> " . htmlspecialchars($fullAddress, ENT_QUOTES, 'UTF-8') . "</p>"
                           . "<p>Customer is sending evidence via WhatsApp.</p>"
                           . "<p><a href='{$purchasesUrl}'>View order</a></p></div>";
            $mail->send();
        } catch (Exception $e) { error_log("Bank transfer email: " . $e->getMessage()); }
    }

    // Mark pending
    $conn->prepare("UPDATE invoice SET status = 'pending' WHERE invoice_id = ? AND status = 'Unpaid'")
         ->execute([$invId]);

    echo json_encode(['success' => true, 'whatsapp_url' => $whatsappUrl, 'purchases_url' => $purchasesUrl]);
    exit;
}

$useUsd            = $usdEnabled && $subtotalUsd > 0;
$paystackPublicKey = getenv('PAYSTACK_PUBLIC_KEY') ?: '';
$sym               = '₦';

include APP_PATH . "/views/includes/header.php";
?>

<style>
:root {
  --primary-color: #072708;
  --primary-rgb: 7, 39, 8;
  --bg-color: #f9f9f7;
  --text-main: #0c1c0d;
  --text-muted: #5e6b5f;
  --border-color: #e8e8e3;
  --card-bg: #ffffff;
}

body {
  background-color: var(--bg-color) !important;
  color: var(--text-main) !important;
}

/* Hide consultation CTA from footer on invoice page */
.footer-section .cta {
  display: none !important;
}

.inv-card {
  background: var(--card-bg);
  border: 1.5px solid var(--border-color);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 24px;
  box-shadow: 0 4px 20px rgba(7, 39, 8, 0.02);
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.inv-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(7, 39, 8, 0.05);
}

.inv-label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--text-muted);
  margin-bottom: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}

.inv-value {
  font-size: 15px;
  color: var(--text-main);
  font-weight: 600;
  line-height: 1.6;
}

.inv-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.inv-row:last-child {
  margin-bottom: 0;
}

.inv-total {
  font-size: 24px;
  font-weight: 800;
  color: var(--primary-color);
  letter-spacing: -0.5px;
}

.tag-paid {
  background: #e6f4ea;
  color: #137333;
  padding: 6px 16px;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: 1px solid #ceead6;
}

.tag-unpaid {
  background: #fffbeb;
  color: #b45309;
  padding: 6px 16px;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: 1px solid #fef3c7;
}

.btn-pay {
  width: 100%;
  padding: 16px;
  background: var(--primary-color);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(7, 39, 8, 0.15);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
}

.btn-pay:hover {
  background: #0d380e;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(7, 39, 8, 0.25);
}

.btn-wa {
  width: 100%;
  padding: 16px;
  background: #25D366;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.btn-wa:hover {
  background: #20ba56;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37, 211, 102, 0.25);
}

.inv-item {
  display: grid;
  grid-template-columns: 80px 1fr 60px 120px;
  align-items: center;
  gap: 20px;
  padding: 20px 0;
  border-bottom: 1.5px solid var(--border-color);
}

.inv-item:last-child {
  border-bottom: none;
}

.usd-sub {
  color: var(--text-muted);
  font-size: 12px;
  font-weight: 500;
  margin-top: 1px;
}

.print-btn {
  padding: 10px 20px;
  border: 1.5px solid var(--primary-color);
  background: transparent;
  border-radius: 10px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.print-btn:hover {
  background: var(--primary-color);
  color: #fff;
}

@media (max-width: 768px) {
  .inv-grid { grid-template-columns: 1fr !important; }
}
@media print {
  .no-print { display:none !important; }
}
</style>

<div style="height:100px;"></div>
<div style="background:#f9f9f7; min-height:calc(100vh - 100px); padding-bottom:80px;">
<div class="container" style="max-width:860px;margin:0 auto;padding:0 20px;">

<div id="invoice-download-area" style="background:#f9f9f7; padding:10px 0; border-radius:16px;">
  <!-- Header -->
  <div style="padding:40px 0 16px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          <h1 class="heading-02" style="margin:0;">Invoice</h1>
        </div>
        <p class="p-01 color-gray" style="margin:0;font-family:monospace;font-size:14px;letter-spacing:0.5px;">#<?= htmlspecialchars($invId, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div style="display:flex;gap:12px;align-items:center;" class="no-print">
        <?php if ($inv['status'] === 'paid'): ?>
          <span class="tag-paid">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            ✓ Paid
          </span>
        <?php else: ?>
          <span class="tag-unpaid">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Awaiting Payment
          </span>
        <?php endif; ?>
        <button onclick="downloadPDF()" class="print-btn" id="downloadPdfBtn" style="background: var(--primary-color); color: #fff; border: none;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Download PDF
        </button>
        <button onclick="printInvoice()" class="print-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
          Print
        </button>
      </div>
    </div>
  </div>

  <!-- Top info cards -->
  <div class="inv-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;margin-bottom:24px;">
    
    <!-- Customer Info Card -->
    <div class="inv-card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:flex-start; gap:6px;">
      <div class="inv-label" style="margin-bottom:16px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Customer
      </div>
      <div class="inv-value" style="font-size:17px; margin-bottom:12px; font-weight:700; color:var(--primary-color);"><?= htmlspecialchars(ucwords($inv['name']), ENT_QUOTES, 'UTF-8') ?></div>
      
      <div class="p-01 color-gray" style="margin-bottom:8px; display:flex; align-items:center; gap:8px; font-size:14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <span style="word-break: break-all;"><?= htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="p-01 color-gray" style="display:flex; align-items:center; gap:8px; font-size:14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span><?= htmlspecialchars($inv['phonenumber'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    </div>

    <!-- Order Info Card -->
    <div class="inv-card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:flex-start; gap:16px;">
      <div class="inv-label" style="margin-bottom:4px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Order Details
      </div>
      
      <div>
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:3px; font-weight:700;">Order Date</div>
        <div class="inv-value" style="font-size:15px; color:var(--text-main); font-weight:600;"><?= !empty($inv['date_created']) ? date('M j, Y', strtotime($inv['date_created'])) : '' ?></div>
      </div>
      
      <div>
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:3px; font-weight:700;">Payment Method</div>
        <div class="inv-value" style="font-size:15px; color:var(--text-main); font-weight:600;"><?= htmlspecialchars($inv['payment_plan'], ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      
      <div>
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:3px; font-weight:700;">Deliver to</div>
        <div class="inv-value" style="font-size:14px; color:var(--text-main); font-weight:500; line-height:1.5;"><?= htmlspecialchars($fullAddress, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>

    <!-- Price Summary Card -->
    <div class="inv-card" style="margin-bottom:0; border-color:var(--primary-color); background:#fcfcfa; display:flex; flex-direction:column; justify-content:flex-start; gap:14px;">
      <div class="inv-label" style="color:var(--primary-color); margin-bottom:6px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        Price Summary
      </div>
      
      <?php if ($vatNgn > 0 || $shippingNgn > 0 || $discountNgn > 0): ?>
        <div class="inv-row">
          <span class="color-gray p-01" style="font-size:14px; font-weight:500;">Subtotal</span>
          <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px;">
            <span style="font-weight: 600; color: var(--text-main); font-size:15px;">₦<?= number_format($subtotalNgn, 2) ?></span>
            <?php if ($useUsd): ?><span class="usd-sub" style="margin: 0; font-size: 11px;">$<?= number_format($subtotalUsd, 2) ?> USD</span><?php endif; ?>
          </div>
        </div>
        
        <?php if ($vatNgn > 0): ?>
          <div class="inv-row">
            <span class="color-gray p-01" style="font-size:14px; font-weight:500;">VAT (<?= $vatPct ?>%)</span>
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px;">
              <span style="font-weight: 600; color: var(--text-main); font-size:15px;">+₦<?= number_format($vatNgn, 2) ?></span>
              <?php if ($useUsd && $finalUsd > 0): ?>
                <?php $vatUsd = ($vatNgn / $subtotalNgn) * $subtotalUsd; ?>
                <span class="usd-sub" style="margin: 0; font-size: 11px;">+$<?= number_format($vatUsd, 2) ?> USD</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if ($shippingNgn > 0): ?>
          <div class="inv-row">
            <span class="color-gray p-01" style="max-width: 120px; line-height: 1.3; font-size:14px; font-weight:500;">Shipping<?= htmlspecialchars($shippingName, ENT_QUOTES, 'UTF-8') ?></span>
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px;">
              <span style="font-weight: 600; color: var(--text-main); font-size:15px;">+₦<?= number_format($shippingNgn, 2) ?></span>
              <?php if ($useUsd && !empty($shippingMeta['fee_usd'])): ?>
                <span class="usd-sub" style="margin: 0; font-size: 11px;">+$<?= number_format((float)$shippingMeta['fee_usd'], 2) ?> USD</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if ($discountNgn > 0): ?>
          <div class="inv-row">
            <span class="color-gray p-01" style="font-size:14px; font-weight:500;">Discount<?= !empty($inv['applied_coupon_code']) ? ' (' . htmlspecialchars($inv['applied_coupon_code'], ENT_QUOTES, 'UTF-8') . ')' : '' ?></span>
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px;">
              <span style="font-weight: 600; color: #16a34a; font-size:15px;">-₦<?= number_format($discountNgn, 2) ?></span>
              <?php if ($useUsd): ?>
                <?php $discountUsd = ($discountNgn / $subtotalNgn) * $subtotalUsd; ?>
                <span class="usd-sub" style="margin: 0; font-size: 11px; color: #16a34a;">-$<?= number_format($discountUsd, 2) ?> USD</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <div style="border-top:1.5px solid var(--border-color); margin:4px 0;"></div>
      <?php endif; ?>
      
      <div class="inv-row" style="margin-bottom:0; margin-top: auto;">
        <span class="p-02-medium" style="font-weight:700; font-size:15px;">Total Due</span>
        <div style="text-align:right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px;">
          <span class="inv-total" style="font-size:24px;">₦<?= number_format($finalNgn, 2) ?></span>
          <?php if ($useUsd && $finalUsd > 0): ?><span class="usd-sub" style="font-weight:700; font-size:12px; color:var(--primary-color);">$<?= number_format($finalUsd, 2) ?> USD</span><?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Items -->
  <div class="inv-card" style="padding:32px 32px 16px;">
    <h3 class="heading-05" style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
      Ordered Items
    </h3>
    <div style="display:grid;grid-template-columns:80px 1fr 60px 120px;gap:20px;padding-bottom:12px;border-bottom:1.5px solid var(--border-color);margin-bottom:4px;">
      <span class="tagline" style="text-transform:uppercase;letter-spacing:1.5px;color:var(--text-muted);font-weight:700;">Image</span>
      <span class="tagline" style="text-transform:uppercase;letter-spacing:1.5px;color:var(--text-muted);font-weight:700;">Product Description</span>
      <span class="tagline" style="text-transform:uppercase;letter-spacing:1.5px;color:var(--text-muted);font-weight:700;text-align:center;">Qty</span>
      <span class="tagline" style="text-transform:uppercase;letter-spacing:1.5px;color:var(--text-muted);font-weight:700;text-align:right;">Price Total</span>
    </div>
    <?php
    $itemsList = [];
    $customItems = [];
    if (!empty($inv['custom'])) {
        $customItems = json_decode($inv['custom'], true);
    }

    if (is_array($customItems) && !empty($customItems)) {
        foreach ($customItems as $item) {
            $itemsList[] = [
                'title'      => $item['product_name'] . (!empty($item['variant_options']) ? ' (' . $item['variant_options'] . ')' : ''),
                'quantity'   => (int)$item['quantity'],
                'price_ngn'  => (float)$item['price_ngn'] * (int)$item['quantity'],
                'price_ngn2' => (float)$item['price_usd'] * (int)$item['quantity'],
                'image'      => !empty($item['image']) ? $item['image'] : '/assets/img/placeholder.png'
            ];
        }
    } else {
        // Semicolon delimited fallback
        $titles  = array_values(array_filter(explode(';', rtrim($inv['title']    ?? '', ';'))));
        $qtys    = array_values(array_filter(explode(';', rtrim($inv['quantity'] ?? '', ';'))));
        $prices  = array_values(array_filter(explode(';', rtrim($inv['unit_price']  ?? '', ';'))));
        $prices2 = array_values(array_filter(explode(';', rtrim($inv['unit_price2'] ?? '', ';'))));
        foreach ($titles as $i => $title) {
            $itemsList[] = [
                'title'      => $title,
                'quantity'   => (int)($qtys[$i] ?? 1),
                'price_ngn'  => (float)($prices[$i] ?? 0),
                'price_ngn2' => (float)($prices2[$i] ?? 0),
                'image'      => '/assets/img/placeholder.png'
            ];
        }
    }

    foreach ($itemsList as $item):
        $imgSrc = (strpos($item['image'], 'http') === 0) ? $item['image'] : ($baseUrl ?? '') . $item['image'];
    ?>
    <div class="inv-item">
      <div style="width:80px; height:80px; border-radius:12px; overflow:hidden; border:1px solid var(--border-color); background:#fff; display:flex; align-items:center; justify-content:center;">
        <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Product image" style="width:100%; height:100%; object-fit:cover;">
      </div>
      <div>
        <div class="p-02-medium" style="color:var(--text-main);font-size:15px;line-height:1.5;"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div class="p-01" style="text-align:center;font-weight:600;color:var(--text-main);background:#f0f0ec;padding:4px 0;border-radius:6px;max-width:36px;margin:0 auto;"><?= $item['quantity'] ?></div>
      <div style="text-align:right;">
        <span class="p-02-medium" style="font-size:15px;color:var(--text-main);font-weight:700;">₦<?= number_format($item['price_ngn'], 2) ?></span>
        <?php if ($useUsd && $item['price_ngn2'] > 0): ?><span class="usd-sub" style="display:block;">$<?= number_format($item['price_ngn2'], 2) ?></span><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div> <!-- End #invoice-download-area -->

  <!-- Payment action -->
  <div class="inv-card no-print" style="border-left: 5px solid <?= $inv['payment_plan'] === 'Direct Bank Transfer' ? '#25D366' : '#072708' ?>;">
    <h3 class="heading-05" style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M12 14v4M8 18h8"/></svg>
      <?= $inv['payment_plan'] === 'Direct Bank Transfer' ? 'Bank Transfer Verification' : 'Complete Checkout Payment' ?>
    </h3>

    <?php if ($inv['payment_plan'] === 'Direct Bank Transfer'): ?>
      <?php if (!empty($bankDetails)): ?>
        <div style="background:#f4f4f2;border:1px solid #e2e2da;border-radius:12px;padding:24px;margin-bottom:24px;line-height:1.8;font-size:15px;color:#2c3a2e;">
          <?= strip_tags($bankDetails, '<strong><b><br><p>') ?>
        </div>
      <?php elseif (!empty($bankName)): ?>
        <div style="background:#f4f4f2;border:1px solid #e2e2da;border-radius:12px;padding:24px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;">
          <div><span class="color-gray tagline" style="text-transform:uppercase;letter-spacing:1px;font-weight:700;">Beneficiary Bank</span><div class="p-02-medium" style="font-size:16px;margin-top:2px;"><?= htmlspecialchars($bankName, ENT_QUOTES, 'UTF-8') ?></div></div>
          <div><span class="color-gray tagline" style="text-transform:uppercase;letter-spacing:1px;font-weight:700;">Account Number</span><div class="p-02-medium" style="font-size:16px;margin-top:2px;font-family:monospace;letter-spacing:1px;"><?= htmlspecialchars($bankAccNo, ENT_QUOTES, 'UTF-8') ?></div></div>
          <div style="grid-column: span 2;"><span class="color-gray tagline" style="text-transform:uppercase;letter-spacing:1px;font-weight:700;">Account Name</span><div class="p-02-medium" style="font-size:16px;margin-top:2px;"><?= htmlspecialchars($bankAccName, ENT_QUOTES, 'UTF-8') ?></div></div>
        </div>
      <?php endif; ?>

      <div style="background:#eafaf1;border:1px solid #a3f3cc;border-radius:12px;padding:20px;margin-bottom:24px;font-size:14px;color:#0e5a36;">
        <div style="font-weight:700;display:flex;align-items:center;gap:8px;margin-bottom:12px;font-size:15px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          Verification Instruction Steps:
        </div>
        <ol style="margin:0;padding-left:20px;line-height:2.0;font-weight:500;">
          <li>Initiate and transfer <strong style="font-size:15px;text-decoration:underline;">₦<?= number_format($finalNgn, 2) ?></strong> to our official account above.</li>
          <li>Click the button below to open a direct WhatsApp chat window.</li>
          <li>Share your payment confirmation slip / screenshot with our agent.</li>
          <li>Our verification team will confirm receipt and release your order instantly!</li>
        </ol>
      </div>

      <button type="button" id="whatsappBtn" class="btn-wa">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.464 3.488"/></svg>
        <strong><?= htmlspecialchars($bankLabel, ENT_QUOTES, 'UTF-8') ?></strong>
      </button>
      <div id="whatsappMsg" style="display:none;margin-top:16px;font-size:14px;color:#15803d;text-align:center;font-weight:600;"></div>

    <?php elseif (strtolower($inv['payment_plan']) === 'paystack'): ?>
      <div style="text-align:center;padding:12px 0;">
        <p class="p-01 color-gray" style="margin-bottom:24px;font-size:15px;line-height:1.5;">
          🔒 Secure e-payment integration powered by <strong>Paystack</strong>.<br>Accepts Visa, Mastercard, Verve, and direct bank API transfers.
        </p>
        <?php if ($inv['status'] !== 'paid'): ?>
          <button id="paystackBtn" class="btn-pay" style="max-width:420px;margin:0 auto;">
            <div style="display:flex;align-items:center;gap:8px;font-size:17px;font-weight:700;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Pay ₦<?= number_format($finalNgn, 2) ?>
            </div>
            <?php if ($useUsd): ?><span style="font-size:13px;opacity:0.8;display:block;margin-top:2px;">($<?= number_format($finalUsd, 2) ?> USD)</span><?php endif; ?>
          </button>
          <p class="tagline color-gray" style="margin-top:16px;font-size:12px;letter-spacing:0.5px;">
            🛡️ 256-bit SSL encrypted connection
          </p>
        <?php else: ?>
          <div style="display:inline-flex;align-items:center;gap:10px;background:#dcfce7;border:1px solid #b9f6ca;padding:14px 28px;border-radius:12px;color:#15803d;font-weight:700;font-size:16px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            Payment Completed Successfully
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div style="text-align:center;margin-top:16px;">
    <a href="<?= ($baseUrl ?? '') ?>/" class="p-01 color-gray" style="text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Continue Shopping
    </a>
  </div>

</div>
</div>

<?php if (strtolower($inv['payment_plan']) === 'paystack'): ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('paystackBtn');
    if (!btn) return;
    btn.addEventListener('click', function() {
        btn.disabled = true;
        btn.textContent = 'Opening payment gate…';
        var handler = PaystackPop.setup({
            key:      "<?= htmlspecialchars($paystackPublicKey, ENT_QUOTES, 'UTF-8') ?>",
            email:    "<?= htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8') ?>",
            amount:   <?= (int)round($finalNgn * 100) ?>,
            currency: "NGN",
            ref:      "<?= htmlspecialchars($inv['invoice_id'], ENT_QUOTES, 'UTF-8') ?>_<?= time() ?>",
            callback: function(response) {
                btn.textContent = 'Verifying transaction…';
                fetch("<?= ($baseUrl ?? '') ?>/verify-paystack", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ reference: response.reference, invoice_id: "<?= htmlspecialchars($inv['invoice_id'], ENT_QUOTES, 'UTF-8') ?>" })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        btn.style.background = '#16a34a';
                        btn.textContent = '✓ Payment Verified!';
                        setTimeout(function() {
                            window.location.href = "<?= ($baseUrl ?? '') ?>/purchases?invoice_id=<?= urlencode($inv['invoice_id']) ?>&payment_success=true";
                        }, 1500);
                    } else {
                        alert('Verification failed: ' + (data.message || 'Please contact support.'));
                        btn.disabled = false;
                        btn.innerHTML = '<div style="display:flex;align-items:center;gap:8px;font-size:17px;font-weight:700;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Pay ₦<?= number_format($finalNgn, 2) ?></div>';
                    }
                })
                .catch(function() {
                    alert('Connection error. Your payment may have gone through — please contact support with ref: ' + response.reference);
                    btn.disabled = false;
                    btn.innerHTML = '<div style="display:flex;align-items:center;gap:8px;font-size:17px;font-weight:700;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Pay ₦<?= number_format($finalNgn, 2) ?></div>';
                });
            },
            onClose: function() {
                btn.disabled = false;
                btn.innerHTML = '<div style="display:flex;align-items:center;gap:8px;font-size:17px;font-weight:700;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Pay ₦<?= number_format($finalNgn, 2) ?></div>';
            }
        });
        handler.openIframe();
    });
});
</script>
<?php endif; ?>

<?php if ($inv['payment_plan'] === 'Direct Bank Transfer'): ?>
<script>
var waBtn = document.getElementById('whatsappBtn');
var waMsg = document.getElementById('whatsappMsg');
if (waBtn) {
    waBtn.addEventListener('click', function() {
        waBtn.disabled = true;
        waBtn.textContent = 'Processing instruction…';
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
            body: JSON.stringify({ pay: 1 })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                window.open(data.whatsapp_url, '_blank');
                if (waMsg) {
                    waMsg.style.display = 'block';
                    waMsg.innerHTML = '✓ WhatsApp opened in a new tab. Redirecting to your purchases page…';
                }
                waBtn.style.background = '#15803d';
                waBtn.textContent = '✓ WhatsApp Opened';
                setTimeout(function() { window.location.href = data.purchases_url; }, 2500);
            } else {
                waBtn.disabled = false;
                waBtn.innerHTML = '<strong><?= htmlspecialchars($bankLabel, ENT_QUOTES, 'UTF-8') ?></strong>';
                alert('Something went wrong. Please try again.');
            }
        })
        .catch(function() {
            waBtn.disabled = false;
            waBtn.innerHTML = '<strong><?= htmlspecialchars($bankLabel, ENT_QUOTES, 'UTF-8') ?></strong>';
        });
    });
}
</script>
<?php endif; ?>

<script>
function printInvoice() {
    var c = document.querySelector('.container').innerHTML;
    var w = window.open('', '_blank');
    w.document.write('<html><head><title>Invoice</title><style>body{font-family:system-ui,sans-serif;margin:40px;color:#072708;background:#f9f9f7}.no-print{display:none!important}.inv-card{background:#fff;border:1px solid #e8e8e3;border-radius:12px;padding:24px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,0.03)}.inv-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px}.inv-item{display:grid;grid-template-columns:1fr 60px 120px;padding:16px 0;border-bottom:1px solid #f0f0ec}.inv-total{font-size:22px;font-weight:800;color:#072708}.tagline{font-size:11px;text-transform:uppercase;letter-spacing:1px}.color-gray{color:#5e6b5f}</style></head><body>' + c + '<script>window.onload=function(){window.print();window.onafterprint=function(){window.close()}}<\/script></body></html>');
    w.document.close();
}
</script>

<!-- Client-side HTML-to-PDF generation library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    var element = document.getElementById('invoice-download-area');
    var btn = document.getElementById('downloadPdfBtn');
    
    // Indicate loading state
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.innerHTML = 'Generating PDF...';
    
    var opt = {
        margin:       10,
        filename:     'Invoice_<?= htmlspecialchars($invId, ENT_QUOTES, 'UTF-8') ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2.5, 
            useCORS: true,
            ignoreElements: function(el) {
                return el.classList.contains('no-print');
            }
        },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save().then(function() {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.innerHTML = originalHtml;
    }).catch(function(err) {
        console.error(err);
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.innerHTML = originalHtml;
        alert('Could not generate PDF. Please use the Print option to save as PDF.');
    });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
