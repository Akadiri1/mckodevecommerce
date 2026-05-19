<?php
$page_title = "Checkout";
$bodyClass  = "page-light-navbar";

// ── Guard: must have items in cart ────────────────────────────
$cartData  = getCartItems();

if (!($cartData['success'] ?? false)) {
    die("Error loading cart: " . ($cartData['error'] ?? 'Unknown error'));
}

$cart      = $cartData['cart_items'] ?? [];
$cartCount = $cartData['cart_count'] ?? 0;

if ($cartCount < 1) {
    header("Location: " . ($baseUrl ?? '') . "/cart");
    exit;
}

// ── VAT settings ──────────────────────────────────────────────
$vatSettings   = selectContent($conn, "settings_vat_settings", []);
$vatPercentage = !empty($vatSettings) ? (float)$vatSettings[0]['input_vat_percentage'] : 0;
$vatRate       = $vatPercentage / 100;

// ── Shipping locations ────────────────────────────────────────
$controller        = new ProductController($conn, $usdEnabled);
$shippingLocations = $controller->fetchShippingLocations();

// ── Coupon / totals ───────────────────────────────────────────
$totalNgn = (float)$cartData['total_ngn'];
$totalUsd = (float)$cartData['total_usd'];

// ── Pre-fill from customer session ───────────────────────────
$customerData = [];
if (!empty($_SESSION['customer_id'])) {
    $cu = selectContent($conn, "read_users", ["id" => (int)$_SESSION['customer_id'], "visibility" => "show"]);
    $customerData = $cu[0] ?? [];
}

