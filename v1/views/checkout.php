<?php
$page_title = "Checkout";
$bodyClass  = "";

$sessionId = session_id();
$cartRows  = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);

if (empty($cartRows)) { header("Location: /cart"); exit; }

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
<section style="padding:60px 0 100px;background:#f6f6f6;min-height:70vh;">
  <div class="container">
    <h1 class="heading-02" style="margin-bottom:40px;">Checkout</h1>
    <div class="checkout-layout">

      <!-- ── Checkout form ──────────────────────────────────── -->
      <div>
        <form id="checkoutForm" onsubmit="submitCheckout(event)">

          <!-- Contact info -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title">Contact Information</div>
            </div>
            <div class="checkout-block-body">
              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">First Name *</label>
                  <input class="checkout-input" type="text" name="first_name" placeholder="Jane" required>
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Last Name *</label>
                  <input class="checkout-input" type="text" name="last_name" placeholder="Smith" required>
                </div>
              </div>
              <div class="checkout-field">
                <label class="checkout-label">Email Address *</label>
                <input class="checkout-input" type="email" name="email" placeholder="jane@example.com" required>
              </div>
              <div class="checkout-field">
                <label class="checkout-label">Phone Number</label>
                <input class="checkout-input" type="tel" name="phone" placeholder="+1 (555) 000-0000">
              </div>
            </div>
          </div>

          <!-- Shipping address -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title">Shipping Address</div>
            </div>
            <div class="checkout-block-body">
              <div class="checkout-field">
                <label class="checkout-label">Address Line 1 *</label>
                <input class="checkout-input" type="text" name="address_1" placeholder="123 Main Street" required>
              </div>
              <div class="checkout-field">
                <label class="checkout-label">Address Line 2</label>
                <input class="checkout-input" type="text" name="address_2" placeholder="Apartment, suite, etc.">
              </div>
              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">City *</label>
                  <input class="checkout-input" type="text" name="city" required>
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Postal Code *</label>
                  <input class="checkout-input" type="text" name="postal_code" required>
                </div>
              </div>
              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">State / Province</label>
                  <input class="checkout-input" type="text" name="state">
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">Country *</label>
                  <select class="checkout-select" name="country" required>
                    <option value="">Select country…</option>
                    <option value="US" selected>United States</option>
                    <option value="CA">Canada</option>
                    <option value="GB">United Kingdom</option>
                    <option value="NG">Nigeria</option>
                    <option value="AU">Australia</option>
                    <option value="DE">Germany</option>
                    <option value="FR">France</option>
                  </select>
                </div>
              </div>
              <div class="checkout-field">
                <label class="checkout-label">Order Notes (optional)</label>
                <textarea class="checkout-input" name="notes" rows="2"
                          placeholder="Any special instructions for your order..."
                          style="height:auto;min-height:70px;resize:vertical;"></textarea>
              </div>
            </div>
          </div>

          <!-- Shipping method -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title">Shipping Method</div>
            </div>
            <div class="checkout-block-body">
              <label class="shipping-option active">
                <input type="radio" name="shipping_method" value="standard" checked>
                <div class="shipping-option-label">
                  <div class="shipping-option-name">Standard Shipping</div>
                  <div class="shipping-option-time">5–7 business days</div>
                </div>
                <span class="shipping-option-price">
                  <?= $shipping === 0 ? "Free" : $sym . number_format($shop_ship_rate, 2) ?>
                </span>
              </label>
              <label class="shipping-option">
                <input type="radio" name="shipping_method" value="express">
                <div class="shipping-option-label">
                  <div class="shipping-option-name">Express Shipping</div>
                  <div class="shipping-option-time">2–3 business days</div>
                </div>
                <span class="shipping-option-price"><?= $sym ?>12.99</span>
              </label>
            </div>
          </div>

          <!-- Payment info -->
          <div class="checkout-block">
            <div class="checkout-block-header">
              <div class="checkout-block-title">Payment Information</div>
            </div>
            <div class="checkout-block-body">
              <div style="padding:16px;background:#f6f6f6;border-radius:4px;font-size:13px;color:#5c5f6a;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <img src="/assets/img/icons/stripe.svg" alt="Stripe" style="height:20px;">
                <span>Secure payment powered by Stripe</span>
              </div>
              <div class="checkout-field">
                <label class="checkout-label">Card Number</label>
                <input class="checkout-input" type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
              </div>
              <div class="checkout-row">
                <div class="checkout-field">
                  <label class="checkout-label">Expiry Date</label>
                  <input class="checkout-input" type="text" name="card_expiry" placeholder="MM / YY" maxlength="7">
                </div>
                <div class="checkout-field">
                  <label class="checkout-label">CVC</label>
                  <input class="checkout-input" type="text" name="card_cvc" placeholder="123" maxlength="4">
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="place-order-btn" id="placeOrderBtn">
            Place Order — <?= $sym ?><?= number_format($total, 2) ?>
          </button>
          <p style="font-size:12px;color:#b5b5b5;text-align:center;margin-top:10px;">
            Your payment info is encrypted and secure.
          </p>
        </form>
      </div>

      <!-- ── Order summary sidebar ──────────────────────────── -->
      <div class="order-summary-card" style="position:sticky;top:100px;">
        <div class="order-summary-title">Order Summary</div>
        <?php foreach ($cartRows as $row):
          $p = $cartProducts[$row["input_product_id"]] ?? null;
          if (!$p) continue;
        ?>
          <div class="order-item">
            <div style="position:relative;">
              <img src="<?= htmlspecialchars($p["image_1"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
                   class="order-item-img"
                   alt="<?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?>">
              <span style="position:absolute;top:-6px;right:-6px;background:#072708;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">
                <?= (int)$row["input_quantity"] ?>
              </span>
            </div>
            <div class="order-item-info">
              <div class="order-item-name"><?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?></div>
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
          <span class="order-row-value"><?= $shipping === 0 ? "Free" : $sym . number_format($shipping, 2) ?></span>
        </div>
        <?php if ($shop_tax_rate > 0): ?>
          <div class="order-row">
            <span class="order-row-label">Tax (<?= $shop_tax_rate ?>%)</span>
            <span class="order-row-value"><?= $sym ?><?= number_format($tax, 2) ?></span>
          </div>
        <?php endif; ?>
        <div class="order-total-row">
          <span class="order-total-label">Total</span>
          <span class="order-total-value"><?= $sym ?><?= number_format($total, 2) ?></span>
        </div>
      </div>

    </div>
  </div>
</section>
</div>
<?php/*##cbcode_70001c##*/>

<?php/*##cb1c##*/>
</div>

<script>
// Shipping option toggle
document.querySelectorAll('.shipping-option').forEach(function(opt) {
  opt.addEventListener('click', function() {
    document.querySelectorAll('.shipping-option').forEach(function(o) { o.classList.remove('active'); });
    opt.classList.add('active');
  });
});

function submitCheckout(e) {
  e.preventDefault();
  var btn = document.getElementById('placeOrderBtn');
  btn.textContent = 'Processing...';
  btn.disabled = true;
  var data = {};
  new FormData(e.target).forEach(function(v, k) { data[k] = v; });
  fetch('/checkout-process', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.success) {
      window.location.href = '/orders/' + res.order_id;
    } else {
      btn.textContent = '✗ ' + (res.error || 'Payment failed. Please try again.');
      btn.style.background = '#c1121f';
      btn.disabled = false;
      setTimeout(function() {
        btn.textContent = 'Place Order — <?= $sym ?><?= number_format($total, 2) ?>';
        btn.style.background = '';
      }, 4000);
    }
  })
  .catch(function() {
    btn.textContent = 'Network error — please try again.';
    btn.style.background = '#c1121f';
    btn.disabled = false;
  });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
