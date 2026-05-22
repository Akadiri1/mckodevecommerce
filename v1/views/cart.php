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
      <p class="p-01"><?= $totalQty ?> item<?= $totalQty !== 1 ? 's' : '' ?> in your bag</p>
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
                      <div class="product-unit-price"><?= formatPrice($price, $sym) ?></div>
                      
                      <!-- Mobile quantity + total (shown only on small screens) -->
                      <div class="cart-row-mobile-actions">
                        <div class="quantity-control-v2">
                          <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', Math.max(1, parseInt(document.getElementById('qty-<?= $rowId ?>').value)-1))" aria-label="Decrease quantity">−</button>
                          <input class="q-input" id="qty-<?= $rowId ?>" type="number" value="<?= (int)$item["quantity"] ?>" min="1" readonly>
                          <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', parseInt(document.getElementById('qty-<?= $rowId ?>').value)+1)" aria-label="Increase quantity">+</button>
                        </div>
                        <div class="mobile-line-total"><?= formatPrice($lineTotal, $sym) ?></div>
                      </div>
                      
                      <button class="remove-item-btn" onclick="doCartRemove('<?= $rowId ?>')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                        <span>Remove</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Quantity Column (desktop) -->
                <div class="col-qty col-desktop-only">
                  <div class="quantity-control-v2">
                    <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', Math.max(1, parseInt(document.getElementById('qty-d-<?= $rowId ?>').value)-1))" aria-label="Decrease quantity">−</button>
                    <input class="q-input" id="qty-d-<?= $rowId ?>" type="number" value="<?= (int)$item["quantity"] ?>" min="1" readonly>
                    <button class="q-btn" onclick="doCartUpdate('<?= $rowId ?>', parseInt(document.getElementById('qty-d-<?= $rowId ?>').value)+1)" aria-label="Increase quantity">+</button>
                  </div>
                </div>

                <!-- Total Column (desktop) -->
                <div class="col-total col-desktop-only">
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

            <a href="<?= $baseUrl ?>/checkout" class="checkout-btn-premium" id="cart-checkout-btn">
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
/* ================================================================
   CART PAGE — Fully Responsive Redesign
   ================================================================ */

/* --- Page Wrapper --- */
.cart-page-wrapper {
  padding: 40px 0 120px;
  background: #ffffff;
  min-height: 70vh;
}

/* --- Header --- */
.cart-header-main {
  margin-bottom: 48px;
  border-bottom: 1px solid rgba(0,0,0,0.06);
  padding-bottom: 24px;
}
.cart-header-main h1 { margin-bottom: 4px; }
.cart-header-main p { color: var(--text-secondary); font-size: 15px; }

/* --- Empty State --- */
.empty-cart-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-icon {
  font-size: 64px;
  margin-bottom: 24px;
  animation: emptyBounce 2s ease-in-out infinite;
}
@keyframes emptyBounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.empty-cart-state h2 { margin-bottom: 8px; }
.empty-cart-state p { margin-bottom: 32px; color: var(--text-secondary); }

/* ================================================================
   MAIN GRID — Desktop: 2-column, Tablet+Mobile: 1-column
   ================================================================ */
.cart-main-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 60px;
  align-items: flex-start;
}

/* ================================================================
   TABLE HEADER (Desktop only)
   ================================================================ */