// ── Form submit: create invoice ───────────────────────────────
$formError = [];
$isAjax = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit']) || $isAjax)) {
    $post = $_POST;

    // Map country code to full name if selected from dropdown
    $countriesMap = [
        'NG' => 'Nigeria',
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'GH' => 'Ghana',
        'KE' => 'Kenya',
        'ZA' => 'South Africa',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
        'IN' => 'India'
    ];
    if (isset($post['country']) && isset($countriesMap[$post['country']])) {
        $post['country'] = $countriesMap[$post['country']];
    }

    // --- Validate required fields ---
    $required = ['firstname','lastname','email','phone','street-address-1','city','state','country','payment_method','shipping_location'];
    foreach ($required as $field) {
        if (empty(trim($post[$field] ?? ''))) {
            $formError[$field] = 'This field is required.';
        }
    }
    if (!empty($post['email']) && !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
        $formError['email'] = 'Invalid email address.';
    }

    // --- Coupon validation ---
    $amountNgn      = $totalNgn;
    $amountUsd      = $totalUsd;
    $discountNgn    = 0;
    $discountUsd    = 0;
    $appliedCouponId = null;
    $couponCode     = trim($post['coupon_code'] ?? '');

    if (!empty($couponCode)) {
        $mgr = new CouponManager($conn);
        $res = $mgr->validateDual($couponCode, $totalNgn, $totalUsd, $userId ?? 'guest');
        if ($res['success']) {
            $coupon = $res['coupon'];
            $appliedCouponId = $coupon['id'];
            $dual = $mgr->calculateDualFinalPrices($coupon, $totalNgn, $totalUsd);
            $amountNgn = $dual['ngn']['new_total'] ?? $totalNgn;
            $amountUsd = $dual['usd']['new_total'] ?? $totalUsd;
            $discountNgn = $dual['ngn']['discount'] ?? 0;
            $discountUsd = $dual['usd']['discount'] ?? 0;
        } else {
            $formError['coupon_code'] = $res['message'];
        }
    }

    // --- Shipping ---
    $shippingId   = (int)($post['shipping_location'] ?? 0);
    $shipRow      = $conn->prepare("SELECT * FROM panel_shipping_locations WHERE id = ? AND is_active = 1 LIMIT 1");
    $shipRow->execute([$shippingId]);
    $shipData     = $shipRow->fetch(PDO::FETCH_ASSOC);
    $shipNgn      = $shipData ? (float)$shipData['input_shipping_fee']     : 0;
    $shipUsd      = $shipData ? (float)$shipData['input_shipping_fee_usd'] : 0;
    $shipName     = $shipData ? $shipData['input_location_name']           : '';

    // --- VAT ---
    $vatNgn = $amountNgn * $vatRate;
    $vatUsd = $amountUsd * $vatRate;

    // --- Finals ---
    $finalNgn = $amountNgn + $vatNgn + $shipNgn;
    $finalUsd = $amountUsd + $vatUsd + $shipUsd;

    if (empty($formError)) {
        // Build title / qty / unit_price strings
        $titleArr  = [];
        $qtyArr    = [];
        $priceArr  = [];
        $price2Arr = [];

        foreach ($cart as $item) {
            $label     = $item['product_name'];
            if (!empty($item['variant_options'])) $label .= ' (' . $item['variant_options'] . ')';
            $titleArr[]  = $label;
            $qtyArr[]    = $item['quantity'];
            $priceArr[]  = number_format($item['price_ngn'] * $item['quantity'], 2, '.', '');
            $price2Arr[] = number_format($item['price_usd'] * $item['quantity'], 2, '.', '');
        }

        $titleStr  = implode(';', $titleArr) . ';';
        $qtyStr    = implode(';', $qtyArr)   . ';';
        $priceStr  = implode(';', $priceArr) . ';';
        $price2Str = implode(';', $price2Arr). ';';

        $invId   = 'INV_' . strtoupper(substr(md5(uniqid()), 0, 12)) . '_' . time();
        $hashId  = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        $custId  = $userId ?? session_id();

        $addressSer = serialize([
            'address'  => $post['street-address-1'],
            'city'     => $post['city'],
            'state'    => $post['state'],
            'country'  => ['name' => htmlspecialchars($post['country'], ENT_QUOTES, 'UTF-8')],
            'zip_code' => $post['zip_code'] ?? null,
        ]);

        $customJson = json_encode($cart);

        $sql = "INSERT INTO invoice (
            hash_id, user_id, invoice_id,
            shipping_id, shipping_amount, shipping_amount2, shipping_data,
            payment_plan, amount_due, amount_due2,
            subtotal_amount, subtotal_amount2,
            discount_amount, discount_amount2,
            tax_amount, tax_amount2, tax_percentage,
            applied_coupon_code,
            title, quantity, unit_price, unit_price2,
            name, email, phonenumber, status,
            date_created, time_created, custom, currency, address, note, created_by
        ) VALUES (
            :hash_id, :user_id, :invoice_id,
            :shipping_id, :shipping_amount, :shipping_amount2, :shipping_data,
            :payment_plan, :amount_due, :amount_due2,
            :subtotal_amount, :subtotal_amount2,
            :discount_amount, :discount_amount2,
            :tax_amount, :tax_amount2, :tax_percentage,
            :applied_coupon_code,
            :title, :quantity, :unit_price, :unit_price2,
            :name, :email, :phone, 'Unpaid',
            CURDATE(), CURTIME(), :custom, :currency, :address, :note, :created_by
        )";

        try {
            $stmt = $conn->prepare($sql);
            $ok   = $stmt->execute([
                ':hash_id'            => $hashId,
                ':user_id'            => $custId,
                ':invoice_id'         => $invId,
                ':shipping_id'        => $shippingId,
                ':shipping_amount'    => $shipNgn,
                ':shipping_amount2'   => $shipUsd,
                ':shipping_data'      => json_encode(['location_id' => $shippingId, 'location_name' => $shipName, 'fee_ngn' => $shipNgn, 'fee_usd' => $shipUsd]),
                ':payment_plan'       => $post['payment_method'],
                ':amount_due'         => $finalNgn,
                ':amount_due2'        => $finalUsd,
                ':subtotal_amount'    => $totalNgn,
                ':subtotal_amount2'   => $totalUsd,
                ':discount_amount'    => $discountNgn,
                ':discount_amount2'   => $discountUsd,
                ':tax_amount'         => $vatNgn,
                ':tax_amount2'        => $vatUsd,
                ':tax_percentage'     => $vatPercentage,
                ':applied_coupon_code'=> $couponCode ?: null,
                ':title'              => $titleStr,
                ':quantity'           => $qtyStr,
                ':unit_price'         => $priceStr,
                ':unit_price2'        => $price2Str,
                ':name'               => trim($post['firstname'] . ' ' . $post['lastname']),
                ':email'              => $post['email'],
                ':phone'              => $post['phone'],
                ':custom'             => $customJson,
                ':currency'           => $usdEnabled ? 'USD' : 'NGN',
                ':address'            => $addressSer,
                ':note'               => $post['note'] ?? null,
                ':created_by'         => $custId,
            ]);

            if ($ok) {
                // Link cart rows to this invoice
                $conn->prepare("UPDATE cart SET invoice_id = :inv WHERE user_id = :uid")
                     ->execute([':inv' => $invId, ':uid' => $custId]);

                // Record coupon usage
                if ($appliedCouponId) {
                    $mgr = new CouponManager($conn);
                    $mgr->applyCoupon($appliedCouponId, $userId ?? 'guest', $invId);
                }

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'redirect' => ($baseUrl ?? '') . "/payment-invoice?id=" . urlencode($invId)]);
                    exit;
                }

                header("Location: " . ($baseUrl ?? '') . "/payment-invoice?id=" . urlencode($invId));
                exit;
            } else {
                $formError['general'] = 'Could not create invoice. Please try again.';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'errors' => $formError]);
                    exit;
                }
            }
        } catch (PDOException $e) {
            $formError['general'] = 'Database error: ' . $e->getMessage();
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $formError]);
                exit;
            }
        }
    }
    
    if (!empty($formError)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $formError]);
            exit;
        }
    }
}

