<?php
$page_title = "Checkout";
$bodyClass  = "page-light-navbar";

// ── Guard: must have items in cart ────────────────────────────
$cartData  = getCartItems();
if (!($cartData['success'] ?? false)) { die("Error loading cart"); }
$cart      = $cartData['items'] ?? $cartData['cart_items'] ?? [];
$cartCount = $cartData['count'] ?? $cartData['cart_count'] ?? 0;
if ($cartCount < 1) { header("Location: $baseUrl/cart"); exit; }

// ── VAT settings ──────────────────────────────────────────────
$vatSettings   = selectContent($conn, "settings_vat_settings", []);
$vatPercentage = !empty($vatSettings) ? (float)$vatSettings[0]['input_vat_percentage'] : 0;
$vatRate       = $vatPercentage / 100;

// ── Shipping locations ────────────────────────────────────────
$controller        = new ProductController($conn, $usdEnabled);
$shippingLocations = $controller->fetchShippingLocations();

// ── Pre-fill from customer session & Fetch Addresses ─────────
$customerData = [];
$savedAddresses = [];
if (!empty($_SESSION['customer_id'])) {
    $cu = selectContent($conn, "read_users", ["id" => (int)$_SESSION['customer_id'], "visibility" => "show"]);
    $customerData = $cu[0] ?? [];
    if (!empty($customerData['hash_id'])) {
        $savedAddresses = selectContent($conn, "read_user_addresses", ["tb_link" => $customerData['hash_id'], "visibility" => "show"]);
    }
}

// ── Totals ────────────────────────────────────────────────────
$totalNgn = (float)$cartData['total_ngn'];
$totalUsd = (float)$cartData['total_usd'];

// ── Form submit logic ─────────────────────────────────────────
$formError = [];
$isAjax = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit']) || $isAjax)) {
    $post = $_POST;
    $required = ['firstname','lastname','email','phone','street-address-1','city','state','country','payment_method','shipping_location'];
    foreach ($required as $field) { if (empty(trim($post[$field] ?? ''))) { $formError[$field] = 'Required'; } }
    
    if (empty($formError)) {
        $titleArr = []; $qtyArr = []; $priceArr = []; $price2Arr = [];
        foreach ($cart as $item) {
            $label = $item['product_name'];
            if (!empty($item['variant_options'])) $label .= ' (' . $item['variant_options'] . ')';
            $titleArr[] = $label; $qtyArr[] = $item['quantity'];
            $priceArr[] = number_format($item['price_ngn'] * $item['quantity'], 2, '.', '');
            $price2Arr[] = number_format($item['price_usd'] * $item['quantity'], 2, '.', '');
        }

        $invId = 'INV_' . strtoupper(substr(md5(uniqid()), 0, 12)) . '_' . time();
        $hashId = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        $custId = $_SESSION['customer_id'] ?? session_id();

        $addressSer = serialize([
            'address'  => $post['street-address-1'],
            'city'     => $post['city'],
            'state'    => $post['state'],
            'country'  => ['name' => htmlspecialchars($post['country'], ENT_QUOTES, 'UTF-8')],
            'zip_code' => $post['zip_code'] ?? null,
        ]);

        $shippingId = (int)$post['shipping_location'];
        $shipRow = $conn->prepare("SELECT * FROM panel_shipping_locations WHERE id = ? LIMIT 1");
        $shipRow->execute([$shippingId]);
        $sD = $shipRow->fetch(PDO::FETCH_ASSOC);
        $shipNgn = $sD ? (float)$sD['input_shipping_fee'] : 0;
        $shipUsd = $sD ? (float)$sD['input_shipping_fee_usd'] : 0;

        $vN = $totalNgn * $vatRate; $vU = $totalUsd * $vatRate;
        $fN = $totalNgn + $vN + $shipNgn; $fU = $totalUsd + $vU + $shipUsd;

        $sql = "INSERT INTO invoice (hash_id, user_id, invoice_id, shipping_id, shipping_amount, shipping_amount2, payment_plan, amount_due, amount_due2, subtotal_amount, subtotal_amount2, tax_amount, tax_amount2, tax_percentage, title, quantity, unit_price, unit_price2, name, email, phonenumber, status, date_created, time_created, custom, currency, address, note, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Unpaid', CURDATE(), CURTIME(), ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $ok = $stmt->execute([
            $hashId, $custId, $invId, $shippingId, $shipNgn, $shipUsd, $post['payment_method'], $fN, $fU, $totalNgn, $totalUsd, $vN, $vU, $vatPercentage, implode(';', $titleArr).';', implode(';', $qtyArr).';', implode(';', $priceArr).';', implode(';', $price2Arr).';', 
            $post['firstname'].' '.$post['lastname'], $post['email'], $post['phone'], json_encode($cart), ($usdEnabled?'USD':'NGN'), $addressSer, $post['note']??'', $custId
        ]);

        if ($ok) {
            $conn->prepare("UPDATE cart SET invoice_id = ? WHERE user_id = ?")->execute([$invId, $custId]);
            if ($isAjax) { echo json_encode(['success' => true, 'redirect' => "$baseUrl/payment-invoice?id=$invId"]); exit; }
            header("Location: $baseUrl/payment-invoice?id=$invId"); exit;
        }
    }
}

