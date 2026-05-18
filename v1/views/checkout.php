<?php
$page_title = "Checkout";
$bodyClass  = "page-light-navbar";

$sessionId = session_id();
$cartRows  = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);

if (empty($cartRows)) { header("Location: " . ($baseUrl ?? '') . "/cart"); exit; }

// ── Checkout labels (ADMC-editable via settings_shop_checkout_labels) ──────────
try {
    $checkoutSett = selectContent($conn, "settings_shop_checkout_labels", ["visibility" => "show"]);
    $checkoutSett = !empty($checkoutSett) ? $checkoutSett[0] : [];
} catch (Exception $e) { $checkoutSett = []; }
$csId           = $checkoutSett['id'] ?? '';
$csPageTitle    = htmlspecialchars($checkoutSett['input_page_heading']          ?? '', ENT_QUOTES, 'UTF-8');
$csContact      = htmlspecialchars($checkoutSett['input_contact_block_heading'] ?? '', ENT_QUOTES, 'UTF-8');
$csShipAddr     = htmlspecialchars($checkoutSett['input_address_block_heading'] ?? '', ENT_QUOTES, 'UTF-8');
$csShipMethod   = htmlspecialchars($checkoutSett['input_shipping_block_heading']?? '', ENT_QUOTES, 'UTF-8');
$csPayment      = htmlspecialchars($checkoutSett['input_payment_block_heading'] ?? '', ENT_QUOTES, 'UTF-8');
$csPlaceOrder   = htmlspecialchars($checkoutSett['input_place_order_btn']       ?? '', ENT_QUOTES, 'UTF-8');
$csSummaryTitle = htmlspecialchars($checkoutSett['input_summary_title']         ?? '', ENT_QUOTES, 'UTF-8');
$csStdName      = htmlspecialchars($checkoutSett['input_standard_name']         ?? '', ENT_QUOTES, 'UTF-8');
$csStdTime      = htmlspecialchars($checkoutSett['input_standard_time']         ?? '', ENT_QUOTES, 'UTF-8');
$csExpName      = htmlspecialchars($checkoutSett['input_express_name']          ?? '', ENT_QUOTES, 'UTF-8');
$csExpTime      = htmlspecialchars($checkoutSett['input_express_time']          ?? '', ENT_QUOTES, 'UTF-8');
$csExpPrice     = (float)($checkoutSett['input_express_price'] ?? 0);

$productIds   = array_unique(array_column($cartRows, "input_product_id"));
$cartProducts = [];
foreach ($productIds as $pid) {
    $p = selectContent($conn, "panel_products", ["hash_id" => $pid, "visibility" => "show"]);
    if (!empty($p)) $cartProducts[$pid] = $p[0];
}

$subtotal = 0;
foreach ($cartRows as $row) {
    $price = isset($cartProducts[$row["input_product_id"]]) ? (float)$cartProducts[$row["input_product_id"]]["input_price"] : 0;
    $subtotal += $price * (int)$row["input_quantity"];
}
$shipping = $shop_free_ship > 0 && $subtotal >= $shop_free_ship ? 0 : $shop_ship_rate;
$tax      = $subtotal * ($shop_tax_rate / 100);
$total    = $subtotal + $shipping + $tax;
$sym      = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/>