$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, 'UTF-8');
include APP_PATH . "/views/includes/header.php";
?>

<div style="height:100px;background:#f9f9f7;"></div>
<div style="background:#f9f9f7;padding-bottom:80px;min-height:calc(100vh - 100px);">
<div class="container" style="max-width:1100px;margin:0 auto;padding:0 20px;">

  <div style="padding:40px 0 20px;">
    <h1 class="heading-02" style="margin-bottom:4px;">Checkout</h1>
    <p class="p-01 color-gray">Complete your order below.</p>
  </div>

  <div id="generalErrorBlock" style="display:<?= !empty($formError['general']) ? 'block' : 'none' ?>;background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:6px;margin-bottom:20px;">
    <?= htmlspecialchars($formError['general'] ?? '', ENT_QUOTES, 'UTF-8') ?>
  </div>

  <form method="POST" action="" id="checkoutForm">
  <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;" class="checkout-grid">

    <!-- LEFT: Details -->
    <div>

      <!-- Contact -->
      <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h3 class="heading-05" style="margin-bottom:20px;">Contact Information</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">First name *</label>
            <input type="text" name="firstname" value="<?= htmlspecialchars($_POST['firstname'] ?? $customerData['input_firstname'] ?? '', ENT_QUOTES) ?>"
                   style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['firstname']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
            <?php if (isset($formError['firstname'])): ?><span style="color:#c1121f;font-size:12px;"><?= $formError['firstname'] ?></span><?php endif; ?>
          </div>
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Last name *</label>
            <input type="text" name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? $customerData['input_lastname'] ?? '', ENT_QUOTES) ?>"
                   style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['lastname']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
          </div>
        </div>
        <div style="margin-top:16px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Email address *</label>
          <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $customerData['input_email'] ?? '', ENT_QUOTES) ?>"
                 style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['email']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
          <?php if (isset($formError['email'])): ?><span style="color:#c1121f;font-size:12px;"><?= $formError['email'] ?></span><?php endif; ?>
        </div>
        <div style="margin-top:16px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Phone number *</label>
          <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? $customerData['input_phone'] ?? '', ENT_QUOTES) ?>"
                 style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['phone']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
        </div>
      </div>

      <!-- Shipping Address -->
      <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h3 class="heading-05" style="margin-bottom:20px;">Shipping Address</h3>
        <div style="margin-bottom:16px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Street address *</label>
          <input type="text" name="street-address-1" value="<?= htmlspecialchars($_POST['street-address-1'] ?? $customerData['input_address'] ?? '', ENT_QUOTES) ?>"
                 style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['street-address-1']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">City *</label>
            <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? $customerData['input_city'] ?? '', ENT_QUOTES) ?>"
                   style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['city']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;" required>
          </div>
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">State *</label>
            <div style="position:relative;">
              <select id="stateSelect" name="state"
                      style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['state']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;background:#fff;appearance:none;-webkit-appearance:none;cursor:pointer;" required>
                <option value="" disabled selected>Select State</option>
              </select>
              <div style="position:absolute;top:50%;right:15px;transform:translateY(-50%);pointer-events:none;color:#555;">▼</div>
            </div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Country *</label>
            <div style="position:relative;">
              <select id="countrySelect" name="country" onchange="fetchStatesFunc(this);"
                      style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['country']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;background:#fff;appearance:none;-webkit-appearance:none;cursor:pointer;" required>
                <option value="" disabled <?= empty($_POST['country']) && empty($customerData['input_country']) ? 'selected' : '' ?>>Select Country</option>
                <?php
                $countriesMap = [
                    'NG' => 'Nigeria',
                    'US' => 'United States',
                    'GB' => 'United Kingdom',
                    'CA' => 'Canada',
                    'GH' => 'Ghana',
                    'KE' => 'Kenya',
                    'ZA' => 'South Africa',
                    'AU' => 'Australia',
                    'DE' => 'Germany',
                    'FR' => 'France',
                    'IN' => 'India'
                ];
                $currentCountryVal = $_POST['country'] ?? $customerData['input_country'] ?? '';
                foreach ($countriesMap as $code => $name) {
                    $selected = ($currentCountryVal === $code || strcasecmp($currentCountryVal, $name) === 0) ? 'selected' : '';
                    echo '<option value="' . $code . '" ' . $selected . '>' . htmlspecialchars($name) . '</option>';
                }
                ?>
              </select>
              <div style="position:absolute;top:50%;right:15px;transform:translateY(-50%);pointer-events:none;color:#555;">▼</div>
            </div>
          </div>
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Postal / ZIP code</label>
            <input type="text" name="zip_code" value="<?= htmlspecialchars($_POST['zip_code'] ?? '', ENT_QUOTES) ?>"
                   style="width:100%;padding:11px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;">
          </div>
        </div>
        <div style="margin-top:16px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Order notes (optional)</label>
          <textarea name="note" rows="3"
                    style="width:100%;padding:11px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;resize:vertical;"
                    placeholder="Special instructions for your order…"><?= htmlspecialchars($_POST['note'] ?? '', ENT_QUOTES) ?></textarea>
        </div>
      </div>

      <!-- Shipping Method -->
      <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h3 class="heading-05" style="margin-bottom:20px;">Shipping Method</h3>
        <?php if (!empty($shippingLocations)): ?>
          <div style="position:relative;">
            <select name="shipping_location" id="shippingSelect"
                    style="width:100%;padding:11px 14px;border:1.5px solid <?= isset($formError['shipping_location']) ? '#c1121f' : '#ddd' ?>;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;background:#fff;appearance:none;-webkit-appearance:none;cursor:pointer;" required>
              <option value="" disabled <?= empty($_POST['shipping_location']) && empty($customerData['input_shipping_location']) ? 'selected' : '' ?>>Select shipping method</option>
              <?php
              $currentShippingVal = $_POST['shipping_location'] ?? $customerData['input_shipping_location'] ?? '';
              foreach ($shippingLocations as $loc) {
                  $selected = ($currentShippingVal == $loc['id']) ? 'selected' : '';
                  $priceNgn = "₦" . number_format($loc['input_shipping_fee'], 0);
                  $priceUsd = ($usdEnabled && $loc['input_shipping_fee_usd'] > 0) ? " / $" . number_format($loc['input_shipping_fee_usd'], 2) : "";
                  $deliveryTime = !empty($loc['input_estimated_delivery_time']) ? " (" . $loc['input_estimated_delivery_time'] . ")" : "";
                  $label = htmlspecialchars($loc['input_location_name'], ENT_QUOTES, 'UTF-8') . " - " . $priceNgn . $priceUsd . $deliveryTime;
                  echo '<option value="' . $loc['id'] . '" ' . $selected . ' data-price-ngn="' . (float)$loc['input_shipping_fee'] . '" data-price-usd="' . (float)$loc['input_shipping_fee_usd'] . '">' . $label . '</option>';
              }
              ?>
            </select>
            <div style="position:absolute;top:50%;right:15px;transform:translateY(-50%);pointer-events:none;color:#555;">▼</div>
          </div>
        <?php else: ?>
          <p class="color-gray p-01">No shipping locations available. Please contact us.</p>
          <input type="hidden" name="shipping_location" value="0">
        <?php endif; ?>
        <?php if (isset($formError['shipping_location'])): ?>
          <span style="color:#c1121f;font-size:12px;">Please select a shipping method.</span>
        <?php endif; ?>
      </div>

      <!-- Payment Method -->
      <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h3 class="heading-05" style="margin-bottom:20px;">Payment Method</h3>
        <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid #e8e8e3;border-radius:8px;margin-bottom:10px;cursor:pointer;">
          <input type="radio" name="payment_method" value="Paystack" checked
                 style="accent-color:#072708;width:16px;height:16px;">
          <div>
            <div class="p-02-medium">Pay with Card / Paystack</div>
            <div class="tagline color-gray">Visa, Mastercard, Bank Transfer via Paystack</div>
          </div>
        </label>
        <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid #e8e8e3;border-radius:8px;cursor:pointer;">
          <input type="radio" name="payment_method" value="Direct Bank Transfer"
                 style="accent-color:#072708;width:16px;height:16px;">
          <div>
            <div class="p-02-medium">Direct Bank Transfer</div>
            <div class="tagline color-gray">Make payment to our bank account, then send evidence via WhatsApp</div>
          </div>
        </label>
      </div>

    </div>

    <!-- RIGHT: Order summary -->
    <div>
      <div style="position:sticky;top:120px;">
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(7,39,8,0.06);margin-bottom:16px;">
          <h3 class="heading-05" style="margin-bottom:16px;">Order Summary</h3>

          <!-- Items -->
          <?php foreach ($cart as $item): ?>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f0f0ec;">
              <div style="display:flex;gap:12px;flex:1;padding-right:12px;">
                <img src="<?= htmlspecialchars($item['image'] ?? '/assets/img/icons/cart.svg', ENT_QUOTES, 'UTF-8') ?>" 
                     style="width:50px;height:50px;object-fit:cover;border-radius:6px;background:#f9f9f7;border:1px solid #e8e8e3;" 
                     alt="<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>">
                <div>
                  <div class="p-02-medium"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php if (!empty($item['variant_options'])): ?>
                    <div class="tagline color-gray"><?= htmlspecialchars($item['variant_options'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                  <div class="tagline color-gray">Qty: <?= (int)$item['quantity'] ?></div>
                </div>
              </div>
              <div class="p-02-medium" style="white-space:nowrap;">
                <?php if ($usdEnabled): ?>
                  $<?= number_format($item['price_usd'] * $item['quantity'], 2) ?>
                <?php else: ?>
                  ₦<?= number_format($item['price_ngn'] * $item['quantity'], 0) ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Coupon code -->
          <div style="margin-top:12px;">
            <div style="display:flex;gap:8px;">
              <input type="text" name="coupon_code" id="couponInput"
                     value="<?= htmlspecialchars($_POST['coupon_code'] ?? '', ENT_QUOTES) ?>"
                     placeholder="Coupon code"
                     style="flex:1;padding:10px 12px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:14px;">
              <button type="button" id="applyCouponBtn"
                      style="padding:10px 16px;background:#f0f0ec;border:none;border-radius:7px;font-family:inherit;font-size:14px;cursor:pointer;transition:background 0.2s;"
                      onmouseover="this.style.background='#e2e2de'" onmouseout="this.style.background='#f0f0ec'">
                Apply
              </button>
            </div>
            <div id="couponFeedback" style="font-size:13px;margin-top:6px;<?= isset($formError['coupon_code']) ? '' : 'display:none;' ?>color:#c1121f;">
              <?= isset($formError['coupon_code']) ? '✗ ' . htmlspecialchars($formError['coupon_code'], ENT_QUOTES, 'UTF-8') : '' ?>
            </div>
          </div>

          <!-- Totals -->
          <div style="margin-top:20px;padding-top:16px;border-top:1px solid #e8e8e3;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span class="p-01 color-gray">Subtotal</span>
              <span class="p-01" id="subtotalDisplay">
                <?= $usdEnabled ? '$' . number_format($totalUsd, 2) : '₦' . number_format($totalNgn, 0) ?>
              </span>
            </div>
            
            <!-- Discount Row -->
            <div id="discountRow" style="display:none;justify-content:space-between;margin-bottom:8px;">
              <span class="p-01" style="color:#2a9d8f;font-weight:500;">Discount</span>
              <span class="p-01" id="discountDisplay" style="color:#2a9d8f;font-weight:500;"></span>
            </div>

            <?php if ($vatPercentage > 0): ?>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span class="p-01 color-gray">VAT (<?= $vatPercentage ?>%)</span>
              <span class="p-01" id="vatDisplay">
                <?= $usdEnabled ? '+$' . number_format($totalUsd * $vatRate, 2) : '+₦' . number_format($totalNgn * $vatRate, 0) ?>
              </span>
            </div>
            <?php endif; ?>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span class="p-01 color-gray">Shipping</span>
              <span class="p-01 color-gray" id="shippingDisplay">Select method</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #e8e8e3;margin-top:8px;">
              <span class="heading-06">Total</span>
              <span class="heading-06" id="totalDisplay">
                <?= $usdEnabled ? '$' . number_format($totalUsd * (1 + $vatRate), 2) : '₦' . number_format($totalNgn * (1 + $vatRate), 0) ?>
              </span>
            </div>
          </div>
        </div>

        <button type="submit" id="placeOrderBtn" name="submit"
                style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:16px;background:#072708;color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:16px;font-weight:600;cursor:pointer;transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
          <span id="placeOrderSpinner" style="display:none;width:18px;height:18px;border:2.5px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;"></span>
          <span id="placeOrderBtnText">Place Order</span>
        </button>
        <p class="tagline color-gray" style="text-align:center;margin-top:12px;">
          You'll be directed to the invoice page to complete payment.
        </p>
      </div>
    </div>

  </div>
  </form>
</div>
</div>

<style>
@media (max-width: 768px) {
  .checkout-grid { grid-template-columns: 1fr !important; }
}
.footer-section .cta { display: none !important; }
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>

<script>
// Update shipping display dynamically
var shippingSelect = document.getElementById('shippingSelect');
if (shippingSelect) {
  shippingSelect.addEventListener('change', function() {
    recalculateTotals();
  });
}

// AJAX Helper to post requests
function ajaxPost(url, data, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        callback(null, xhr.responseText);
      } else {
        callback(new Error("Status: " + xhr.status));
      }
    }
  };
  var params = [];
  for (var key in data) {
    if (data.hasOwnProperty(key)) {
      params.push(encodeURIComponent(key) + "=" + encodeURIComponent(data[key]));
    }
  }
  xhr.send(params.join("&"));
}

