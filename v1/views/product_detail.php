<?php
// /products/{hash_id} or /products/{hash_id}/{slug}
$hash = $s2 ?? $uri[2] ?? '';

$websiteInfo = selectContent($conn, "settings_website_info", []);
$usdToggle   = isset($websiteInfo[0]['input_usd_toggle']) ? (int) $websiteInfo[0]['input_usd_toggle'] : 0;
$controller  = new ProductController($conn, $usdToggle === 1);
$details     = $controller->fetchProductDetailsByHashId($hash);

if (!$details) { include APP_PATH . "/views/404.php"; die; }

$page_title = htmlspecialchars($details["name"] ?? "", ENT_QUOTES, "UTF-8");
$metaDescription = previewBody($details["description"] ?? "", 30);
$bodyClass = "page-light-navbar";

$productCategoryName = $details["category_name"] ?? "";
$gallery = $details["images"] ?? [];
if (!empty($details["primary_image"])) {
    if (!in_array($details["primary_image"], $gallery)) {
        array_unshift($gallery, $details["primary_image"]);
    }
}
if (empty($gallery)) {
    $gallery = ["/assets/img/icons/cart.svg"];
}

// Variants from 'variants' table via ProductController
$variants = $details['variants'] ?? [];

// Base price
$basePrice = $usdToggle === 1 ? ($details['price_range_usd']['price'] ?? $details['price_range_usd']['min'] ?? 0) : ($details['price_range_ngn']['price'] ?? $details['price_range_ngn']['min'] ?? 0);

// Reviews
$reviews = $details['reviews'] ?? []; // ProductController might need to be updated to fetch reviews if not already
$avgRating = $details['input_rating'] ?? 4.5;

// Related products
$relatedOptions = [
    'category_id' => $details['select_product_category'] ?? null,
    'limit' => 4,
    'currency' => $usdToggle === 1 ? 'USD' : 'NGN'
];
$related = $controller->fetchProducts($relatedOptions);
$related = array_filter($related, fn($p) => $p["hash_id"] !== $hash);
$related = array_slice(array_values($related), 0, 3);

$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/>