$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");
include APP_PATH . "/views/includes/header.php";
?>

<div style="height:100px;background:#f9f9f7;"></div>
<div style="background:#f9f9f7;padding-bottom:80px;min-height:calc(100vh - 100px);">
<div class="container" style="max-width:1100px;margin:0 auto;padding:0 20px;">

  <div style="padding:40px 0 20px;">
    <h1 class="heading-02" style="margin-bottom:4px;">Checkout</h1>
    <p class="p-01 color-gray">Review and complete your order.</p>
  </div>

  <form method="POST" action="" id="checkoutForm">
  <div class="checkout-grid">

    <!-- LEFT: Shipping -->
    <div>
      
      <?php if (!empty($savedAddresses)): ?>
      <div style="background:#f0f3f1;border-radius:12px;padding:20px;margin-bottom:20px;border:1px solid rgba(7,39,8,0.1);">
        <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--primary);">Quick Fill: Use a Saved Address</h4>
        <div style="display:flex;gap:10px;overflow-x:auto;padding-bottom:10px;scrollbar-width:none;">
          <?php foreach ($savedAddresses as $addr): ?>
            <div class="address-pick-card" 
                 onclick="fillAddress(<?= htmlspecialchars(json_encode($addr)) ?>)"
                 style="background:#fff;padding:12px 16px;border-radius:10px;border:1.5px solid #ddd;cursor:pointer;min-width:180px;flex-shrink:0;transition:all 0.2s;">
              <div style="font-weight:700;font-size:13px;margin-bottom:4px;"><?= htmlspecialchars($addr['input_label']) ?></div>
              <div style="font-size:12px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= htmlspecialchars($addr['input_address']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 1px 8px rgba(0,0,0,0.04);">
        <h3 class="heading-05" style="margin-bottom:20px;">Contact & Shipping</h3>
        <div class="checkout-input-grid">
          <input type="text" name="firstname" id="f_first" placeholder="First Name *" value="<?= htmlspecialchars($customerData['input_firstname'] ?? '') ?>" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
          <input type="text" name="lastname" id="f_last" placeholder="Last Name *" value="<?= htmlspecialchars($customerData['input_lastname'] ?? '') ?>" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
        </div>
        <div style="margin-bottom:16px;">
          <input type="email" name="email" id="f_email" placeholder="Email Address *" value="<?= htmlspecialchars($customerData['input_email'] ?? '') ?>" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
        </div>
        <div style="margin-bottom:16px;">
          <input type="tel" name="phone" id="f_phone" placeholder="Phone Number *" value="<?= htmlspecialchars($customerData['input_phone'] ?? '') ?>" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
        </div>
        <div style="margin-bottom:16px;">
          <input type="text" name="street-address-1" id="f_addr" placeholder="Street Address *" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
        </div>
        <div class="checkout-input-grid">
          <input type="text" name="city" id="f_city" placeholder="City *" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
          <input type="text" name="state" id="f_state" placeholder="State / Province *" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
        </div>
        <div class="checkout-input-grid no-margin">
          <input type="text" name="country" id="f_country" placeholder="Country *" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;" required>
          <input type="text" name="zip_code" id="f_zip" placeholder="Postcode" style="width:100%;padding:12px;border:1.5px solid #eee;border-radius:8px;">
        </div>
      </div>

      <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 1px 8px rgba(0,0,0,0.04);">
        <h3 class="heading-05" style="margin-bottom:20px;">Shipping Method</h3>
        <select name="shipping_location" id="shippingSelect" style="width:100%;padding:14px;border:1.5px solid #eee;border-radius:8px;background:#fff;" required onchange="updateTotals()">
          <option value="" disabled selected>Select a shipping location</option>
          <?php foreach ($shippingLocations as $loc): ?>
            <option value="<?= $loc['id'] ?>" data-fee-ngn="<?= $loc['input_shipping_fee'] ?>" data-fee-usd="<?= $loc['input_shipping_fee_usd'] ?>">
              <?= htmlspecialchars($loc['input_location_name']) ?> — <?= formatPrice($loc['input_shipping_fee'], $sym) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 8px rgba(0,0,0,0.04);">
        <h3 class="heading-05" style="margin-bottom:20px;">Payment Method</h3>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <label style="display:flex;align-items:center;gap:12px;padding:16px;border:1px solid #e5e7eb;border-radius:12px;cursor:pointer;">
            <input type="radio" name="payment_method" value="Paystack" checked style="accent-color:var(--primary);">
            <span style="font-weight:600;">Paystack (Card/Bank)</span>
          </label>
          <label style="display:flex;align-items:center;gap:12px;padding:16px;border:1px solid #e5e7eb;border-radius:12px;cursor:pointer;margin-top:12px;">
            <input type="radio" name="payment_method" value="Direct Bank Transfer" style="accent-color:var(--primary);">
            <span style="font-weight:600;">Direct Bank Transfer</span>
          </label>
        </div>
      </div>
    </div>

    <!-- RIGHT: Summary -->
    <div>
      <div style="position:sticky;top:120px;">
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
          <h3 class="heading-05" style="margin-bottom:20px;">Your Order</h3>
          <div style="max-height:300px;overflow-y:auto;margin-bottom:20px;">
            <?php foreach ($cart as $item): ?>
              <div style="display:flex;gap:12px;margin-bottom:12px;">
                <img src="<?= $item['image'] ?>" style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                <div style="flex:1;">
                  <div style="font-size:13px;font-weight:700;"><?= htmlspecialchars($item['product_name']) ?></div>
                  <?php if (!empty($item['variant_options'])): ?>
                    <div style="font-size:11px;color:#888;margin-top:2px;"><?= htmlspecialchars($item['variant_options']) ?></div>
                  <?php endif; ?>
                  <div style="font-size:11px;color:#888;">Qty: <?= $item['quantity'] ?></div>
                </div>
                <div style="font-size:13px;font-weight:700;"><?= formatPrice($usdEnabled ? $item['price_usd']*$item['quantity'] : $item['price_ngn']*$item['quantity'], $sym) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div style="border-top:1px solid #eee;padding-top:16px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px;color:#666;">
              <span>Subtotal</span>
              <span><?= formatPrice($usdEnabled ? $totalUsd : $totalNgn, $sym) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px;color:#666;">
              <span>Shipping</span>
              <span id="shipDisplay">Select location</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-top:12px;margin-top:12px;border-top:2px solid var(--primary);font-size:18px;font-weight:800;color:var(--primary);">
              <span>Total</span>
              <span id="totalDisplay"><?= formatPrice($usdEnabled ? $totalUsd : $totalNgn, $sym) ?></span>
            </div>
          </div>
        </div>
        
        <button type="submit" name="submit" class="place-order-btn" style="width:100%;margin-top:16px;padding:18px;background:var(--primary);color:#fff;border:none;border-radius:12px;font-weight:700;font-size:16px;cursor:pointer;transition:all 0.2s;">
          Place Order
        </button>
      </div>
    </div>

  </div>
  </form>