<?php/*##cbcode_70001o##*?>
<div data-cbcodesection="cbcode_70001">
<style>
@keyframes checkoutShake {
  0%,100% { transform: translateX(0); }
  15%      { transform: translateX(-8px); }
  30%      { transform: translateX(8px); }
  45%      { transform: translateX(-6px); }
  60%      { transform: translateX(6px); }
  75%      { transform: translateX(-3px); }
  90%      { transform: translateX(3px); }
}
.checkout-shake {
  animation: checkoutShake 0.5s ease;
  border-color: #dc3545 !important;
  outline: none;
}
</style>
<div style="height:100px;background:#f6f6f6;"></div>
<section style="padding:0 0 100px;background:#f6f6f6;min-height:70vh;">
  <div class="container">
    <h1 class="heading-02" style="margin-bottom:40px;"
        data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>"><?= $csPageTitle ?></h1>
    <div class="checkout-layout">

      <!-- ── Checkout form (demo16 structure) ─────────────────── -->
      <div>
        <?php
        // Prefill from session/localStorage fallback
        $val_firstname = $_SESSION['customer_first_name'] ?? '';
        $val_lastname  = $_SESSION['customer_last_name']  ?? '';
        $val_email     = $_SESSION['customer_email']      ?? '';
        $val_phone     = '';
        ?>
        <form id="checkoutForm">

          <!-- Billing Details -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title"
                   data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>">Billing Details</div>
              <p style="font-size:12px;color:#888;margin:4px 0 0;">This address will be used for delivery</p>
            </div>
            <div class="checkout-block-body">

              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">First Name *</label>
                  <input class="checkout-input required-field" type="text" name="first_name" id="firstname"
                         value="<?= htmlspecialchars($val_firstname) ?>" placeholder="Jane" required>
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Last Name *</label>
                  <input class="checkout-input required-field" type="text" name="last_name" id="lastname"
                         value="<?= htmlspecialchars($val_lastname) ?>" placeholder="Smith" required>
                </div>
              </div>

              <div class="checkout-field">
                <label class="checkout-label">Email Address *</label>
                <input class="checkout-input required-field" type="email" name="email" id="emailInput"
                       value="<?= htmlspecialchars($val_email) ?>" placeholder="jane@example.com" required>
                <span id="emailMsg" style="font-size:12px;margin-top:4px;display:block;min-height:16px;"></span>
              </div>

              <div class="checkout-field">
                <label class="checkout-label">Country *</label>
                <select class="checkout-select required-field" name="country" id="countrySelect"
                        onchange="fetchStatesFunc(this)" required>
                  <option value="" selected disabled>Select Country…</option>
                  <?php
                  $countries_list = [
                    'NG'=>'Nigeria','US'=>'United States','GB'=>'United Kingdom',
                    'CA'=>'Canada','AU'=>'Australia','DE'=>'Germany','FR'=>'France',
                    'ZA'=>'South Africa','GH'=>'Ghana','KE'=>'Kenya','IN'=>'India',
                  ];
                  foreach ($countries_list as $code => $name): ?>
                    <option value="<?= $code ?>"><?= strtoupper($name) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">State / Province *</label>
                  <select class="checkout-select required-field" name="state" id="stateSelect" required>
                    <option value="" selected disabled>Select State</option>
                  </select>
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Town / City *</label>
                  <input class="checkout-input required-field" type="text" name="city" id="city"
                         placeholder="Lagos" required>
                </div>
              </div>

              <div class="checkout-field">
                <label class="checkout-label">Street Address *</label>
                <input class="checkout-input required-field" type="text" name="address_1" id="street-address-1"
                       placeholder="House number and street name" required>
              </div>

              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">Phone Number *</label>
                  <input class="checkout-input" type="tel" name="phone" id="phoneInput"
                         value="<?= htmlspecialchars($val_phone) ?>" required>
                  <span id="phoneMsg" style="font-size:12px;margin-top:4px;display:block;min-height:16px;"></span>
                  <input type="hidden" id="fullPhoneNumber" name="phone_full">
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Zip Code</label>
                  <input class="checkout-input" type="text" name="postal_code" id="zip_code" placeholder="100001">
                </div>
              </div>

              <div class="checkout-field">
                <label class="checkout-label">Additional Notes (optional)</label>
                <textarea class="checkout-input" name="notes" rows="3"
                          placeholder="Any special instructions for your order..."
                          style="height:auto;min-height:80px;resize:vertical;"></textarea>
              </div>

            </div>
          </div>

          <!-- Shipping Method -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title"
                   data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>"><?= $csShipMethod ?></div>
            </div>
            <div class="checkout-block-body">
              <label class="shipping-option active" id="shippingOptStandard">
                <input type="radio" name="shipping_method" value="standard" checked
                       onchange="updateShippingTotal('standard')">
                <div class="shipping-option-label">
                  <div class="shipping-option-name"
                       data-admc-manage="settings_shop_checkout_labels" data-admc-id="<?= $csId ?>"><?= $csStdName ?></div>
                  <div class="shipping-option-time"
                       data-admc-manage="settings_shop_checkout_labels" data-admc-id="<?= $csId ?>"><?= $csStdTime ?></div>
                </div>
                <span class="shipping-option-price">
                  <?= $shipping === 0 ? "Free" : $sym . number_format($shop_ship_rate, 2) ?>
                </span>
              </label>
              <?php if ($csExpPrice > 0): ?>
              <label class="shipping-option" id="shippingOptExpress">
                <input type="radio" name="shipping_method" value="express"
                       onchange="updateShippingTotal('express')">
                <div class="shipping-option-label">
                  <div class="shipping-option-name"
                       data-admc-manage="settings_shop_checkout_labels" data-admc-id="<?= $csId ?>"><?= $csExpName ?></div>
                  <div class="shipping-option-time"
                       data-admc-manage="settings_shop_checkout_labels" data-admc-id="<?= $csId ?>"><?= $csExpTime ?></div>
                </div>
                <span class="shipping-option-price"><?= $sym ?><?= number_format($csExpPrice, 2) ?></span>
              </label>
              <?php endif; ?>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title"
                   data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>"><?= $csPayment ?></div>
            </div>
            <div class="checkout-block-body">
              <label class="shipping-option active">
                <input type="radio" name="payment_method" value="Bank Transfer" checked
                       class="required-field">
                <div class="shipping-option-label">
                  <div class="shipping-option-name">Direct Bank Transfer</div>
                  <div class="shipping-option-time">Pay directly via bank transfer</div>
                </div>
              </label>
              <label class="shipping-option">
                <input type="radio" name="payment_method" value="Card"
                       class="required-field">
                <div class="shipping-option-label">
                  <div class="shipping-option-name">Pay with Card</div>
                  <div class="shipping-option-time">Secure online payment</div>
                </div>
              </label>
            </div>
          </div>

          <button type="button" class="place-order-btn" id="placeOrderBtn"
                  data-invalid="true"
                  style="opacity:0.55;cursor:not-allowed;"
                  data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>">
            Complete Form to Continue
          </button>
          <p style="font-size:12px;color:#b5b5b5;text-align:center;margin-top:10px;">
            Your payment info is encrypted and secure.
          </p>
        </form>
      </div>

      <!-- ── Order summary sidebar ──────────────────────────── -->
      <div class="order-summary-card" style="position:sticky;top:100px;">
        <div class="order-summary-title"
             data-admc-manage="settings_checkout" data-admc-id="<?= $csId ?>"><?= $csSummaryTitle ?></div>
        <?php foreach ($cartRows as $row):
          $p = $cartProducts[$row["input_product_id"]] ?? null;
          if (!$p) continue;
          $summaryProdId = (int)($p['id'] ?? 0);
        ?>
          <div class="order-item">
            <div style="position:relative;"
                 data-admc-image="panel_products" data-admc-id="<?= $summaryProdId ?>">
              <img src="<?= htmlspecialchars($p["image_1"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
                   class="order-item-img"
                   alt="<?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?>">
              <span style="position:absolute;top:-6px;right:-6px;background:#072708;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">
                <?= (int)$row["input_quantity"] ?>
              </span>
            </div>
            <div class="order-item-info">
              <div class="order-item-name"
                 data-admc-manage="panel_products" data-admc-id="<?= $summaryProdId ?>"><?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?></div>
              <?php if (!empty($row["input_variant"])): ?>
                <div class="order-item-variant"><?= htmlspecialchars($row["input_variant"], ENT_QUOTES, "UTF-8") ?></div>
              <?php endif; ?>
            </div>
            <div class="order-item-price">
              <?= $sym ?><?= number_format((float)$p["input_price"] * (int)$row["input_quantity"], 2) ?>
            </div>
          </div>
        <?php endforeach; ?>
        <hr class="order-divider">
        <div class="order-row">
          <span class="order-row-label">Subtotal</span>
          <span class="order-row-value"><?= $sym ?><?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="order-row">
          <span class="order-row-label">Shipping</span>
          <span class="order-row-value" id="summaryShipping"><?= $shipping === 0 ? "Free" : $sym . number_format($shipping, 2) ?></span>
        </div>
        <?php if ($shop_tax_rate > 0): ?>
          <div class="order-row">
            <span class="order-row-label">Tax (<?= $shop_tax_rate ?>%)</span>
            <span class="order-row-value"><?= $sym ?><?= number_format($tax, 2) ?></span>
          </div>
        <?php endif; ?>
        <div class="order-total-row">
          <span class="order-total-label">Total</span>
          <span class="order-total-value" id="summaryTotal"><?= $sym ?><?= number_format($total, 2) ?></span>
        </div>
      </div>

    </div>
  </div>
</section>
</div>
<?php/*##cbcode_70001c##*/>