<?php/*##cbcode_80001o##*?>
<div data-cbcodesection="cbcode_80001">
<section class="products hero-section section-0-120">
  <div class="container">
    <div class="product-single">
      <!-- Breadcrumb -->
      <div class="navigation" style="margin-bottom:28px;">
        <div class="breadcrumb-row">
          <a href="<?= $baseUrl ?>/">Home</a>
          <span class="breadcrumb-sep">/</span>
          <a href="<?= $baseUrl ?>/products">Products</a>
          <?php if (!empty($productCategoryName)): ?>
            <span class="breadcrumb-sep">/</span>
            <a href="<?= $baseUrl ?>/products?tab=<?= urlencode($productCategoryName) ?>">
              <?= htmlspecialchars($productCategoryName, ENT_QUOTES, "UTF-8") ?>
            </a>
          <?php endif; ?>
          <span class="breadcrumb-sep">/</span>
          <span><?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?></span>
        </div>
      </div>

      <div class="product-details">

        <!-- ── Gallery ─────────────────────────────────────── -->
        <div class="product-img-wrapper">
          <!-- Main image -->
          <div class="product-img-box"
               data-admc-image="panel_product"
               data-admc-id="<?= $details["id"] ?>">
            <img alt="<?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?>"
                 class="all-img zoom" loading="lazy"
                 id="mainProductImg"
                 src="<?= htmlspecialchars($gallery[0], ENT_QUOTES, "UTF-8") ?>">
          </div>
          <!-- Thumbnails — only shown when more than 1 image -->
          <?php if (count($gallery) > 1): ?>
            <div class="product-thumbs">
              <?php foreach ($gallery as $gi => $imgUrl): ?>
                <div class="product-thumb <?= $gi === 0 ? "active" : "" ?>"
                     data-src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, "UTF-8") ?>"
                     onclick="document.getElementById('mainProductImg').src=this.dataset.src;
                              document.querySelectorAll('.product-thumb').forEach(function(t){t.classList.remove('active')});
                              this.classList.add('active');">
                  <img alt="" loading="lazy"
                       src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, "UTF-8") ?>">
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- ── Product details panel ──────────────────────── -->
        <div class="product-details-right">
          <div class="product-details-top">
            <!-- Rating -->
            <div class="product-rating">
              <div class="product-rating-inner">
                <img alt="Star" class="star" src="/assets/img/icons/star.svg"
                     style="width:16px;height:16px;">
                <div class="tagline no-height">
                  <?= $avgRating ?>
                  <span class="color-gray">(<?= count($reviews) ?> Reviews)</span>
                </div>
              </div>
            </div>
            <!-- Name -->
            <h2 class="heading-02"
                data-admc-manage="panel_product"
                data-admc-id="<?= $details["id"] ?>">
              <?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?>
            </h2>
            <!-- Category tag -->
            <?php if (!empty($productCategoryName)): ?>
              <div class="tagline caps color-gray" style="margin-bottom:8px;">
                <?= htmlspecialchars($productCategoryName, ENT_QUOTES, "UTF-8") ?>
              </div>
            <?php endif; ?>
            <!-- Price -->
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
              <div class="heading-04" id="detailPrice"
                   data-admc-manage="panel_product"
                   data-admc-id="<?= $details["id"] ?>">
                <?= $sym ?><?= htmlspecialchars(number_format((float)$basePrice, 2), ENT_QUOTES, "UTF-8") ?>
              </div>
              <?php if (!empty($details["base_price_range_ngn"]) || !empty($details["base_price_range_usd"])): ?>
                <?php 
                   $compPrice = $usdToggle === 1 
                     ? ($details['base_price_range_usd']['price'] ?? $details['base_price_range_usd']['min'] ?? 0)
                     : ($details['base_price_range_ngn']['price'] ?? $details['base_price_range_ngn']['min'] ?? 0);
                ?>
                <?php if ($compPrice > $basePrice): ?>
                <div style="text-decoration:line-through;color:#b5b5b5;font-size:16px;">
                  <?= $sym ?><?= htmlspecialchars(number_format((float)$compPrice, 2), ENT_QUOTES, "UTF-8") ?>
                </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Short description -->
          <p class="p-01 color-gray" style="margin-bottom:24px;"
             data-admc-manage="panel_product"
             data-admc-id="<?= $details["id"] ?>">
            <?= previewBody($details["description"] ?? "", 40) ?>
          </p>

          <!-- Add to cart section (anchor for sticky bar) -->
          <div class="add-to-cart-section" id="addToCartSection">
            <!-- Variants -->
            <?php if (!empty($variants)): ?>
              <div class="modal-variants-label" style="text-transform:uppercase; font-weight:bold; margin-bottom:12px; font-size:12px; letter-spacing:1px; color:#000;">VARIANTS:</div>
              <?php 
              $grouped = [];
              foreach ($variants as $v) { 
                  $optName = !empty($v['options']) ? $v['options'][0]['option_name'] : 'Options';
                  $grouped[$optName][] = $v; 
              }
              foreach ($grouped as $name => $opts): ?>
                <div class="modal-variants" style="margin-bottom:16px;">
                  <div class="modal-variant-options">
                    <?php foreach ($opts as $vi => $v):
                      $vStock = (int)($v["inventory"] ?? 0);
                      $vPrice = $usdToggle === 1 ? $v['price_usd'] : $v['price_ngn'];
                      $vVal   = !empty($v['options']) ? $v['options'][0]['value_name'] : 'Default';
                    ?>
                      <button type="button"
                              class="modal-variant-btn variant-btn <?= $vStock <= 0 ? "out-of-stock" : "" ?>"
                              data-variant-id="<?= $v["id"] ?>"
                              data-value="<?= htmlspecialchars($vVal, ENT_QUOTES, "UTF-8") ?>"
                              data-price="<?= htmlspecialchars($vPrice, ENT_QUOTES, "UTF-8") ?>"
                              data-stock="<?= $vStock ?>">
                        <?= htmlspecialchars($vVal, ENT_QUOTES, "UTF-8") ?>
                        <?php if ($vStock <= 0): ?>
                          <span class="variant-stock-label out">Out of stock</span>
                        <?php elseif ($vStock <= 5): ?>
                          <span class="variant-stock-label low">Only <?= $vStock ?> left</span>
                        <?php endif; ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

            <!-- Quantity -->
            <div class="quantity-outer" style="margin-bottom:20px;">
              <label class="p-02 caps" style="display:block;margin-bottom:8px;">Quantity</label>
              <div class="quantity-wrap" style="display:flex;align-items:center;gap:16px;">
                <div class="qty-control">
                  <button type="button" class="qty-btn" data-action="decrease">−</button>
                  <input class="qty-input" id="detailQty" type="number" value="1" min="1">
                  <button type="button" class="qty-btn" data-action="increase">+</button>
                </div>
                <div class="heading-04" id="detailPriceDisplay">
                  <?= $sym ?><?= htmlspecialchars(number_format((float)$basePrice, 2), ENT_QUOTES, "UTF-8") ?>
                </div>
              </div>
            </div>

            <!-- Stock indicator -->
            <?php
              $stock = $details['base_inventory'] ?? 0;
              $hasVariants = !empty($variants);
            ?>
            <?php if (!$hasVariants): ?>
              <div class="product-stock-status" id="stockStatus" style="margin-bottom:16px;">
                <?php if ($stock <= 0): ?>
                  <span class="stock-badge stock-out">Out of stock</span>
                <?php elseif ($stock <= 5): ?>
                  <span class="stock-badge stock-low">Only <?= $stock ?> left in stock — order soon</span>
                <?php elseif ($stock <= 20): ?>
                  <span class="stock-badge stock-medium"><?= $stock ?> in stock</span>
                <?php else: ?>
                  <span class="stock-badge stock-high">In stock</span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <!-- Variant stock shown dynamically via JS when variant is selected -->
            <?php if ($hasVariants): ?>
              <div class="product-stock-status" id="stockStatus" style="margin-bottom:16px;display:none;"></div>
            <?php endif; ?>

            <!-- Add to cart + Wishlist -->
            <?php if ($stock > 0): ?>
              <div class="add-cart-btn" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;">
                <button type="button"
                        id="detailAddToCart"
                        class="btn-add-to-cart-main"
                        data-product-id="<?= $details["hash_id"] ?>">
                  Add to Cart
                </button>
                <button type="button" class="modal-wishlist-btn" id="detailWishlist"
                        data-id="<?= $details["hash_id"] ?>">
                  <img src="/assets/img/icons/heart-outline.svg" alt="Wishlist" id="detailWishlistImg">
                </button>
              </div>
            <?php else: ?>
              <div style="padding:14px 20px;background:#f6f6f6;border-radius:8px;color:#888;font-size:14px;margin-bottom:20px;">
                This product is currently out of stock.
              </div>
            <?php endif; ?>
          </div>

          <!-- Trust badges — text only, no image dependency -->
          <div style="display:flex;gap:24px;margin-top:20px;padding-top:20px;border-top:1px solid #dedede;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:#555;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Dermatologist Tested
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:#555;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              Cruelty Free
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:#555;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
              30-Day Returns
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</div>
<?php/*##cbcode_80001c##*/>