</div>
</div>

<script>
function fillAddress(addr) {
  document.getElementById('f_first').value = addr.input_firstname;
  document.getElementById('f_last').value = addr.input_lastname;
  document.getElementById('f_phone').value = addr.input_phone;
  document.getElementById('f_addr').value = addr.input_address;
  document.getElementById('f_city').value = addr.input_city;
  document.getElementById('f_state').value = addr.input_state;
  document.getElementById('f_country').value = addr.input_country;
  document.getElementById('f_zip').value = addr.input_postcode || '';
  
  document.querySelectorAll('.address-pick-card').forEach(c => c.style.borderColor = '#ddd');
  event.currentTarget.style.borderColor = 'var(--primary)';
  window.Venora.showToast('Address filled!');
}

function updateTotals() {
  const sel = document.getElementById('shippingSelect');
  const opt = sel.options[sel.selectedIndex];
  const fee = <?= $usdEnabled ? '1' : '0' ?> === 1 ? parseFloat(opt.dataset.feeUsd) : parseFloat(opt.dataset.feeNgn);
  const base = <?= $usdEnabled ? '1' : '0' ?> === 1 ? <?= $totalUsd ?> : <?= $totalNgn ?>;
  const symbol = '<?= $sym ?>';
  
  document.getElementById('shipDisplay').textContent = symbol + fee.toLocaleString(undefined, {minimumFractionDigits: 2});
  document.getElementById('totalDisplay').textContent = symbol + (base + fee).toLocaleString(undefined, {minimumFractionDigits: 2});
}
</script>

<style>
  @media (max-width: 991px) {
    .checkout-grid { grid-template-columns: 1fr; }
  }
  .address-pick-card:hover { border-color: var(--primary) !important; background: #f4f6f4 !important; }
</style>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
