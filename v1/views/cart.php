<?php
$page_title = "Your Cart";
$bodyClass  = "page-light-navbar";

// Standardized User ID resolver from cart_functions.php
$cartUserId = getCartUserId();

try {
    $cartData = getCartItems();
    $cartItems = $cartData['items'] ?? [];
    $subtotal = $usdEnabled ? $cartData['total_usd'] : $cartData['total_ngn'];
    $totalQty = $cartData['total_quantity'] ?? 0;
} catch (Exception $e) {
    $cartItems = [];
    $subtotal = 0;
    $totalQty = 0;
}

$shipping = $shop_free_ship > 0 && $subtotal >= $shop_free_ship ? 0 : $shop_ship_rate;
$tax      = $subtotal * ($shop_tax_rate / 100);
$total    = $subtotal + $shipping + $tax;
$sym      = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/?>

<div style="height:100px;background:var(--bg-colour, #f6f6f6);"></div>
<div style="padding:0 0 100px;background:var(--bg-colour, #f6f6f6);min-height:60vh;">
  <div class="container">
    <div style="margin-bottom:40px;">
      <h1 class="heading-02" style="margin-bottom:8px;">Your Cart</h1>
      <p class="p-01 color-gray"><?= $totalQty ?> items in your bag</p>
    </div>

    <?php if (empty($cartItems)): ?>
      <div style="background:var(--v-white, #fff); border-radius:16px; text-align:center; padding:80px 20px; box-shadow:0 4px 24px rgba(0,0,0,0.04);">
        <div style="font-size:64px;margin-bottom:24px;">🛍</div>
        <h2 class="heading-03" style="margin-bottom:12px;">Your cart is empty</h2>
        <p class="p-01 color-gray" style="margin-bottom:32px;">Looks like you haven't added anything yet.</p>
        <a class="btn-02-link w-inline-block" href="<?= $baseUrl ?>/products" style="display:inline-flex;">
          <div class="btn-inner"><div class="btn-text-wrap">
            <div class="btn-text-3 _01"><div class="cta-text">Explore Products</div></div>
            <div class="btn-text-3 _02"><div class="cta-text">Explore Products</div></div>
          </div></div>
        </a>
      </div>

    <?php else: ?>
      <div class="cart-layout">

        <!-- LEFT: Cart items -->
        <div class="cart-items-column">
          <?php foreach ($cartItems as $item):
            $rowId     = $item["cart_id"];
            $lineTotal = $usdEnabled ? ($item["total_usd"] ?? 0) : ($item["total_ngn"] ?? 0);
            $prodLink  = $baseUrl . '/products/' . htmlspecialchars($item["product_id"], ENT_QUOTES, "UTF-8") . '/' . cleans($item["product_name"]);
            $price     = $usdEnabled ? ($item["price_usd"] ?? 0) : ($item["price_ngn"] ?? 0);
          ?>
            <div class="cart-card" id="cart-row-<?= $rowId ?>">
              <div class="cart-card-inner">
                <a href="<?= $prodLink ?>" class="cart-card-img-link">
                  <img src="<?= htmlspecialchars($item["image"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
                       alt="<?= htmlspecialchars($item["product_name"], ENT_QUOTES, "UTF-8") ?>">
                </a>
                
                <div class="cart-card-details">
                  <div class="cart-card-top">
                    <a href="<?= $prodLink ?>" class="cart-card-title">
                      <?= htmlspecialchars($item["product_name"], ENT_QUOTES, "UTF-8") ?>
                    </a>
                    <button class="cart-card-remove" onclick="doCartRemove('<?= $rowId ?>')" aria-label="Remove">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                    </button>
                  </div>

                  <?php if (!empty($item["variant_options"])): ?>
                    <div class="cart-card-meta"><?= htmlspecialchars($item["variant_options"], ENT_QUOTES, "UTF-8") ?></div>
                  <?php endif; ?>

                  <div class="cart-card-bottom">
                    <div class="qty-control">
                      <button class="qty-btn" onclick="doCartUpdate('<?= $rowId ?>', Math.max(1, parseInt(document.getElementById('qty-<?= $rowId ?>').value)-1))">−</button>
                      <input class="qty-input" id="qty-<?= $rowId ?>" type="number" value="<?= (int)$item["quantity"] ?>" min="1" readonly>
                      <button class="qty-btn" onclick="doCartUpdate('<?= $rowId ?>', parseInt(document.getElementById('qty-<?= $rowId ?>').value)+1)">+</button>
                    </div>
                    <div class="cart-card-price">
                      <div class="price-each"><?= formatPrice($price, $sym) ?> each</div>
                      <div class="price-total"><?= formatPrice($lineTotal, $sym) ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- RIGHT: Order summary -->
        <div class="cart-summary-column">
          <div class="order-summary-card">
            <h3 class="heading-05" style="margin-bottom:24px;">Order Summary</h3>
            
            <div class="summary-rows">
              <div class="summary-row">
                <span class="label">Total Items</span>
                <span class="value"><?= $totalQty ?></span>
              </div>
              <div class="summary-row">
                <span class="label">Subtotal</span>
                <span class="value"><?= formatPrice($subtotal, $sym) ?></span>
              </div>
              <div class="summary-row">
                <span class="label">Shipping</span>
                <span class="value"><?= $shipping === 0 ? "Free" : formatPrice($shipping, $sym) ?></span>
              </div>
              <?php if ($shop_tax_rate > 0): ?>
                <div class="summary-row">
                  <span class="label">Tax (<?= $shop_tax_rate ?>%)</span>
                  <span class="value"><?= formatPrice($tax, $sym) ?></span>
                </div>
              <?php endif; ?>
            </div>

            <?php if ($shop_free_ship > 0 && $subtotal < $shop_free_ship): ?>
              <div class="shipping-nudge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7M5 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM18 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                <span>Add <strong><?= formatPrice($shop_free_ship - $subtotal, $sym) ?></strong> more for <strong>Free Shipping!</strong></span>
              </div>
            <?php endif; ?>

            <div class="summary-total">
              <span class="label">Estimated Total</span>
              <span class="value"><?= formatPrice($total, $sym) ?></span>
            </div>

            <a href="<?= $baseUrl ?>/checkout" class="place-order-btn" style="text-decoration:none;">
              <span>Checkout Now</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            
            <a href="<?= $baseUrl ?>/products" class="continue-shopping">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              <span>Continue Shopping</span>
            </a>
          </div>
        </div>

      </div>
    <?php endif; ?>
  </div>