<!-- ── Product Tabs (Details / Ingredients / Reviews) ─────── -->
<?php/*##cbcode_80002o##*?>
<div data-cbcodesection="cbcode_80002">
<section class="products-info section-0-120">
  <div class="container">
    <div class="product-tabs w-tabs">

      <!-- Tab menu -->
      <div class="product-tab-menu w-tab-menu">
        <a class="product-tab-link w-inline-block w-tab-link w--current"
           data-tab="tab-details" data-tab-link="tab-details">
          <div class="product-tab-link-inner">
            <div class="p-02-medium">Details</div>
          </div>
        </a>
        <a class="product-tab-link w-inline-block w-tab-link"
           data-tab="tab-ingredients" data-tab-link="tab-ingredients">
          <div class="product-tab-link-inner">
            <div class="p-02-medium">Ingredients</div>
          </div>
        </a>
        <a class="product-tab-link w-inline-block w-tab-link"
           data-tab="tab-reviews" data-tab-link="tab-reviews">
          <div class="product-tab-link-inner">
            <div class="p-02-medium">Reviews (<?= count($reviews) ?>)</div>
          </div>
        </a>
      </div>

      <!-- Tab panes -->
      <div class="product-tab-content w-tab-content">

        <!-- Details tab -->
        <div class="w-tab-pane w--tab-active" data-tab-pane="tab-details">
          <div class="product-info-box">
            <div class="p-01 rich-text w-richtext"
                 data-admc-manage="panel_product"
                 data-admc-id="<?= $details["id"] ?>">
              <?= nl2br(htmlspecialchars($details["description"] ?? "", ENT_QUOTES, "UTF-8")) ?>
            </div>
          </div>
        </div>

        <!-- Ingredients tab -->
        <div class="w-tab-pane" data-tab-pane="tab-ingredients">
          <div class="product-info-box">
            <div class="p-01 rich-text w-richtext"
                 data-admc-manage="panel_product"
                 data-admc-id="<?= $details["id"] ?>">
              <?= nl2br(htmlspecialchars($details["text_ingredients"] ?? "Ingredient list not available.", ENT_QUOTES, "UTF-8")) ?>
            </div>
          </div>
        </div>

        <!-- Reviews tab -->
        <div class="w-tab-pane" data-tab-pane="tab-reviews">
          <div class="product-review">
            <div class="product-review-top-wrap">
              <div class="product-review-top">
                <div class="p-01-semibold">Reviews</div>
                <div class="review-total-wrap">
                  <div class="heading-03"><?= $avgRating ?></div>
                  <div class="rating-wrap">
                    <div class="star-wrap bit-space">
                      <?php for ($s = 0; $s < 5; $s++): ?>
                        <img alt="Star" class="star" src="/assets/img/icons/star.svg"
                             style="width:14px;height:14px;<?= $s >= round($avgRating) ? "opacity:0.3;" : "" ?>">
                      <?php endfor; ?>
                    </div>
                    <div class="tagline">(<?= count($reviews) ?> Reviews)</div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Review list — admin adds reviews via ADMC (pencil icon) -->
            <?php if (!empty($reviews)): ?>
              <div class="w-dyn-list"
                   data-admc-tb="read_reviews"
                   data-admc-tbadd="panel_product"
                   data-admc-tblink="<?= htmlspecialchars($details['hash_id'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="review-list w-dyn-items" role="list">
                  <?php foreach ($reviews as $rev): ?>
                    <div class="w-dyn-item" role="listitem">
                      <div class="review-card">
                        <!-- Avatar -->
                        <div class="review-name-tag"
                             data-admc-image="read_reviews"
                             data-admc-id="<?= $rev['id'] ?>"
                             style="overflow:hidden;border-radius:50%;flex-shrink:0;">
                          <?php if (!empty($rev['image_1'])): ?>
                            <img src="<?= htmlspecialchars($rev['image_1'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($rev['input_reviewer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                 style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%;">
                          <?php else: ?>
                            <div class="p-02" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                              <?= htmlspecialchars(strtoupper(substr($rev["input_reviewer_name"] ?? "A", 0, 2)), ENT_QUOTES, "UTF-8") ?>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="review-content">
                          <div class="review-card-top">
                            <div class="review-profile">
                              <div class="p-02-medium"
                                   data-admc-manage="read_reviews"
                                   data-admc-id="<?= $rev["id"] ?>">
                                <?= htmlspecialchars($rev["input_reviewer_name"] ?? "Customer", ENT_QUOTES, "UTF-8") ?>
                              </div>
                              <div class="color-gray">
                                <div class="tagline caps">
                                  <?= !empty($rev["date_created"]) ? decodeDate($rev["date_created"]) : "" ?>
                                </div>
                              </div>
                            </div>
                            <div class="rating-wrap">
                              <div class="star-wrap bit-space">
                                <?php for ($s = 0; $s < 5; $s++): ?>
                                  <img alt="Star" class="star"
                                       src="/assets/img/icons/star.svg"
                                       style="width:14px;height:14px;<?= $s >= (int)($rev["input_rating"] ?? 5) ? "opacity:0.3;" : "" ?>">
                                <?php endfor; ?>
                              </div>
                            </div>
                          </div>
                          <div class="color-gray">
                            <div class="p-01"
                                 data-admc-manage="read_reviews"
                                 data-admc-id="<?= $rev["id"] ?>">
                              <?= htmlspecialchars($rev["text_review"] ?? "", ENT_QUOTES, "UTF-8") ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php else: ?>
              <!-- Empty state -->
              <div class="w-dyn-list"
                   data-admc-tb="read_reviews"
                   data-admc-tbadd="panel_product"
                   data-admc-tblink="<?= htmlspecialchars($details['hash_id'], ENT_QUOTES, 'UTF-8') ?>"
                   style="padding:32px 0;color:#b5b5b5;font-size:14px;">
                No reviews yet.
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
</div>
<?php/*##cbcode_80002c##*/>

