<?php
// /products/{hash_id} or /products/{hash_id}/{slug}
$hash = $uri[2] ?? "";
$productArr = selectContent($conn, "panel_products", ["hash_id" => $hash, "visibility" => "show"]);
if (empty($productArr)) { include APP_PATH . "/views/404.php"; die; }
$product = $productArr[0];

$page_title = htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8");
$metaDescription = previewBody($product["text_description"] ?? "", 30);
$bodyClass = "page-light-navbar";

// Gallery images (addition_product_images pre-indexed)
$galleryRaw = selectContent($conn, "addition_product_images", ["tb_link" => $product["hash_id"], "visibility" => "show"]);
$gallery = array_merge(
    array_filter([$product["image_1"] ?? null]),
    array_filter([$product["image_2"] ?? null]),
    array_column($galleryRaw, "image_1")
);
$gallery = array_values(array_unique(array_filter($gallery)));
if (empty($gallery)) $gallery = ["/assets/img/icons/cart.svg"];

// Variants
$variants = selectContentAsc($conn, "addition_product_variants", ["tb_link" => $product["hash_id"], "visibility" => "show"], "input_order", 20);

// Reviews
$reviews = selectContentDesc($conn, "read_reviews", ["input_product_id" => $hash, "visibility" => "show"], "id", 20);
$avgRating = !empty($reviews) ? round(array_sum(array_column($reviews, "input_rating")) / count($reviews), 1) : (float)($product["input_rating"] ?? 4.5);