function fetchStatesFunc(e) {
  var countryVal = e.value;
  var stateSelect = document.getElementById("stateSelect");
  var currentSelectedState = <?= json_encode($_POST['state'] ?? $customerData['input_state'] ?? '') ?>;

  stateSelect.innerHTML = "<option>Loading states...</option>";
  stateSelect.disabled = true;

  ajaxPost("<?= ($baseUrl ?? '') ?>/fetch-state-backend", { countryVal: countryVal }, function(err, res) {
    stateSelect.disabled = false;
    if (err) {
      console.error(err);
      stateSelect.innerHTML = "<option value='' disabled selected>Select State</option>";
      return;
    }
    try {
      var response = JSON.parse(res);
      var states = response.success || [];
      stateSelect.innerHTML = "<option value='' disabled>Select State</option>";
      states.forEach(function(state) {
        var selected = (currentSelectedState === state.state) ? "selected" : "";
        stateSelect.innerHTML += "<option value='" + state.state + "' " + selected + ">" + state.state + "</option>";
      });
    } catch(err2) {
      console.error(err2);
      stateSelect.innerHTML = "<option value='' disabled selected>Select State</option>";
    }
  });
}

// Auto-run if a country is already selected
var initialCountry = document.getElementById("countrySelect");
if (initialCountry && initialCountry.value) {
  fetchStatesFunc(initialCountry);
}