<!-- ── Related Products ───────────────────────────────────── -->
<?php/*##cbcode_80003o##*?>
<div data-cbcodesection="cbcode_80003">
<?php if (!empty($related)): ?>
<section class="products section-0-120">
  <div class="container">
    <div class="products-inner">
      <h2 class="heading-02 reveal">You might also like</h2>
      <div class="product-grid w-dyn-items" role="list">
        <?php foreach ($related as $rel):
          $relUrl = $baseUrl . "/products/" . $rel["hash_id"] . "/" . cleans($rel["name"]);
          $relPrice = $usdToggle === 1 ? ($rel['price_range_usd']['price'] ?? $rel['price_range_usd']['min'] ?? 0) : ($rel['price_range_ngn']['price'] ?? $rel['price_range_ngn']['min'] ?? 0);
        ?>
          <div class="w-dyn-item product-card-wrap reveal" role="listitem">
            <div class="product-card" style="position:relative;">
              <button class="quick-view-btn" data-id="<?= $rel["hash_id"] ?>" 
                      onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.openQuickView('<?= $rel["hash_id"] ?>');" aria-label="Quick view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
              <a class="product-link w-inline-block" href="<?= $relUrl ?>">
                <div class="product-card-img"
                     data-admc-image="panel_product"
                     data-admc-id="<?= $rel["id"] ?>">
                  <img alt="<?= htmlspecialchars($rel["name"], ENT_QUOTES, "UTF-8") ?>"
                       class="all-img" loading="lazy"
                       src="<?= htmlspecialchars($rel["primary_image"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>">
                  <div class="add-to-card-02" data-product-id="<?= $rel["hash_id"] ?>"
                       onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.cartAddItem('<?= $rel["hash_id"] ?>', '', 1);">
                    <img alt="" class="add-to-card-icon" src="/assets/img/icons/cart-add.svg">
                    <div class="p-01">Add to cart</div>
                  </div>
                </div>
                <div class="product-card-bottom">
                  <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($rel["category_name"] ?? "", ENT_QUOTES, "UTF-8") ?></div></div>
                  <div class="product-name-price">
                    <div class="heading-06"><?= htmlspecialchars($rel["name"], ENT_QUOTES, "UTF-8") ?></div>
                    <div class="heading-07"><?= $sym ?><?= number_format((float)$relPrice, 2) ?></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
</div>
<?php/*##cbcode_80003c##*/>