<?php/*##cb1c##*/>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
var _baseUrl = (typeof window.VENORA_BASE_URL !== 'undefined' ? window.VENORA_BASE_URL : '') || '';
var _sym     = '<?= $sym ?>';
var _stdShip = <?= (float)$shop_ship_rate ?>;
var _expShip = <?= (float)$csExpPrice ?>;
var _subtotal= <?= (float)$subtotal ?>;
var _tax     = <?= (float)$tax ?>;
var _currentShipping = <?= (float)$shipping ?>;

/* ── Shipping option toggle ─────────────────────────────── */
document.querySelectorAll('.shipping-option').forEach(function(opt) {
  opt.querySelector('input[type=radio]').addEventListener('change', function() {
    document.querySelectorAll('.shipping-option').forEach(function(o) { o.classList.remove('active'); });
    opt.classList.add('active');
  });
});

function updateShippingTotal(method) {
  _currentShipping = (method === 'express') ? _expShip : (<?= $shop_free_ship > 0 && $subtotal >= $shop_free_ship ? 0 : (float)$shop_ship_rate ?>);
  var total = _subtotal + _currentShipping + _tax;

  /* Update Order Summary sidebar */
  var shipEl  = document.getElementById('summaryShipping');
  var totalEl = document.getElementById('summaryTotal');
  if (shipEl)  shipEl.textContent  = _currentShipping === 0 ? 'Free' : _sym + _currentShipping.toFixed(2);
  if (totalEl) totalEl.textContent = _sym + total.toFixed(2);

  /* Update Place Order button if form is valid */
  var btn = document.getElementById('placeOrderBtn');
  if (btn && !btn.disabled) {
    btn.textContent = '<?= $csPlaceOrder ?> — ' + _sym + total.toFixed(2);
  }
}