.cart-table-header {
  display: grid;
  grid-template-columns: 1fr 140px 160px;
  padding: 0 0 16px;
  border-bottom: 1px solid rgba(0,0,0,0.1);
  color: var(--text-secondary);
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

/* ================================================================
   CART ROW — Desktop: 3-column grid, Mobile: stacked card
   ================================================================ */
.cart-items-list { margin-bottom: 32px; }

.cart-row {
  display: grid;
  grid-template-columns: 1fr 140px 160px;
  padding: 28px 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  align-items: center;
  transition: background 0.25s ease;
}
.cart-row:hover {
  background: rgba(0,0,0,0.008);
  border-radius: 12px;
}

/* --- Product Column --- */
.product-item-box {
  display: flex;
  gap: 20px;
  align-items: center;
}
.product-img-link {
  width: 100px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  background: #f5f5f5;
  flex-shrink: 0;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-img-link:hover {
  transform: scale(1.03);
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
.product-img-link img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info-box { flex: 1; min-width: 0; }

.product-name-link {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
  text-decoration: none;
  display: block;
  margin-bottom: 4px;
  line-height: 1.35;
  transition: color 0.2s;
}
.product-name-link:hover { color: var(--primary); }

.product-variants-text {
  font-size: 13px;
  color: var(--text-secondary);
  margin-bottom: 6px;
  line-height: 1.4;
}

.product-unit-price {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 10px;
}

/* Mobile actions — hidden on desktop, visible on mobile */
.cart-row-mobile-actions {
  display: none;
}

.remove-item-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  color: #ef4444;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  padding: 6px 0;
  opacity: 0.6;
  transition: opacity 0.2s;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.remove-item-btn:hover { opacity: 1; }

/* --- Quantity Column --- */
.quantity-control-v2 {
  display: inline-flex;
  align-items: center;
  border: 1.5px solid #e8e8e8;
  border-radius: 10px;
  background: #fff;
  overflow: hidden;
  margin: 0 auto;
  transition: border-color 0.2s;
}
.quantity-control-v2:hover { border-color: #ccc; }

.q-btn {
  width: 38px;
  height: 42px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 18px;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}
.q-btn:hover { background: #f5f5f5; color: var(--text-primary); }
.q-btn:active { background: #eee; }

.q-input {
  width: 44px;
  border: none;
  text-align: center;
  font-weight: 700;
  font-family: inherit;
  font-size: 15px;
  background: transparent;
  -moz-appearance: textfield;
}
.q-input::-webkit-outer-spin-button,
.q-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

/* --- Total Column --- */
.row-price-wrap { text-align: right; }
.price-each-desktop { font-size: 13px; color: #b5b5b5; margin-bottom: 4px; }
.price-line-total { font-size: 20px; font-weight: 800; color: var(--text-primary); }

/* --- Footer Actions --- */
.cart-footer-actions {
  display: flex;
  align-items: center;
  padding-top: 16px;
}
.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--text-secondary);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s, gap 0.2s;
}
.back-link:hover { color: var(--text-primary); gap: 10px; }

/* ================================================================
   SUMMARY SIDEBAR
   ================================================================ */
.summary-card-v2 {
  background: #ffffff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.04), 0 1px 4px rgba(0,0,0,0.02);
  position: sticky;
  top: 120px;
  border: 1px solid rgba(0,0,0,0.05);
  transition: box-shadow 0.3s ease;
}
.summary-card-v2:hover {
  box-shadow: 0 8px 40px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.03);
}

.summary-title {
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 28px;
  color: var(--text-primary);
  letter-spacing: -0.01em;
}

.summary-detail-rows {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-bottom: 20px;
}

.s-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.s-label {
  font-size: 14px;
  color: var(--text-secondary);
  font-weight: 500;
}
.s-value {
  font-size: 14px;
  font-weight: 700;
  color: var(--text-primary);
}

/* --- Free Shipping Meter --- */
.free-ship-meter {
  background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 20px;
  border: 1px solid rgba(22,163,74,0.1);
}
.meter-text {
  font-size: 13px;
  color: #166534;
  margin-bottom: 10px;
  line-height: 1.4;
}
.meter-bar {
  height: 6px;
  background: #dcfce7;
  border-radius: 3px;
  overflow: hidden;
}
.meter-fill {
  height: 100%;
  background: linear-gradient(90deg, #22c55e, #16a34a);
  border-radius: 3px;
  transition: width 0.5s ease;
}

/* --- Total Row --- */
.total-big-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  border-top: 1.5px solid #f0f0f0;
  margin-bottom: 28px;
}
.total-label {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
}
.total-value {
  font-weight: 800;
  font-size: 24px;
  color: var(--primary);
  letter-spacing: -0.02em;
}

/* --- Checkout Button --- */
.checkout-btn-premium {
  width: 100%;
  background: var(--primary);
  color: #fff !important;
  padding: 18px 24px;
  border-radius: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s ease;
  font-size: 16px;
  box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.15);
  text-decoration: none;
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.checkout-btn-premium:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.25);
}
.checkout-btn-premium:active {
  transform: translateY(0);
}

/* --- Secure Badge --- */
.secure-badges {
  margin-top: 16px;
  text-align: center;
}
.s-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--text-secondary);
  font-weight: 500;
  opacity: 0.7;
}

/* ================================================================
   RESPONSIVE: Tablet (≤ 1100px) — Stack to single column
   ================================================================ */