<?php/*##cb1c##*/>
</div>

<!-- Sticky add-to-cart bar -->
<div class="sticky-cart-bar" id="stickyCartBar">
  <img src="<?= htmlspecialchars($gallery[0] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
       class="sticky-cart-product-img"
       alt="<?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?>">
  <span class="sticky-cart-product-name">
    <?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?>
  </span>
  <span class="sticky-cart-price"><?= $sym ?><?= number_format((float)$basePrice, 2) ?></span>
  <button class="sticky-cart-btn"
          data-product-id="<?= $details["hash_id"] ?>">Add to Cart</button>
</div>

<script>
// Tab switching
document.querySelectorAll("[data-tab-link]").forEach(function(tab) {
  tab.addEventListener("click", function() {
    var target = tab.dataset.tabLink;
    document.querySelectorAll(".product-tab-link").forEach(function(t) { t.classList.remove("w--current"); });
    document.querySelectorAll(".w-tab-pane").forEach(function(p) { p.classList.remove("w--tab-active"); });
    tab.classList.add("w--current");
    var pane = document.querySelector("[data-tab-pane=\"" + target + "\"]");
    if (pane) pane.classList.add("w--tab-active");
  });
});

// Gallery
document.querySelectorAll(".product-gallery-thumb").forEach(function(thumb) {
  thumb.addEventListener("click", function() {
    var main = document.getElementById("mainProductImg");
    if (main) main.src = thumb.dataset.src;
    document.querySelectorAll(".product-gallery-thumb").forEach(function(t) { t.classList.remove("active"); });
    thumb.classList.add("active");
  });
});

// Sticky cart scroll
var addToCartSection = document.getElementById("addToCartSection");
if (addToCartSection) {
  new IntersectionObserver(function(entries) {
    document.getElementById("stickyCartBar").classList.toggle("visible", !entries[0].isIntersecting);
  }, { threshold: 0 }).observe(addToCartSection);
}