/* ── intl-tel-input (phone) ─────────────────────────────── */
var phoneInput = document.querySelector('#phoneInput');
var phoneMsg   = document.querySelector('#phoneMsg');
var iti = window.intlTelInput(phoneInput, {
  utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
  separateDialCode: true,
  initialCountry: 'ng',
  geoIpLookup: function(cb) {
    fetch('https://ipapi.co/json').then(function(r){ return r.json(); }).then(function(d){ cb(d.country_code); }).catch(function(){ cb('ng'); });
  }
});

function validatePhone() {
  if (phoneInput.value.trim()) {
    if (iti.isValidNumber()) {
      phoneInput.style.borderColor = '#198754';
      phoneMsg.style.color = '#198754';
      phoneMsg.textContent = 'Valid number';
    } else {
      phoneInput.style.borderColor = '#dc3545';
      phoneMsg.style.color = '#dc3545';
      phoneMsg.textContent = 'Invalid number format';
    }
  }
  checkFormValidity();
}
phoneInput.addEventListener('blur', validatePhone);
phoneInput.addEventListener('keyup', validatePhone);
phoneInput.addEventListener('countrychange', validatePhone);

/* ── Email validation ───────────────────────────────────── */
var emailInput = document.querySelector('#emailInput');
var emailMsg   = document.querySelector('#emailMsg');
function validateEmail() {
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailInput.value.trim() && regex.test(emailInput.value)) {
    emailInput.style.borderColor = '#198754';
    emailMsg.textContent = '';
  } else if (emailInput.value.trim()) {
    emailInput.style.borderColor = '#dc3545';
    emailMsg.style.color = '#dc3545';
    emailMsg.textContent = 'Invalid email';
  }
  checkFormValidity();
}
emailInput.addEventListener('input', validateEmail);
emailInput.addEventListener('blur', validateEmail);