@media (max-width: 1100px) {
  .cart-main-grid {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  .cart-summary-section {
    order: -1;
  }
  .summary-card-v2 {
    position: static;
    max-width: 100%;
  }
}

/* ================================================================
   RESPONSIVE: Mobile (≤ 768px) — Card-based items
   ================================================================ */
@media (max-width: 768px) {
  .cart-page-wrapper {
    padding: 24px 0 100px;
  }

  .cart-header-main {
    margin-bottom: 28px;
    padding-bottom: 16px;
  }
  .cart-header-main h1 { font-size: 28px; }
  
  /* Hide desktop table header */
  .cart-table-header { display: none; }
  
  /* Hide desktop-only quantity & total columns */
  .col-desktop-only { display: none !important; }
  
  /* Switch cart row to single column (card-style) */
  .cart-row {
    display: block;
    padding: 20px 0;
    border-bottom: 1px solid rgba(0,0,0,0.06);
  }
  .cart-row:hover { background: transparent; }
  
  /* Product layout */
  .product-item-box {
    gap: 16px;
    align-items: flex-start;
  }
  .product-img-link {
    width: 90px;
    height: 110px;
    border-radius: 10px;
  }
  
  .product-name-link { font-size: 15px; }
  .product-unit-price { font-size: 13px; margin-bottom: 12px; }
  
  /* Show mobile actions row */
  .cart-row-mobile-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 8px;
  }
  
  .mobile-line-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-primary);
    white-space: nowrap;
  }
  
  /* Quantity control — smaller for mobile */
  .quantity-control-v2 { margin: 0; }
  .q-btn { width: 34px; height: 38px; }
  .q-input { width: 38px; font-size: 14px; }
  
  /* Summary card adjustments */
  .summary-card-v2 { padding: 24px; border-radius: 16px; }
  .summary-title { font-size: 18px; margin-bottom: 20px; }
  .total-value { font-size: 22px; }
  .checkout-btn-premium { padding: 16px 20px; font-size: 15px; border-radius: 12px; }
  
  /* Footer actions */
  .cart-footer-actions { justify-content: center; }
}

/* ================================================================
   RESPONSIVE: Small Mobile (≤ 480px)
   ================================================================ */
@media (max-width: 480px) {
  .cart-page-wrapper {
    padding: 16px 0 80px;
  }
  
  .cart-header-main {
    margin-bottom: 20px;
    padding-bottom: 12px;
  }
  .cart-header-main h1 { font-size: 24px; }
  .cart-header-main p { font-size: 13px; }
  
  .product-item-box { gap: 12px; }
  .product-img-link {
    width: 76px;
    height: 95px;
    border-radius: 8px;
  }
  
  .product-name-link { font-size: 14px; }
  .product-variants-text { font-size: 12px; }
  .product-unit-price { font-size: 12px; margin-bottom: 10px; }
  
  .cart-row-mobile-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .mobile-line-total { font-size: 16px; }
  
  .q-btn { width: 32px; height: 36px; font-size: 16px; }
  .q-input { width: 34px; font-size: 13px; }
  
  .summary-card-v2 { padding: 20px; }
  .summary-title { font-size: 17px; margin-bottom: 16px; }
  .s-label, .s-value { font-size: 13px; }
  .total-label { font-size: 14px; }
  .total-value { font-size: 20px; }
  .total-big-row { margin-bottom: 20px; }
  .checkout-btn-premium { padding: 14px 16px; font-size: 14px; }
  
  .empty-cart-state { padding: 60px 16px; }
  .empty-icon { font-size: 48px; }
}

/* ================================================================
   RESPONSIVE: Large Desktop (≥ 1400px) — wider layout
   ================================================================ */
@media (min-width: 1400px) {
  .cart-main-grid {
    grid-template-columns: 1fr 400px;
    gap: 80px;
  }
}

/* ================================================================
   ANIMATION — fade-in on page load
   ================================================================ */
.cart-row {
  animation: cartRowFadeIn 0.4s ease both;
}
.cart-row:nth-child(1) { animation-delay: 0.05s; }
.cart-row:nth-child(2) { animation-delay: 0.1s; }
.cart-row:nth-child(3) { animation-delay: 0.15s; }
.cart-row:nth-child(4) { animation-delay: 0.2s; }
.cart-row:nth-child(5) { animation-delay: 0.25s; }

@keyframes cartRowFadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.summary-card-v2 {
  animation: summarySlideIn 0.5s ease 0.15s both;
}
@keyframes summarySlideIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
var _cartBase = (typeof window.VENORA_BASE_URL !== 'undefined' ? window.VENORA_BASE_URL : '') || '';
function doCartUpdate(cartId, qty) {
  fetch(_cartBase + '/cart-update', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({cart_id: cartId, quantity: parseInt(qty)})
  }).then(function(r){ return r.json(); }).then(function(res){ 
      if (res.success) {
          location.reload(); 
      } else if (window.Venora) {
          window.Venora.showToast(res.error || 'Update failed', 'error');
          // Optional: slight delay before reload to let user read toast
          setTimeout(function() { location.reload(); }, 2000);
      } else {
          alert(res.error || 'Update failed');
          location.reload();
      }
  });
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