// Variant selection update
document.querySelectorAll(".variant-btn").forEach(function(btn) {
  btn.addEventListener("click", function() {
    if (btn.classList.contains("out-of-stock")) return;
    var parent = btn.closest(".modal-variant-options");
    var wasActive = btn.classList.contains("active");

    parent.querySelectorAll(".variant-btn").forEach(function(b) { b.classList.remove("active"); });
    
    var priceEl = document.getElementById("detailPrice");
    var sym = window.VENORA_CURRENCY_SYMBOL || "$";
    var basePrice = parseFloat("<?= $basePrice ?>") || 0;

    if (!wasActive) {
      btn.classList.add("active");
      if (btn.dataset.price && priceEl) {
        priceEl.textContent = sym + parseFloat(btn.dataset.price).toFixed(2);
      }
      // Update stock status
      var stockEl = document.getElementById("stockStatus");
      if (stockEl) {
        var s = parseInt(btn.dataset.stock) || 0;
        stockEl.style.display = "block";
        if (s <= 0) {
          stockEl.innerHTML = '<span class="stock-badge stock-out">Out of stock</span>';
        } else if (s <= 5) {
          stockEl.innerHTML = '<span class="stock-badge stock-low">Only ' + s + ' left in stock — order soon</span>';
        } else if (s <= 20) {
          stockEl.innerHTML = '<span class="stock-badge stock-medium">' + s + ' in stock</span>';
        } else {
          stockEl.innerHTML = '<span class="stock-badge stock-high">In stock</span>';
        }
      }
    } else {
      btn.classList.remove("active");
      if (priceEl) priceEl.textContent = sym + basePrice.toFixed(2);
      var stockEl2 = document.getElementById("stockStatus");
      if (stockEl2) stockEl2.style.display = "none";
    }
  });
});

// Helper to get active variants
function getSelectedVariantIds() {
  var ids = [];
  var totalGroups = document.querySelectorAll(".modal-variant-options").length;
  var activeButtons = document.querySelectorAll(".variant-btn.active");
  
  if (totalGroups > 0 && activeButtons.length < totalGroups) {
      if(window.Venora) window.Venora.showToast("Please select all options (Size, Color, etc.)", "error");
      return false;
  }

  activeButtons.forEach(function(btn) {
    ids.push(btn.dataset.variantId);
  });
  return ids.join(",");
}

// Quantity buttons — handled here, venora-app.js handles the add-to-cart click
document.querySelectorAll(".qty-btn").forEach(function(btn) {
  btn.addEventListener("click", function() {
    var input = document.getElementById("detailQty");
    if (!input) return;
    var val = parseInt(input.value) || 1;
    if (btn.dataset.action === "increase") {
      input.value = val + 1;
    } else if (btn.dataset.action === "decrease" && val > 1) {
      input.value = val - 1;
    }
    // Update price display
    var priceDisplay = document.getElementById("detailPriceDisplay");
    var basePrice = parseFloat("<?= $basePrice ?>") || 0;
    var sym = window.VENORA_CURRENCY_SYMBOL || "$";
    if (priceDisplay) priceDisplay.textContent = sym + (basePrice * parseInt(input.value)).toFixed(2);
  });
});

// Sticky cart button
document.querySelector(".sticky-cart-btn") && document.querySelector(".sticky-cart-btn").addEventListener("click", function() {
  var btn = this;
  var productId = btn.dataset.productId;

  var variantGroups = document.querySelectorAll(".modal-variant-options");
  var activeButtons = document.querySelectorAll(".variant-btn.active");

  if (variantGroups.length > 0 && activeButtons.length < variantGroups.length) {
    var section = document.getElementById("addToCartSection");
    if (section) {
      section.scrollIntoView({ behavior: "smooth", block: "center" });
      document.querySelectorAll(".modal-variants-label").forEach(function(el) {
        el.style.transition = "color 0.3s, transform 0.3s";
        el.style.color = "#c1121f";
        el.style.transform = "scale(1.05)";
        setTimeout(function() { 
          el.style.color = ""; 
          el.style.transform = "";
        }, 1500);
      });
    }
    return;
  }

  var variantId = variantGroups.length === 0 ? "" : Array.from(activeButtons).map(function(b) { return b.dataset.variantId; }).join(",");
  var qty = parseInt((document.getElementById("detailQty") || {}).value) || 1;
  btn.textContent = "Adding...";
  btn.disabled = true;
  window.Venora.cartAddItem(productId, variantId, qty, function() {
    btn.textContent = "Added!";
    btn.style.background = "#2d6a4f";
    setTimeout(function() {
      btn.textContent = "Add to Cart";
      btn.style.background = "";
      btn.disabled = false;
    }, 2000);
  });
});

</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
