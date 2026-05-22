<?php
$page_title = "Your Cart";
$bodyClass  = "page-light-navbar";

// Standardized User ID resolver
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

<!-- Cart Header Section -->
<div style="height:100px;background:var(--bg-colour);"></div>
<div class="cart-page-wrapper">
  <div class="container">
    
    <div class="cart-header-main">
      <h1 class="heading-02">Shopping Cart</h1>
      <p class="p-01"><?= $totalQty ?> items in your bag</p>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="empty-cart-state">
        <div class="empty-icon">🛍️</div>
        <h2 class="heading-03">Your bag is currently empty</h2>
        <p class="p-01">Discover our collection and find something special.</p>
        <a class="btn-02-link w-inline-block" href="<?= $baseUrl ?>/products">
          <div class="btn-inner"><div class="btn-text-wrap">
            <div class="btn-text-3 _01"><div class="cta-text">Continue Shopping</div></div>
            <div class="btn-text-3 _02"><div class="cta-text">Continue Shopping</div></div>
          </div></div>
        </a>
      </div>

    <?php else: ?>
      <div class="cart-main-grid">
        
        <!-- LEFT: Items List -->
        <div class="cart-items-section">
          <!-- Desktop Table Header -->
          <div class="cart-table-header">
            <div class="col-product">Product</div>
            <div class="col-qty text-center">Quantity</div>
            <div class="col-total text-right">Total</div>
          </div>

          <div class="cart-items-list">
            <?php foreach ($cartItems as $item):
              $rowId     = $item["cart_id"];
              $lineTotal = $usdEnabled ? ($item["total_usd"] ?? 0) : ($item["total_ngn"] ?? 0);
              $prodLink  = $baseUrl . '/products/' . htmlspecialchars($item["product_id"], ENT_QUOTES, "UTF-8") . '/' . cleans($item["product_name"]);
              $price     = $usdEnabled ? ($item["price_usd"] ?? 0) : ($item["price_ngn"] ?? 0);
            ?>
              <div class="cart-row" id="cart-row-<?= $rowId ?>">
                <!-- Product Column -->
                <div class="col-product">
                  <div class="product-item-box">
                    <a href="<?= $prodLink ?>" class="product-img-link">
                      <img src="<?= htmlspecialchars($item["image"], ENT_QUOTES, "UTF-8") ?>" alt="<?= htmlspecialchars($item["product_name"], ENT_QUOTES, "UTF-8") ?>">
                    </a>
                    <div class="product-info-box">
                      <a href="<?= $prodLink ?>" class="product-name-link"><?= htmlspecialchars($item["product_name"], ENT_QUOTES, "UTF-8") ?></a>
                      <?php if (!empty($item["variant_options"])): ?>
                        <div class="product-variants-text"><?= htmlspecialchars($item["variant_options"], ENT_QUOTES, "UTF-8") ?></div>
                      <?php endif; ?>
                      <div class="product-price-mobile">Unit Price: <?= formatPrice($price, $sym) ?></div>
                      <button class="remove-item-btn" onclick="doCartRemove('<?= $rowId ?>')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                        <span>Remove</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Quantity Column -->
                <div class="col-qty">
                  <div class="quantity-control-v2">
                    <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', Math.max(1, parseInt(document.getElementById('qty-<?= $rowId ?>').value)-1))">−</button>
                    <input class="q-input" id="qty-<?= $rowId ?>" type="number" value="<?= (int)$item["quantity"] ?>" min="1" readonly>
                    <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', parseInt(document.getElementById('qty-<?= $rowId ?>').value)+1)">+</button>
                  </div>
                </div>

                <!-- Total Column -->
                <div class="col-total">
                  <div class="row-price-wrap">
                    <div class="price-each-desktop"><?= formatPrice($price, $sym) ?> each</div>
                    <div class="price-line-total"><?= formatPrice($lineTotal, $sym) ?></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="cart-footer-actions">
            <a href="<?= $baseUrl ?>/products" class="back-link">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              <span>Continue Shopping</span>
            </a>
          </div>
        </div>

        <!-- RIGHT: Summary Sidebar -->
        <div class="cart-summary-section">
          <div class="summary-card-v2">
            <h3 class="summary-title">Order Summary</h3>
            
            <div class="summary-detail-rows">
              <div class="s-row">
                <span class="s-label">Total Items</span>
                <span class="s-value"><?= $totalQty ?></span>
              </div>
              <div class="s-row">
                <span class="s-label">Subtotal</span>
                <span class="s-value"><?= formatPrice($subtotal, $sym) ?></span>
              </div>
              <div class="s-row">
                <span class="s-label">Shipping</span>
                <span class="s-value"><?= $shipping === 0 ? "Free" : formatPrice($shipping, $sym) ?></span>
              </div>
              <?php if ($shop_tax_rate > 0): ?>
                <div class="s-row">
                  <span class="s-label">Tax (<?= $shop_tax_rate ?>%)</span>
                  <span class="s-value"><?= formatPrice($tax, $sym) ?></span>
                </div>
              <?php endif; ?>
            </div>

            <?php if ($shop_free_ship > 0 && $subtotal < $shop_free_ship): ?>
              <div class="free-ship-meter">
                <div class="meter-text">Add <strong><?= formatPrice($shop_free_ship - $subtotal, $sym) ?></strong> for <strong>Free Shipping</strong></div>
                <div class="meter-bar"><div class="meter-fill" style="width: <?= min(100, ($subtotal/$shop_free_ship)*100) ?>%"></div></div>
              </div>
            <?php endif; ?>

            <div class="total-big-row">
              <span class="total-label">Estimated Total</span>
              <span class="total-value"><?= formatPrice($total, $sym) ?></span>
            </div>

            <a href="<?= $baseUrl ?>/checkout" class="checkout-btn-premium">
              <span>Secure Checkout</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>

            <div class="secure-badges">
              <div class="s-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Secure SSL Connection</div>
            </div>
          </div>
        </div>

      </div>
    <?php endif; ?>
  </div>