// --- Coupon AJAX Logic ---
var couponDiscountNgn = 0;
var couponDiscountUsd = 0;

function recalculateTotals() {
  var totalNgn = parseFloat(<?= json_encode($totalNgn) ?>);
  var totalUsd = parseFloat(<?= json_encode($totalUsd) ?>);
  var vatPercentage = parseFloat(<?= json_encode($vatPercentage) ?>);
  var vatRate = vatPercentage / 100;
  var usdEnabled = <?= json_encode($usdEnabled) ?>;

  // 1. Get Shipping Selection
  var shippingSelect = document.getElementById('shippingSelect');
  var shipNgn = 0;
  var shipUsd = 0;
  var shippingText = 'Select method';
  
  if (shippingSelect && shippingSelect.selectedIndex >= 0) {
    var selectedOpt = shippingSelect.options[shippingSelect.selectedIndex];
    if (selectedOpt && selectedOpt.value) {
      shipNgn = parseFloat(selectedOpt.getAttribute('data-price-ngn') || 0);
      shipUsd = parseFloat(selectedOpt.getAttribute('data-price-usd') || 0);
      if (usdEnabled) {
        shippingText = '$' + shipUsd.toFixed(2);
      } else {
        shippingText = '₦' + shipNgn.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
      }
    }
  }

  // 2. Calculations
  var amountNgn = totalNgn - couponDiscountNgn;
  if (amountNgn < 0) amountNgn = 0;
  
  var amountUsd = totalUsd - couponDiscountUsd;
  if (amountUsd < 0) amountUsd = 0;

  var vatNgn = amountNgn * vatRate;
  var vatUsd = amountUsd * vatRate;

  var finalNgn = amountNgn + vatNgn + shipNgn;
  var finalUsd = amountUsd + vatUsd + shipUsd;

  // 3. Update DOM
  var discountRow = document.getElementById('discountRow');
  var discountDisplay = document.getElementById('discountDisplay');
  var vatDisplay = document.getElementById('vatDisplay');
  var shippingDisplay = document.getElementById('shippingDisplay');
  var totalDisplay = document.getElementById('totalDisplay');

  // Discount
  if (couponDiscountNgn > 0 || couponDiscountUsd > 0) {
    discountRow.style.display = 'flex';
    if (usdEnabled) {
      discountDisplay.textContent = '-$' + couponDiscountUsd.toFixed(2);
    } else {
      discountDisplay.textContent = '-₦' + couponDiscountNgn.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
  } else {
    discountRow.style.display = 'none';
  }

  // VAT
  if (vatDisplay) {
    if (usdEnabled) {
      vatDisplay.textContent = '+$' + vatUsd.toFixed(2);
    } else {
      vatDisplay.textContent = '+₦' + vatNgn.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
  }

  // Shipping
  if (shippingDisplay) {
    shippingDisplay.textContent = shippingText;
  }

  // Total Display
  if (totalDisplay) {
    if (usdEnabled) {
      totalDisplay.textContent = '$' + finalUsd.toFixed(2);
    } else {
      totalDisplay.textContent = '₦' + finalNgn.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
  }
}

// AJAX fetch for coupon verification
document.getElementById('applyCouponBtn').addEventListener('click', function() {
  var codeInput = document.getElementById('couponInput');
  var code = codeInput.value.trim();
  var feedback = document.getElementById('couponFeedback');
  var btn = this;

  feedback.style.display = 'none';
  feedback.textContent = '';

  if (!code) {
    feedback.style.display = 'block';
    feedback.style.color = '#c1121f';
    feedback.textContent = '✗ Please enter a coupon code.';
    return;
  }

  btn.disabled = true;
  var originalText = btn.textContent;
  btn.textContent = 'Applying...';

  // Request JSON verification to /validate-coupon
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "<?= ($baseUrl ?? '') ?>/validate-coupon", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      btn.disabled = false;
      btn.textContent = originalText;
      
      if (xhr.status === 200) {
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            feedback.style.display = 'block';
            feedback.style.color = '#2a9d8f';
            feedback.textContent = '✓ ' + res.message;
            
            // Set global discounts from response
            couponDiscountNgn = parseFloat(res.ngn.discount || 0);
            couponDiscountUsd = parseFloat(res.usd ? (res.usd.discount || 0) : 0);
            
            recalculateTotals();
          } else {
            feedback.style.display = 'block';
            feedback.style.color = '#c1121f';
            feedback.textContent = '✗ ' + res.message;
            
            couponDiscountNgn = 0;
            couponDiscountUsd = 0;
            recalculateTotals();
          }
        } catch (e) {
          console.error(e);
          feedback.style.display = 'block';
          feedback.style.color = '#c1121f';
          feedback.textContent = '✗ Error parsing coupon response.';
        }
      } else {
        feedback.style.display = 'block';
        feedback.style.color = '#c1121f';
        feedback.textContent = '✗ Failed to validate coupon. Status: ' + xhr.status;
      }
    }
  };
  
  xhr.send(JSON.stringify({ code: code }));
});