// Related products (same category, exclude current)
$related = selectContentDesc($conn, "panel_products", ["visibility" => "show", "select_category" => $product["select_category"] ?? ""], "id", 4);
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
          <?php if (!empty($product["select_category"])): ?>
            <span class="breadcrumb-sep">/</span>
            <a href="<?= $baseUrl ?>/products?category=<?= urlencode($product["select_category"]) ?>">
              <?= htmlspecialchars($product["select_category"], ENT_QUOTES, "UTF-8") ?>
            </a>
          <?php endif; ?>
          <span class="breadcrumb-sep">/</span>
          <span><?= htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8") ?></span>
        </div>
      </div>

      <div class="product-details">

        <!-- ── Gallery ─────────────────────────────────────── -->
        <div class="product-img-wrapper">
          <!-- Main image -->
          <div class="product-img-box"
               data-admc-image="panel_products"
               data-admc-id="<?= $product["id"] ?>">
            <img alt="<?= htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8") ?>"
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
                data-admc-manage="panel_products"
                data-admc-id="<?= $product["id"] ?>">
              <?= htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8") ?>
            </h2>
            <!-- Category tag -->
            <?php if (!empty($product["select_category"])): ?>
              <div class="tagline caps color-gray" style="margin-bottom:8px;">
                <?= htmlspecialchars($product["select_category"], ENT_QUOTES, "UTF-8") ?>
              </div>
            <?php endif; ?>
            <!-- Price -->
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
              <div class="heading-04" id="detailPrice"
                   data-admc-manage="panel_products"
                   data-admc-id="<?= $product["id"] ?>">
                <?= $sym ?><?= htmlspecialchars(number_format((float)$product["input_price"], 2), ENT_QUOTES, "UTF-8") ?>
              </div>
              <?php if (!empty($product["input_compare_price"])): ?>
                <div style="text-decoration:line-through;color:#b5b5b5;font-size:16px;">
                  <?= $sym ?><?= htmlspecialchars(number_format((float)$product["input_compare_price"], 2), ENT_QUOTES, "UTF-8") ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Short description -->
          <p class="p-01 color-gray" style="margin-bottom:24px;"
             data-admc-manage="panel_products"
             data-admc-id="<?= $product["id"] ?>">
            <?= previewBody($product["text_description"] ?? "", 40) ?>
          </p>

          <!-- Add to cart section (anchor for sticky bar) -->
          <div class="add-to-cart-section" id="addToCartSection">
            <!-- Variants -->
            <?php if (!empty($variants)): 
              $grouped = [];
              foreach ($variants as $v) { $grouped[$v['input_name']][] = $v; }
              foreach ($grouped as $name => $opts): ?>
                <div class="modal-variants" style="margin-bottom:20px;">
                  <div class="modal-variants-label" style="text-transform:uppercase; font-weight:bold; margin-bottom:10px;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>:</div>
                  <div class="modal-variant-options">
                    <?php foreach ($opts as $vi => $v):
                      $vStock = (int)($v["input_stock"] ?? 1);
                    ?>
                      <button type="button"
                              class="modal-variant-btn variant-btn <?= $vStock <= 0 ? "out-of-stock" : "" ?>"
                              data-variant-id="<?= $v["hash_id"] ?>"
                              data-value="<?= htmlspecialchars($v["input_value"], ENT_QUOTES, "UTF-8") ?>"
                              data-price="<?= htmlspecialchars($v["input_price"] ?? $product["input_price"], ENT_QUOTES, "UTF-8") ?>"
                              data-stock="<?= $vStock ?>">
                        <?= htmlspecialchars($v["input_value"], ENT_QUOTES, "UTF-8") ?>
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
                  <?= $sym ?><?= htmlspecialchars(number_format((float)$product["input_price"], 2), ENT_QUOTES, "UTF-8") ?>
                </div>
              </div>
            </div>

            <!-- Stock indicator -->
            <?php
              $stock = (int)($product["input_stock"] ?? 99);
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
            <?php if ((int)($product["input_stock"] ?? 1) > 0): ?>
              <div class="add-cart-btn" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;">
                <button type="button"
                        id="detailAddToCart"
                        class="btn-add-to-cart-main"
                        data-product-id="<?= $product["hash_id"] ?>">
                  Add to Cart
                </button>
                <button type="button" class="modal-wishlist-btn" id="detailWishlist"
                        data-id="<?= $product["hash_id"] ?>">
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
                 data-admc-manage="panel_products"
                 data-admc-id="<?= $product["id"] ?>">
              <?= nl2br(htmlspecialchars($product["text_description"] ?? "", ENT_QUOTES, "UTF-8")) ?>
            </div>
          </div>
        </div>

        <!-- Ingredients tab -->
        <div class="w-tab-pane" data-tab-pane="tab-ingredients">
          <div class="product-info-box">
            <div class="p-01 rich-text w-richtext"
                 data-admc-manage="panel_products"
                 data-admc-id="<?= $product["id"] ?>">
              <?= nl2br(htmlspecialchars($product["text_ingredients"] ?? "Ingredient list not available.", ENT_QUOTES, "UTF-8")) ?>
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

              <!-- Write review button — only if logged in -->
              <?php if ($isCustomerLoggedIn): ?>
                <button class="btn-02-link w-inline-block" id="showReviewForm"
                        style="background:none;border:1.5px solid #072708;padding:10px 20px;cursor:pointer;font-size:13px;font-weight:600;color:#072708;border-radius:4px;">
                  Write a Review
                </button>
              <?php else: ?>
                <a href="<?= $baseUrl ?>/customer-login"
                   style="display:inline-block;border:1.5px solid #072708;padding:10px 20px;font-size:13px;font-weight:600;color:#072708;border-radius:4px;text-decoration:none;">
                  Sign in to write a review
                </a>
              <?php endif; ?>
            </div>

            <!-- Review form — only shown to logged-in customers -->
            <?php if ($isCustomerLoggedIn): ?>
            <div class="review-form-card" id="reviewFormCard" style="display:none;">
              <div class="review-form-title">Share your experience</div>
              <div id="reviewFormError"
                   style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:10px 14px;border-radius:6px;font-size:13px;margin-bottom:14px;">
              </div>
              <form id="reviewForm" onsubmit="submitReview(event)">
                <input type="hidden" name="product_id" value="<?= $product["hash_id"] ?>">
                <!-- Star rating -->
                <div style="margin-bottom:14px;">
                  <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Rating *</label>
                  <div class="star-rating-input" style="flex-direction:row-reverse;justify-content:flex-end;">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                      <input type="radio" id="star<?= $s ?>" name="rating" value="<?= $s ?>">
                      <label for="star<?= $s ?>">★</label>
                    <?php endfor; ?>
                  </div>
                </div>
                <!-- Name -->
                <div style="margin-bottom:14px;">
                  <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Your name *</label>
                  <input type="text" name="name" id="reviewName"
                         placeholder="<?= htmlspecialchars($customerName ?? 'Your name', ENT_QUOTES, 'UTF-8') ?>"
                         value="<?= htmlspecialchars($customerName ?? '', ENT_QUOTES, 'UTF-8') ?>"
                         style="width:100%;padding:10px 12px;border:1.5px solid #dedede;border-radius:4px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <!-- Review title -->
                <div style="margin-bottom:14px;">
                  <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Review title</label>
                  <input type="text" name="title" id="reviewTitle"
                         placeholder="Summarise your experience"
                         style="width:100%;padding:10px 12px;border:1.5px solid #dedede;border-radius:4px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <!-- Review body -->
                <div style="margin-bottom:14px;">
                  <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Review * <span id="reviewCharCount" style="font-weight:400;color:#aaa;">(min 10 characters)</span></label>
                  <textarea name="review" id="reviewBody"
                            placeholder="Tell others what you think about this product…"
                            rows="4"
                            style="width:100%;padding:10px 12px;border:1.5px solid #dedede;border-radius:4px;font-size:14px;resize:vertical;outline:none;font-family:inherit;box-sizing:border-box;"></textarea>
                </div>
                <button type="submit" id="reviewSubmitBtn"
                        style="padding:11px 28px;background:#072708;color:white;border:none;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;">
                  Submit Review
                </button>
              </form>
              <div id="reviewSuccess" style="display:none;padding:16px;background:#f0faf0;border-radius:4px;color:#072708;font-size:14px;font-weight:500;margin-top:12px;">
                ✓ Thank you for your review! It will appear shortly.
              </div>
            </div>
            <?php endif; ?>

            <!-- Review list -->
            <?php if (!empty($reviews)): ?>
              <div class="w-dyn-list" data-admc-tb="read_reviews">
                <div class="review-list w-dyn-items" role="list">
                  <?php foreach ($reviews as $rev): ?>
                    <div class="w-dyn-item" role="listitem">
                      <div class="review-card">
                        <div class="review-name-tag">
                          <div class="p-02">
                            <?= htmlspecialchars(strtoupper(substr($rev["input_reviewer_name"] ?? "A", 0, 2)), ENT_QUOTES, "UTF-8") ?>
                          </div>
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
              <div style="padding:32px 0;color:#b5b5b5;font-size:14px;">
                No reviews yet. Be the first to share your experience!
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
          $relUrl = $baseUrl . "/products/" . $rel["hash_id"] . "/" . cleans($rel["input_title"]);
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
                     data-admc-image="panel_products"
                     data-admc-id="<?= $rel["id"] ?>">
                  <img alt="<?= htmlspecialchars($rel["input_title"], ENT_QUOTES, "UTF-8") ?>"
                       class="all-img" loading="lazy"
                       src="<?= htmlspecialchars($rel["image_1"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>">
                  <div class="add-to-card-02" data-product-id="<?= $rel["hash_id"] ?>"
                       onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.cartAddItem('<?= $rel["hash_id"] ?>', '', 1);">
                    <img alt="" class="add-to-card-icon" src="/assets/img/icons/cart-add.svg">
                    <div class="p-01">Add to cart</div>
                  </div>
                </div>
                <div class="product-card-bottom">
                  <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($rel["select_category"] ?? "", ENT_QUOTES, "UTF-8") ?></div></div>
                  <div class="product-name-price">
                    <div class="heading-06"><?= htmlspecialchars($rel["input_title"], ENT_QUOTES, "UTF-8") ?></div>
                    <div class="heading-07"><?= $sym ?><?= number_format((float)$rel["input_price"], 2) ?></div>
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
       alt="<?= htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8") ?>">
  <span class="sticky-cart-product-name">
    <?= htmlspecialchars($product["input_title"], ENT_QUOTES, "UTF-8") ?>
  </span>
  <span class="sticky-cart-price"><?= $sym ?><?= number_format((float)$product["input_price"], 2) ?></span>
  <button class="sticky-cart-btn"
          data-product-id="<?= $product["hash_id"] ?>">Add to Cart</button>
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