/* ── State fetch (AJAX) ─────────────────────────────────── */
function fetchStatesFunc(el) {
  var stateSelect = document.getElementById('stateSelect');
  stateSelect.innerHTML = '<option>Loading…</option>';
  stateSelect.disabled = true;
  fetch(_baseUrl + '/fetch-state-backend', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'countryVal=' + encodeURIComponent(el.value)
  })
  .then(function(r){ return r.json(); })
  .then(function(res) {
    stateSelect.disabled = false;
    stateSelect.innerHTML = '<option value="" disabled selected>Select State</option>';
    var states = res.success || res.states || [];
    states.forEach(function(s) {
      var name = typeof s === 'string' ? s : (s.state || s.name || s);
      stateSelect.innerHTML += '<option value="' + name + '">' + name + '</option>';
    });
    checkFormValidity();
  })
  .catch(function() {
    stateSelect.disabled = false;
    stateSelect.innerHTML = '<option value="">Type your state below</option>';
    /* Fallback: convert to text input */
    var txt = document.createElement('input');
    txt.type = 'text'; txt.name = 'state'; txt.className = 'checkout-input required-field';
    txt.placeholder = 'Enter your state'; txt.required = true;
    txt.addEventListener('input', checkFormValidity);
    stateSelect.parentNode.replaceChild(txt, stateSelect);
  });
}

/* ── Shake animation on invalid field ───────────────────── */
function shakeField(el) {
  el.classList.remove('checkout-shake');
  void el.offsetWidth; /* force reflow to restart animation */
  el.classList.add('checkout-shake');
  el.addEventListener('animationend', function() {
    el.classList.remove('checkout-shake');
  }, { once: true });
}

function shakeAllInvalid() {
  requiredFields.forEach(function(f) {
    if (f.type === 'radio') return;
    if (!f.value.trim()) shakeField(f);
  });
  if (emailInput.style.borderColor === 'rgb(220, 53, 69)') shakeField(emailInput);
  if (!phoneInput.value.trim() || !iti.isValidNumber()) shakeField(phoneInput.closest('.iti') || phoneInput);
}

/* ── Form validity check (enables/disables Place Order) ─── */
var submitBtn = document.getElementById('placeOrderBtn');
var requiredFields = document.querySelectorAll('.required-field');

function checkFormValidity() {
  var valid = true;
  requiredFields.forEach(function(f) {
    if (f.type === 'radio') return; /* at least one radio checked = OK */
    if (!f.value.trim()) valid = false;
  });
  if (emailInput.style.borderColor === 'rgb(220, 53, 69)') valid = false;
  if (phoneInput.value.trim() && !iti.isValidNumber()) valid = false;
  if (!phoneInput.value.trim()) valid = false;

  var liveTotal = _subtotal + _currentShipping + _tax;
  if (valid) {
    submitBtn.removeAttribute('data-invalid');
    submitBtn.style.opacity = '1';
    submitBtn.style.cursor  = 'pointer';
    submitBtn.textContent   = '<?= $csPlaceOrder ?> — ' + _sym + liveTotal.toFixed(2);
  } else {
    submitBtn.setAttribute('data-invalid', 'true');
    submitBtn.style.opacity = '0.55';
    submitBtn.style.cursor  = 'not-allowed';
    submitBtn.textContent   = 'Complete Form to Continue';
  }
}

