<?php
$page_title = "Your Cart";
$bodyClass  = "page-light-navbar";

$sessionId = session_id();
$cartRows  = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);

$productIds  = array_unique(array_column($cartRows, "input_product_id"));
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

<?php/*##cbcode_60001o##*?>
<div data-cbcodesection="cbcode_60001">
<div style="height:100px;background:#f6f6f6;"></div>
<div style="padding:0 0 100px;background:#f6f6f6;min-height:60vh;">
  <div class="container">
    <h1 class="heading-02" style="margin-bottom:40px;">Your Cart</h1>

    <?php if (empty($cartRows)): ?>
      <div style="text-align:center;padding:80px 20px;">
        <div style="font-size:64px;margin-bottom:20px;">🛍</div>
        <h2 class="heading-03" style="margin-bottom:12px;">Your cart is empty</h2>
        <p class="p-01 color-gray" style="margin-bottom:28px;">Add some products you love to get started.</p>
        <a class="btn-02-link w-inline-block" href="/products" style="display:inline-flex;">
          <div class="btn-inner"><div class="btn-text-wrap">
            <div class="btn-text-3 _01"><div class="cta-text">Shop Now</div></div>
            <div class="btn-text-3 _02"><div class="cta-text">Shop Now</div></div>
          </div></div>
        </a>
      </div>

    <?php else: ?>
      <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start;">

        <!-- Cart items table -->
        <div style="background:white;border-radius:8px;overflow:hidden;">
          <div style="padding:20px 24px;border-bottom:1px solid #dedede;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#5c5f6a;display:grid;grid-template-columns:1fr 120px 100px;gap:20px;">
            <span>Product</span><span style="text-align:center;">Qty</span><span style="text-align:right;">Total</span>
          </div>
          <?php foreach ($cartRows as $row):
            $p = $cartProducts[$row["input_product_id"]] ?? null;
            if (!$p) continue;
            $rowId     = htmlspecialchars($row["hash_id"], ENT_QUOTES, "UTF-8");
            $lineTotal = (float)$p["input_price"] * (int)$row["input_quantity"];
          ?>
            <div style="padding:20px 24px;border-bottom:1px solid #f0f0f0;display:grid;grid-template-columns:1fr 120px 100px;gap:20px;align-items:center;"
                 id="cart-row-<?= $rowId ?>">
              <a href="/products/<?= $p["hash_id"] ?>/<?= cleans($p["input_title"]) ?>"
                 style="text-decoration:none;display:flex;gap:16px;align-items:flex-start;">
                <img src="<?= htmlspecialchars($p["image_1"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
                     style="width:72px;height:88px;object-fit:cover;border-radius:4px;flex-shrink:0;"
                     alt="<?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?>">
                <div>
                  <div style="font-size:15px;font-weight:600;color:#072708;margin-bottom:4px;line-height:1.3;">
                    <?= htmlspecialchars($p["input_title"], ENT_QUOTES, "UTF-8") ?>
                  </div>
                  <?php if (!empty($row["input_variant"])): ?>
                    <div style="font-size:12px;color:#5c5f6a;margin-bottom:4px;"><?= htmlspecialchars($row["input_variant"], ENT_QUOTES, "UTF-8") ?></div>
                  <?php endif; ?>
                  <div style="font-size:14px;color:#5c5f6a;"><?= $sym ?><?= number_format((float)$p["input_price"], 2) ?></div>
                  <button onclick="doCartRemove('<?= $rowId ?>')"
                          style="background:none;border:none;font-size:12px;color:#b5b5b5;cursor:pointer;text-decoration:underline;padding:0;margin-top:6px;">
                    Remove
                  </button>
                </div>
              </a>
              <div class="qty-control" style="justify-self:center;">
                <button class="qty-btn" onclick="doCartUpdate('<?= $rowId ?>', Math.max(1, parseInt(document.getElementById('qty-<?= $rowId ?>').value)-1))">−</button>
                <input class="qty-input" id="qty-<?= $rowId ?>" type="number" value="<?= (int)$row["input_quantity"] ?>" min="1"
                       onchange="doCartUpdate('<?= $rowId ?>', this.value)">
                <button class="qty-btn" onclick="doCartUpdate('<?= $rowId ?>', parseInt(document.getElementById('qty-<?= $rowId ?>').value)+1)">+</button>
              </div>
              <div style="text-align:right;font-size:15px;font-weight:600;color:#072708;">
                <?= $sym ?><?= number_format($lineTotal, 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Order summary -->
        <div class="order-summary-card" style="position:sticky;top:100px;">
          <div class="order-summary-title">Order Summary</div>
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
          <?php if ($shop_free_ship > 0 && $subtotal < $shop_free_ship): ?>
            <p style="font-size:12px;color:#5c5f6a;margin:12px 0;padding:10px 12px;background:#f6f6f6;border-radius:4px;">
              Add <?= $sym ?><?= number_format($shop_free_ship - $subtotal, 2) ?> more for free shipping!
            </p>
          <?php endif; ?>
          <div class="order-total-row">
            <span class="order-total-label">Total</span>
            <span class="order-total-value"><?= $sym ?><?= number_format($total, 2) ?></span>
          </div>
          <a href="/checkout" class="place-order-btn" style="text-decoration:none;display:block;text-align:center;margin-top:0;">
            Proceed to Checkout
          </a>
          <a href="/products" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:#5c5f6a;text-decoration:underline;">
            Continue Shopping
          </a>
        </div>

      </div>
    <?php endif; ?>
  </div>
</div>
</div>
<?php/*##cbcode_60001c##*/>

<?php/*##cb1c##*/>
</div>

<script>
function doCartUpdate(cartId, qty) {
  fetch('/cart-update', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({cart_id: cartId, quantity: parseInt(qty)})
  }).then(function(r){ return r.json(); }).then(function(){ location.reload(); });
}
function doCartRemove(cartId) {
  fetch('/cart-remove', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({cart_id: cartId})
  }).then(function(r){ return r.json(); }).then(function(){ location.reload(); });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