// Review form toggle — guard null (button only exists when logged in)
var showReviewFormBtn = document.getElementById("showReviewForm");
if (showReviewFormBtn) {
  showReviewFormBtn.addEventListener("click", function() {
    var form = document.getElementById("reviewFormCard");
    if (form) form.style.display = form.style.display === "none" ? "block" : "none";
  });
}

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
    var basePrice = parseFloat("<?= $product['input_price'] ?>") || 0;

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
  
  if (activeButtons.length < totalGroups) {
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
    var basePrice = parseFloat("<?= $product['input_price'] ?>") || 0;
    var sym = window.VENORA_CURRENCY_SYMBOL || "$";
    if (priceDisplay) priceDisplay.textContent = sym + (basePrice * parseInt(input.value)).toFixed(2);
  });
});

// Sticky cart button
document.querySelector(".sticky-cart-btn") && document.querySelector(".sticky-cart-btn").addEventListener("click", function() {
  var btn = this;
  var productId = btn.dataset.productId;
  var variantId = getSelectedVariantIds();
  if (variantId === false) return; // Validation failed
  
  var qty = parseInt((document.getElementById("detailQty") || {}).value) || 1;
  btn.textContent = "Adding...";
  window.Venora.cartAddItem(productId, variantId, qty, function() { btn.textContent = "Added!"; setTimeout(function() { btn.textContent = "Add to Cart"; }, 2000); });
});