requiredFields.forEach(function(f) {
  f.addEventListener('input', checkFormValidity);
  f.addEventListener('change', checkFormValidity);
  /* Shake on blur if still empty */
  if (f.type !== 'radio') {
    f.addEventListener('blur', function() {
      if (!f.value.trim()) shakeField(f);
    });
  }
});

/* Click: shake if invalid, submit if valid */
submitBtn.addEventListener('click', function() {
  if (submitBtn.getAttribute('data-invalid') === 'true') {
    shakeAllInvalid();
  } else {
    submitCheckout();
  }
});

/* ── Auto-fill from localStorage ───────────────────────── */
window.addEventListener('DOMContentLoaded', function() {
  var stored;
  try { stored = JSON.parse(localStorage.getItem('mckCheckoutData')); } catch(e) {}
  if (stored) {
    ['firstname','lastname','city','street-address-1','zip_code'].forEach(function(id) {
      var el = document.getElementById(id); var key = id.replace('-','_');
      if (el && !el.value && stored[key]) { el.value = stored[key]; }
    });
    if (stored.email && !emailInput.value) emailInput.value = stored.email;
    if (stored.phone) {
      var wait = setInterval(function() {
        if (typeof intlTelInputUtils !== 'undefined') {
          clearInterval(wait);
          iti.setNumber(stored.phone.startsWith('+') ? stored.phone : '+' + stored.phone);
          setTimeout(function(){ validatePhone(); checkFormValidity(); }, 100);
        }
      }, 200);
    }
    if (stored.country) {
      var cs = document.getElementById('countrySelect');
      if (cs) { cs.value = stored.country; fetchStatesFunc(cs); }
    }
  }
  setTimeout(checkFormValidity, 800);
});

/* ── Submit ─────────────────────────────────────────────── */
function submitCheckout() {
  if (submitBtn.getAttribute('data-invalid') === 'true') { shakeAllInvalid(); return; }
  if (!iti.isValidNumber()) {
    phoneInput.style.borderColor = '#dc3545';
    phoneMsg.style.color = '#dc3545';
    phoneMsg.textContent = 'Please enter a valid phone number';
    phoneInput.focus(); return;
  }
  document.getElementById('fullPhoneNumber').value = iti.getNumber();

  /* Save to localStorage */
  try {
    localStorage.setItem('mckCheckoutData', JSON.stringify({
      firstname: document.getElementById('firstname').value,
      lastname:  document.getElementById('lastname').value,
      email:     emailInput.value,
      phone:     iti.getNumber(),
      city:      document.getElementById('city').value,
      street_address_1: document.getElementById('street-address-1').value,
      zip_code:  document.getElementById('zip_code').value,
      country:   document.getElementById('countrySelect').value,
      state:     (document.getElementById('stateSelect') || {}).value || ''
    }));
  } catch(e) {}

  submitBtn.textContent = 'Processing…';
  submitBtn.setAttribute('data-invalid', 'true');
  submitBtn.style.opacity = '0.7';
  submitBtn.style.cursor = 'not-allowed';

  var data = {};
  new FormData(document.getElementById('checkoutForm')).forEach(function(v,k){ data[k]=v; });

  fetch(_baseUrl + '/checkout-process', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(function(r){ return r.json(); })
  .then(function(res) {
    if (res.success) {
      window.location.href = _baseUrl + '/orders/' + res.order_id;
    } else {
      submitBtn.textContent = '✗ ' + (res.error || 'Payment failed. Try again.');
      submitBtn.style.background = '#c1121f';
      submitBtn.style.opacity = '1';
      submitBtn.disabled = false;
      setTimeout(function() {
        var total = _subtotal + _currentShipping + _tax;
        submitBtn.textContent = '<?= $csPlaceOrder ?> — ' + _sym + total.toFixed(2);
        submitBtn.style.background = ''; submitBtn.style.opacity = '1';
      }, 4000);
    }
  })
  .catch(function() {
    submitBtn.textContent = 'Network error — please try again.';
    submitBtn.style.background = '#c1121f';
    submitBtn.disabled = false; submitBtn.style.opacity = '1';
  });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
