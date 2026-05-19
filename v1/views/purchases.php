<?php
$page_title = "Order Details";
$bodyClass  = "page-light-navbar";

$invId           = htmlspecialchars(trim($_GET['invoice_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$paymentSuccess  = !empty($_GET['payment_success']);
$transferPending = !empty($_GET['transfer_pending']);

if (empty($invId)) { header("Location: " . ($baseUrl ?? '') . "/"); exit; }

$invRows = selectContent($conn, "invoice", ["invoice_id" => $invId]);
if (empty($invRows)) { include APP_PATH . "/views/404.php"; die; }
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
$useUsd      = $usdEnabled && $subtotalUsd > 0;

// ── Address ───────────────────────────────────────────────────
$addrData    = @unserialize($inv['address']);
$fullAddress = 'Not provided';
if ($addrData) {
    $parts = array_filter([
        $addrData['address']  ?? '',
        $addrData['city']     ?? '',
        $addrData['state']    ?? '',
        is_array($addrData['country'] ?? '') ? ($addrData['country']['name'] ?? '') : ($addrData['country'] ?? ''),
        $addrData['zip_code'] ?? '',
    ]);
    $fullAddress = implode(', ', $parts);
}

// ── Status badge ──────────────────────────────────────────────
$statusLower = strtolower($inv['status'] ?? 'unpaid');
$statusMap   = [
    'paid'       => ['#dcfce7','#15803d','Paid'],
    'pending'    => ['#fef9c3','#854d0e','Pending'],
    'unpaid'     => ['#fef9c3','#854d0e','Awaiting Payment'],
    'processing' => ['#dbeafe','#1d4ed8','Processing'],
    'shipped'    => ['#ede9fe','#6d28d9','Shipped'],
    'delivered'  => ['#dcfce7','#15803d','Delivered'],
    'cancelled'  => ['#fee2e2','#b91c1c','Cancelled'],
];
[$sBg, $sColor, $sLabel] = $statusMap[$statusLower] ?? ['#f3f4f6','#374151', ucfirst($statusLower)];

include APP_PATH . "/views/includes/header.php";
?>

<style>
.ord-card  { background:#fff; border-radius:12px; padding:28px; margin-bottom:20px; box-shadow:0 1px 8px rgba(7,39,8,0.06); }
.ord-label { font-size:11px; text-transform:uppercase; letter-spacing:1.5px; color:#b5b5b5; margin-bottom:6px; font-weight:600; }
.ord-item  { display:flex; justify-content:space-between; align-items:flex-start; padding:14px 0; border-bottom:1px solid #f0f0ec; }
.ord-item:last-child { border-bottom:none; }
.usd-sub   { color:#b5b5b5; font-size:12px; display:block; margin-top:2px; }
.step-dot  { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
.step-done { background:#dcfce7; color:#15803d; }
.step-curr { background:#072708; color:#fff; }
.step-todo { background:#f0f0ec; color:#b5b5b5; }
@media (max-width:640px) { .ord-cols { grid-template-columns:1fr !important; } }
</style>

<div style="height:100px;"></div>
<div style="background:#f9f9f7;min-height:calc(100vh - 100px);padding-bottom:80px;">
<div class="container" style="max-width:760px;margin:0 auto;padding:0 20px;">

  <!-- Page heading -->
  <div style="padding:40px 0 8px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 class="heading-02" style="margin-bottom:4px;">Order Confirmation</h1>
      <p class="p-01 color-gray">#<?= htmlspecialchars($invId, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <span style="background:<?= $sBg ?>;color:<?= $sColor ?>;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;letter-spacing:0.5px;">
      <?= htmlspecialchars($sLabel, ENT_QUOTES, 'UTF-8') ?>
    </span>
  </div>

  <!-- Banners -->
  <?php if ($transferPending): ?>
  <div style="background:#fef9c3;border:1px solid #fde68a;color:#854d0e;padding:16px 20px;border-radius:10px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px;">
    <svg style="flex-shrink:0;margin-top:2px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <div>
      <div style="font-weight:600;margin-bottom:4px;">Awaiting payment confirmation</div>
      <div style="font-size:14px;line-height:1.6;">Your order is placed and we're waiting to confirm your bank transfer. Once confirmed, your status will update to <strong>Paid</strong>. Check WhatsApp for updates from us.</div>
    </div>
  </div>
  <?php elseif ($paymentSuccess): ?>
  <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:16px 20px;border-radius:10px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px;">
    <svg style="flex-shrink:0;margin-top:2px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <div>
      <div style="font-weight:600;margin-bottom:4px;">Payment confirmed — thank you!</div>
      <div style="font-size:14px;">A confirmation email has been sent to <strong><?= htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8') ?></strong>. We'll process and ship your order shortly.</div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Order status tracker -->
  <div class="ord-card">
    <div class="ord-label" style="margin-bottom:20px;">Order Progress</div>
    <?php
    $steps = [
        ['label' => 'Order Placed',    'done' => true],
        ['label' => 'Payment Confirmed','done' => in_array($statusLower, ['paid','processing','shipped','delivered'])],
        ['label' => 'Processing',      'done' => in_array($statusLower, ['processing','shipped','delivered'])],
        ['label' => 'Shipped',         'done' => in_array($statusLower, ['shipped','delivered'])],
        ['label' => 'Delivered',       'done' => $statusLower === 'delivered'],
    ];
    $currentStep = 0;
    foreach ($steps as $i => $s) { if ($s['done']) $currentStep = $i; }
    ?>
    <div style="display:flex;align-items:center;gap:0;">
      <?php foreach ($steps as $i => $step): ?>
        <div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:0;">
          <div class="step-dot <?= $step['done'] ? 'step-done' : ($i === $currentStep + 1 ? 'step-curr' : 'step-todo') ?>">
            <?= $step['done'] ? '✓' : ($i + 1) ?>
          </div>
          <div style="font-size:11px;margin-top:6px;text-align:center;color:<?= $step['done'] ? '#072708' : '#b5b5b5' ?>;font-weight:<?= $step['done'] ? '600' : '400' ?>;">
            <?= htmlspecialchars($step['label'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>
        <?php if ($i < count($steps) - 1): ?>
          <div style="height:2px;flex:1;background:<?= $step['done'] ? '#072708' : '#e8e8e3' ?>;margin-bottom:22px;"></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Customer + order info -->
  <div class="ord-card">
    <div class="ord-cols" style="display:grid;grid-template-columns:1fr 1fr;gap:28px;">
      <div>
        <div class="ord-label">Customer</div>
        <div class="p-02-medium"><?= htmlspecialchars(ucwords($inv['name']), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="p-01 color-gray"><?= htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="p-01 color-gray"><?= htmlspecialchars($inv['phonenumber'], ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div>
        <div class="ord-label">Shipping Address</div>
        <div class="p-01"><?= htmlspecialchars($fullAddress, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div>
        <div class="ord-label">Order Date</div>
        <div class="p-01"><?= !empty($inv['date_created']) ? date('F j, Y', strtotime($inv['date_created'])) : '' ?></div>
      </div>
      <div>
        <div class="ord-label">Payment Method</div>
        <div class="p-01"><?= htmlspecialchars($inv['payment_plan'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php if (!empty($inv['paystack_ref'])): ?>
          <div class="tagline color-gray" style="margin-top:4px;">Ref: <?= htmlspecialchars($inv['paystack_ref'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Items -->
  <div class="ord-card">
    <h3 class="heading-05" style="margin-bottom:20px;">Items Ordered</h3>
    <?php
    $titles = array_values(array_filter(explode(';', rtrim($inv['title']    ?? '', ';'))));
    $qtys   = array_values(array_filter(explode(';', rtrim($inv['quantity'] ?? '', ';'))));
    $prices = array_values(array_filter(explode(';', rtrim($inv['unit_price'] ?? '', ';'))));
    foreach ($titles as $i => $title):
        $q = (int)($qtys[$i] ?? 1);
        $p = (float)($prices[$i] ?? 0);
    ?>
    <div class="ord-item">
      <div>
        <div class="p-02-medium"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="tagline color-gray">Qty: <?= $q ?></div>
      </div>
      <div class="p-02-medium" style="white-space:nowrap;">₦<?= number_format($p, 2) ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Payment summary -->
  <div class="ord-card">
    <h3 class="heading-05" style="margin-bottom:20px;">Payment Summary</h3>

    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
      <span class="p-01 color-gray">Subtotal</span>
      <span class="p-01">₦<?= number_format($subtotalNgn, 2) ?><?= $useUsd ? ' <span style="color:#b5b5b5;font-size:12px;">/ $' . number_format($subtotalUsd, 2) . '</span>' : '' ?></span>
    </div>
    <?php if ($vatNgn > 0): ?>
    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
      <span class="p-01 color-gray">VAT (<?= $vatPct ?>%)</span>
      <span class="p-01">+₦<?= number_format($vatNgn, 2) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($shippingNgn > 0): ?>
    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
      <span class="p-01 color-gray">Shipping</span>
      <span class="p-01">+₦<?= number_format($shippingNgn, 2) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($discountNgn > 0): ?>
    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
      <span class="p-01 color-gray">Discount<?= !empty($inv['applied_coupon_code']) ? ' (' . htmlspecialchars($inv['applied_coupon_code'], ENT_QUOTES, 'UTF-8') . ')' : '' ?></span>
      <span class="p-01" style="color:#16a34a;">-₦<?= number_format($discountNgn, 2) ?></span>
    </div>
    <?php endif; ?>

    <div style="display:flex;justify-content:space-between;padding-top:14px;border-top:1px solid #e8e8e3;">
      <span class="heading-06">Total<?= $statusLower === 'paid' ? ' Paid' : ' Due' ?></span>
      <div style="text-align:right;">
        <span class="heading-06">₦<?= number_format($finalNgn, 2) ?></span>
        <?php if ($useUsd && $finalUsd > 0): ?>
          <span class="usd-sub">$<?= number_format($finalUsd, 2) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($statusLower === 'unpaid' && $inv['payment_plan'] === 'Paystack'): ?>
    <div style="margin-top:20px;text-align:center;">
      <a href="<?= ($baseUrl ?? '') ?>/payment-invoice?id=<?= urlencode($invId) ?>"
         style="display:inline-block;padding:14px 32px;background:#072708;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:15px;">
        Complete Payment
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Actions -->
  <div style="text-align:center;display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
    <a href="<?= ($baseUrl ?? '') ?>/products"
       style="padding:12px 24px;border:1.5px solid #072708;color:#072708;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;">
      Continue Shopping
    </a>
    <?php if ($statusLower === 'unpaid' || $statusLower === 'pending'): ?>
    <a href="<?= ($baseUrl ?? '') ?>/payment-invoice?id=<?= urlencode($invId) ?>"
       style="padding:12px 24px;background:#072708;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;">
      View Invoice
    </a>
    <?php endif; ?>
  </div>

</div>
</div>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
