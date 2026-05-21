<?php
$page_title = "My Wishlist";
$bodyClass  = "page-light-navbar";

// ── Fetch wishlist items ──────────────────────────────────────
$wishlistIds = $_SESSION['wishlist'] ?? [];
$wishlistProducts = [];

if (!empty($wishlistIds)) {
    $allP = selectContent($conn, "panel_product", ["visibility" => "show"]);
    // Pre-index variant prices
    $_wVars = selectContent($conn, "variants", []);
    $_wPriceIdx = [];
    foreach ($_wVars as $_v) {
        $h = $_v['product_hash_id'];
        if (!isset($_wPriceIdx[$h])) $_wPriceIdx[$h] = $usdEnabled ? (float)$_v['input_price_usd'] : (float)$_v['input_price_ngn'];
    }
    foreach ($allP as $p) {
        if (in_array($p['hash_id'], $wishlistIds)) {
            $p['input_price'] = $_wPriceIdx[$p['hash_id']] ?? 0;
            $wishlistProducts[] = $p;
        }
    }
}

$sym = $shop_symbol ?? "$";
$addToCartIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg";

include APP_PATH . "/views/includes/header.php";
?>

<!-- navbar clearance -->
<div style="height:100px;background:#ffffff;"></div>
<div style="background:#ffffff; padding-bottom:40px;">
    <div class="container">

      <!-- Page header — plain div, NOT .header (that class is flex space-between) -->
      <div style="text-align:left; margin-bottom:48px; padding-bottom:24px; border-bottom:1px solid #ececec;">
        <h1 class="heading-02" style="color:var(--dark-green-colour,#072708); margin:0 0 8px;">My Wishlist</h1>
        <p class="p-01 color-gray" style="margin:0;">Items you've saved for later</p>
      </div>

      <?php if (empty($wishlistProducts)): ?>
        <div style="text-align:center; padding:80px 0;">
          <div style="font-size:56px; margin-bottom:20px;">🤍</div>
          <h3 class="heading-05" style="margin-bottom:10px;">Your wishlist is empty</h3>
          <p class="p-01 color-gray" style="margin-bottom:32px;">Browse our collection and save your favourites!</p>
          <a href="<?= $baseUrl ?>/products"
             style="display:inline-block; padding:14px 40px; background:var(--dark-green-colour,#072708);
                    color:#fff; border-radius:100px; font-size:15px; font-weight:600;
                    text-decoration:none; font-family:inherit; transition:background 0.2s;">
            Start Shopping
          </a>
        </div>
      <?php else: ?>
        <div class="product-collection">
          <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($wishlistProducts as $product): 
              $detailUrl = $baseUrl . "/products/" . $product["hash_id"] . "/" . cleans($product["input_product_name"]);
              
              // Check for variants
              $vars = selectContentAsc($conn, "variants", ["product_hash_id" => $product['hash_id']], "id", 1);
              $hasVariants = !empty($vars) ? "true" : "false";
            ?>
              <div class="product-card-wrap">
                <div class="product-card" style="position:relative;">
                  
                  <!-- Remove from wishlist (Permanently visible on this page) -->
                  <button class="wishlist-btn-card active" data-id="<?= $product['hash_id'] ?>" 
                          onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.toggleWishlist('<?= $product['hash_id'] ?>', this).then(() => location.reload());"
                          style="position:absolute; top:12px; left:12px; z-index:20; background:white; border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.1); opacity: 1 !important; transform: scale(1) !important;">
                    <img src="<?= $baseUrl ?>/assets/img/icons/heart-filled.svg" style="width:18px; height:18px;" alt="Remove">
                  </button>

                  <button class="quick-view-btn" data-id="<?= $product['hash_id'] ?>" 
                          onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.openQuickView('<?= $product['hash_id'] ?>');" aria-label="Quick view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>

                  <a class="product-link w-inline-block" href="<?= $detailUrl ?>">
                    <div class="product-card-img">
                      <img alt="<?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?>" 
                           class="all-img" loading="lazy" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      
                      <div class="add-to-card-02" 
                           data-product-id="<?= $product['hash_id'] ?>" 
                           data-has-variants="<?= $hasVariants ?>"
                           onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.cartAddItem('<?= $product['hash_id'] ?>', '', 1, null, this);">
                        <img alt="" class="add-to-card-icon" src="<?= $addToCartIcon ?>">
                        <div class="p-01">Add to cart</div>
                      </div>
                    </div>
                    <div class="product-card-bottom">
                      <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($product['select_category'] ?? "Skincare", ENT_QUOTES, 'UTF-8') ?></div></div>
                      <div class="product-name-price">
                        <div class="heading-06"><?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="heading-07"><?= formatPrice($product['input_price'], $sym) ?></div>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php include APP_PATH . "/views/includes/footer.php"; ?>