// Review submit — with client-side validation
function submitReview(e) {
  e.preventDefault();
  var errBox  = document.getElementById("reviewFormError");
  var btn     = document.getElementById("reviewSubmitBtn");
  errBox && (errBox.style.display = "none");

  // Client-side validation
  var rating = document.querySelector('input[name="rating"]:checked');
  var name   = (document.getElementById("reviewName") || {}).value || "";
  var body   = (document.getElementById("reviewBody") || {}).value || "";

  if (!rating) {
    if (errBox) { errBox.textContent = "Please select a star rating."; errBox.style.display = "block"; }
    return;
  }
  if (name.trim().length < 2) {
    if (errBox) { errBox.textContent = "Please enter your name (min 2 characters)."; errBox.style.display = "block"; }
    return;
  }
  if (body.trim().length < 10) {
    if (errBox) { errBox.textContent = "Review must be at least 10 characters."; errBox.style.display = "block"; }
    return;
  }
  if (body.trim().length > 2000) {
    if (errBox) { errBox.textContent = "Review is too long (max 2000 characters)."; errBox.style.display = "block"; }
    return;
  }

  if (btn) { btn.textContent = "Submitting…"; btn.disabled = true; }

  var data = {};
  new FormData(e.target).forEach(function(v, k) { data[k] = v; });

  fetch(window.VENORA_BASE_URL + "/review-submit", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.auth === false) {
      // Not logged in — redirect to login
      window.location.href = window.VENORA_BASE_URL + "/customer-login";
      return;
    }
    if (res.success) {
      document.getElementById("reviewForm").style.display = "none";
      document.getElementById("reviewSuccess").style.display = "block";
    } else {
      if (errBox) { errBox.textContent = res.message || "Submission failed. Please try again."; errBox.style.display = "block"; }
      if (btn) { btn.textContent = "Submit Review"; btn.disabled = false; }
    }
  })
  .catch(function() {
    if (errBox) { errBox.textContent = "Something went wrong. Please try again."; errBox.style.display = "block"; }
    if (btn) { btn.textContent = "Submit Review"; btn.disabled = false; }
  });
}

// Live character count for review body
var reviewBody = document.getElementById("reviewBody");
var charCount  = document.getElementById("reviewCharCount");
if (reviewBody && charCount) {
  reviewBody.addEventListener("input", function() {
    var len = reviewBody.value.length;
    charCount.textContent = len < 10 ? "(" + (10 - len) + " more characters needed)" : "(" + len + " / 2000)";
    charCount.style.color = len < 10 ? "#dc2626" : len > 1800 ? "#f59e0b" : "#aaa";
  });
}
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>