</div>
</div>

<style>
.cart-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 40px;
  align-items: flex-start;
}
.cart-card {
  background: var(--v-white, #fff);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 1px 8px rgba(0,0,0,0.03);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.cart-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
.cart-card-inner {
  display: flex;
  gap: 24px;
}
.cart-card-img-link {
  width: 100px;
  height: 120px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f0f0f0;
}
.cart-card-img-link img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cart-card-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.cart-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 6px;
}
.cart-card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary, #072708);
  text-decoration: none;
  line-height: 1.3;
}
.cart-card-remove {
  background: none;
  border: none;
  color: #b5b5b5;
  cursor: pointer;
  padding: 4px;
  transition: color 0.2s;
}
.cart-card-remove:hover { color: #c1121f; }
.cart-card-meta {
  font-size: 12px;
  color: var(--text-secondary, #5c5f6a);
  margin-bottom: auto;
}
.cart-card-bottom {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-top: 16px;
}
.cart-card-price { text-align: right; }
.price-each { font-size: 12px; color: #b5b5b5; margin-bottom: 2px; }
.price-total { font-size: 18px; font-weight: 800; color: var(--text-primary, #072708); }

.summary-rows { border-top: 1px solid #f0f0f0; padding-top: 20px; margin-bottom: 20px; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
.summary-row .label { color: var(--text-secondary, #5c5f6a); }
.summary-row .value { font-weight: 600; color: var(--text-primary, #072708); }

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  border-top: 1px solid #f0f0f0;
  margin-bottom: 28px;
}
.summary-total .label { font-weight: 700; font-size: 16px; }
.summary-total .value { font-weight: 800; font-size: 22px; color: var(--primary, #072708); }

.shipping-nudge {
  background: rgba(var(--primary-rgb), 0.06);
  color: var(--primary, #072708);
  padding: 14px;
  border-radius: 10px;
  font-size: 13px;
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 24px;
}
.place-order-btn {
  width: 100%;
  background: var(--primary, #072708);
  color: #fff !important;
  padding: 18px;
  border-radius: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  transition: all 0.3s;
}
.place-order-btn:hover { opacity: 0.9; transform: translateY(-1px); }
.continue-shopping {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 20px;
  color: var(--text-secondary, #5c5f6a);
  font-size: 14px;
  font-weight: 500;
}
.continue-shopping:hover { color: var(--primary); }

@media (max-width: 991px) {
  .cart-layout { grid-template-columns: 1fr; gap: 32px; }
  .cart-summary-column { order: -1; }
  .order-summary-card { position: static !important; }
}
@media (max-width: 480px) {
  .cart-card-inner { gap: 16px; }
  .cart-card-img-link { width: 80px; height: 100px; }
  .price-total { font-size: 16px; }
}
</style>

<script>
var _cartBase = (typeof window.VENORA_BASE_URL !== 'undefined' ? window.VENORA_BASE_URL : '') || '';
function doCartUpdate(cartId, qty) {
  fetch(_cartBase + '/cart-update', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({cart_id: cartId, quantity: parseInt(qty)})
  }).then(function(r){ return r.json(); }).then(function(){ location.reload(); });
}
function doCartRemove(cartId) {
  fetch(_cartBase + '/cart-remove', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({cart_id: cartId})
  }).then(function(r){ return r.json(); }).then(function(){ location.reload(); });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