// Init totals on page load
recalculateTotals();

// Function to show field errors
function showFieldError(fieldName, message) {
  var inputEl = document.querySelector('[name="' + fieldName + '"]');
  if (inputEl) {
    inputEl.style.borderColor = '#c1121f';
    
    // Find or create error element
    var errorEl = inputEl.parentNode.querySelector('.field-error-msg');
    if (!errorEl) {
      errorEl = document.createElement('span');
      errorEl.className = 'field-error-msg';
      errorEl.style.color = '#c1121f';
      errorEl.style.fontSize = '12px';
      errorEl.style.marginTop = '4px';
      errorEl.style.display = 'block';
      inputEl.parentNode.appendChild(errorEl);
    }
    errorEl.textContent = message;
  }
}

// Function to clear all field errors
function clearAllErrors() {
  document.querySelectorAll('.field-error-msg').forEach(function(el) {
    el.textContent = '';
  });
  document.querySelectorAll('input, select').forEach(function(el) {
    el.style.borderColor = '#ddd';
  });
  var generalError = document.getElementById('generalErrorBlock');
  if (generalError) {
    generalError.style.display = 'none';
    generalError.textContent = '';
  }
}

// AJAX Form Submission
var checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
  checkoutForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    clearAllErrors();
    
    var btn = document.getElementById('placeOrderBtn');
    var spinner = document.getElementById('placeOrderSpinner');
    var btnText = document.getElementById('placeOrderBtnText');
    
    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';
    if (btnText) btnText.textContent = 'Processing...';
    
    var formData = new FormData(checkoutForm);
    formData.append('ajax', '1');
    formData.append('submit', '1');
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", window.location.href, true);
    xhr.setRequestHeader("Accept", "application/json");
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          try {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
              window.location.href = res.redirect;
            } else {
              // Enable button
              if (btn) btn.disabled = false;
              if (spinner) spinner.style.display = 'none';
              if (btnText) btnText.textContent = 'Place Order';
              
              if (res.errors) {
                for (var field in res.errors) {
                  if (res.errors.hasOwnProperty(field)) {
                    if (field === 'general') {
                      var genErr = document.getElementById('generalErrorBlock');
                      if (genErr) {
                        genErr.style.display = 'block';
                        genErr.textContent = res.errors[field];
                        window.scrollTo({ top: genErr.offsetTop - 120, behavior: 'smooth' });
                      }
                    } else {
                      showFieldError(field, res.errors[field]);
                    }
                  }
                }
              }
            }
          } catch(e) {
            console.error(e);
            alertError("Error parsing checkout response. Please try again.");
          }
        } else {
          alertError("Failed to submit checkout. Status: " + xhr.status);
        }
      }
    };
    
    xhr.send(formData);
  });
}

function alertError(msg) {
  var btn = document.getElementById('placeOrderBtn');
  var spinner = document.getElementById('placeOrderSpinner');
  var btnText = document.getElementById('placeOrderBtnText');
  if (btn) btn.disabled = false;
  if (spinner) spinner.style.display = 'none';
  if (btnText) btnText.textContent = 'Place Order';
  
  var genErr = document.getElementById('generalErrorBlock');
  if (genErr) {
    genErr.style.display = 'block';
    genErr.textContent = msg;
    window.scrollTo({ top: genErr.offsetTop - 120, behavior: 'smooth' });
  }
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