</div>

<style>
.cart-page-wrapper { padding: 40px 0 120px; background: #ffffff; min-height: 70vh; }
.cart-header-main { margin-bottom: 48px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 24px; }
.cart-header-main h1 { margin-bottom: 4px; }
.cart-header-main p { color: var(--text-secondary); font-size: 15px; }

/* Main Grid */
.cart-main-grid { display: grid; grid-template-columns: 1fr 360px; gap: 60px; align-items: flex-start; }

/* Table styling for items */
.cart-table-header { display: grid; grid-template-columns: 1fr 140px 140px; padding: 0 0 16px; border-bottom: 1px solid rgba(0,0,0,0.1); color: var(--text-secondary); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }

.cart-items-list { margin-bottom: 32px; }
.cart-row { display: grid; grid-template-columns: 1fr 140px 140px; padding: 32px 0; border-bottom: 1px solid rgba(0,0,0,0.05); align-items: center; transition: background 0.2s; }

.product-item-box { display: flex; gap: 24px; align-items: center; }
.product-img-link { width: 110px; height: 130px; border-radius: 12px; overflow: hidden; background: #f8f8f8; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
.product-img-link img { width: 100%; height: 100%; object-fit: cover; }

.product-info-box { flex: 1; min-width: 0; }
.product-name-link { font-size: 18px; font-weight: 700; color: var(--text-primary); text-decoration: none; display: block; margin-bottom: 4px; line-height: 1.3; }
.product-variants-text { font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; line-height: 1.4; }
.product-price-mobile { display: none; }

.remove-item-btn { display: flex; align-items: center; gap: 6px; background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 0; opacity: 0.7; transition: opacity 0.2s; text-transform: uppercase; }

.quantity-control-v2 { display: inline-flex; align-items: center; border: 1.5px solid #eee; border-radius: 10px; background: #fff; overflow: hidden; margin: 0 auto; }
.q-btn { width: 36px; height: 40px; border: none; background: none; cursor: pointer; font-size: 18px; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; }
.q-input { width: 44px; border: none; text-align: center; font-weight: 700; font-family: inherit; font-size: 15px; }

.row-price-wrap { text-align: right; }
.price-each-desktop { font-size: 13px; color: #b5b5b5; margin-bottom: 4px; }
.price-line-total { font-size: 20px; font-weight: 800; color: var(--text-primary); }

/* Sidebar V2 */
.summary-card-v2 { background: #ffffff; border-radius: 24px; padding: 36px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); position: sticky; top: 120px; border: 1px solid rgba(0,0,0,0.04); }
.summary-title { font-size: 22px; font-weight: 800; margin-bottom: 32px; color: var(--text-primary); }
.total-big-row { display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1.5px solid #f0f0f0; margin-bottom: 36px; }
.total-value { font-weight: 800; font-size: 26px; color: var(--primary); }

.checkout-btn-premium { width: 100%; background: var(--primary); color: #fff !important; padding: 20px; border-radius: 16px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 12px; transition: all 0.3s; font-size: 17px; box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2); text-decoration: none; }

/* RESPONSIVE FIXES */
@media (max-width: 1024px) {
  .cart-main-grid { grid-template-columns: 1fr; gap: 48px; }
  .cart-summary-section { order: -1; }
  .summary-card-v2 { position: static; }
}

@media (max-width: 768px) {
  .cart-table-header { display: none; }
  .cart-row { grid-template-columns: 1fr; padding: 24px 0; border-bottom: 1.5px solid #f5f5f5; }
  .col-qty, .col-total { text-align: left; margin-left: 114px; } /* Align with text, not image */
  .product-img-link { width: 90px; height: 110px; }
  .product-item-box { align-items: flex-start; gap: 24px; }
  .product-price-mobile { display: block; font-size: 13px; color: #888; margin-bottom: 8px; }
  .price-each-desktop { display: none; }
  .price-line-total { text-align: left; font-size: 18px; margin-top: 8px; }
  .price-line-total::before { content: "Total: "; font-size: 13px; font-weight: 600; color: #888; }
  .quantity-control-v2 { margin: 0; }
  .cart-footer-actions { justify-content: center; }
}

@media (max-width: 480px) {
  .product-item-box { flex-direction: row; } /* Keep horizontal but stack info */
  .col-qty, .col-total { margin-left: 0; margin-top: 16px; }
  .cart-row { padding: 32px 0; }
  .product-name-link { font-size: 16px; }
  .summary-card-v2 { padding: 24px; }
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
