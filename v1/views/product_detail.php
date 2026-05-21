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
if (!empty($details['primary_image'])) {
    if (empty($gallery) || $gallery[0] !== $details['primary_image']) {
        array_unshift($gallery, $details['primary_image']);
    }
}
$gallery = array_map('fixImagePath', $gallery);
if (empty($gallery)) $gallery = [fixImagePath("/assets/img/icons/cart.svg")];

$variants = $details['variants'] ?? [];

$allOptions = [];
foreach ($variants as $v) {
    foreach ($v['options'] as $opt) {
        $optId = $opt['option_id'];
        if (!isset($allOptions[$optId])) {
            $allOptions[$optId] = ['name' => $opt['option_name'], 'values' => []];
        }
        $valId = $opt['value_id'];
        if (!isset($allOptions[$optId]['values'][$valId])) {
            $allOptions[$optId]['values'][$valId] = ['id' => $valId, 'name' => $opt['value_name']];
        }
    }
}

$basePrice = $usdToggle === 1 ? ($details['price_range_usd']['price'] ?? $details['price_range_usd']['min'] ?? 0) : ($details['price_range_ngn']['price'] ?? $details['price_range_ngn']['min'] ?? 0);
$reviews = $details['reviews'] ?? []; 
$avgRating = $details['input_rating'] ?? 4.5;

$relatedOptions = ['category_id' => $details['select_product_category'] ?? null, 'limit' => 4, 'currency' => $usdToggle === 1 ? 'USD' : 'NGN'];
$related = $controller->fetchProducts($relatedOptions);
$related = array_filter($related, fn($p) => $p["hash_id"] !== $hash);
$related = array_slice(array_values($related), 0, 3);

$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/?>

<section class="products hero-section section-0-120">
  <div class="container">
    <div class="product-single">
      <div class="navigation" style="margin-bottom:28px;">
        <div class="breadcrumb-row">
          <a href="<?= $baseUrl ?>/">Home</a>
          <span class="breadcrumb-sep">/</span>
          <a href="<?= $baseUrl ?>/products">Products</a>
          <?php if (!empty($productCategoryName)): ?>
            <span class="breadcrumb-sep">/</span>
            <a href="<?= $baseUrl ?>/products?tab=<?= urlencode($productCategoryName) ?>"><?= htmlspecialchars($productCategoryName, ENT_QUOTES, "UTF-8") ?></a>
          <?php endif; ?>
          <span class="breadcrumb-sep">/</span>
          <span><?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?></span>
        </div>
      </div>

      <div class="product-details">
        <div class="product-img-wrapper">
          <div class="product-img-box" data-admc-image="panel_product" data-admc-id="<?= $details["id"] ?>">
            <img alt="<?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?>" class="all-img zoom" loading="lazy" id="mainProductImg" src="<?= htmlspecialchars($gallery[0], ENT_QUOTES, "UTF-8") ?>">
          </div>
          <?php if (count($gallery) > 1): ?>
            <div class="product-thumbs">
              <?php foreach ($gallery as $gi => $imgUrl): ?>
                <div class="product-thumb <?= $gi === 0 ? "active" : "" ?>" data-src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, "UTF-8") ?>" onclick="document.getElementById('mainProductImg').src=this.dataset.src; document.querySelectorAll('.product-thumb').forEach(function(t){t.classList.remove('active')}); this.classList.add('active');">
                  <img alt="" loading="lazy" src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, "UTF-8") ?>">
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="product-details-right">
          <div class="product-details-top">
            <div class="product-rating">
              <div class="product-rating-inner">
                <img alt="Star" class="star" src="<?= $baseUrl ?>/assets/img/icons/star.svg" style="width:16px;height:16px;">
                <div class="tagline no-height">
                  <?= $avgRating ?>
                  <span class="color-gray">(<?= count($reviews) ?> Reviews)</span>
                </div>
              </div>
            </div>
            <h2 class="heading-02" data-admc-manage="panel_product" data-admc-id="<?= $details["id"] ?>"><?= htmlspecialchars($details["name"], ENT_QUOTES, "UTF-8") ?></h2>
            <?php if (!empty($productCategoryName)): ?>
              <div class="tagline caps color-gray" style="margin-bottom:8px;"><?= htmlspecialchars($productCategoryName, ENT_QUOTES, "UTF-8") ?></div>
            <?php endif; ?>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
              <div class="heading-04" id="detailPrice"><?= formatPrice($basePrice, $sym) ?></div>
            </div>
          </div>

          <p class="p-01 color-gray" style="margin-bottom:24px;" data-admc-manage="panel_product" data-admc-id="<?= $details["id"] ?>"><?= previewBody($details["description"] ?? "", 40) ?></p>

          <div class="add-to-cart-section" id="addToCartSection">
            <?php if (!empty($allOptions)): ?>
              <div class="modal-variants-label" style="text-transform:uppercase; font-weight:bold; margin-bottom:12px; font-size:12px; letter-spacing:1px; color:#000;">VARIANTS:</div>
              <?php foreach ($allOptions as $optId => $opt): ?>
                <div class="modal-variants" style="margin-bottom:12px;">
                  <div class="modal-variant-options" data-option-id="<?= $optId ?>">
                    <?php foreach ($opt['values'] as $val): ?>
                      <button type="button" class="variant-btn" data-value-id="<?= $val['id'] ?>">
                        <?= htmlspecialchars($val['name'], ENT_QUOTES, 'UTF-8') ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

            <div class="quantity-outer" style="margin-bottom:20px;">
              <label class="p-02 caps" style="display:block;margin-bottom:8px;">Quantity</label>
              <div class="quantity-wrap" style="display:flex;align-items:center;gap:16px;">
                <div class="qty-control">
                  <button type="button" class="qty-btn" data-action="decrease">−</button>
                  <input class="qty-input" id="detailQty" type="number" value="1" min="1">
                  <button type="button" class="qty-btn" data-action="increase">+</button>
                </div>
                <div class="heading-04" id="detailPriceDisplay"><?= formatPrice($basePrice, $sym) ?></div>
              </div>
            </div>

            <?php $stock = $details['base_inventory'] ?? 0; ?>
            <div class="product-stock-status" id="stockStatus" style="margin-bottom:16px;">
               <?php if ($stock <= 0): ?><span class="stock-badge stock-out">Out of stock</span>
               <?php elseif ($stock <= 5): ?><span class="stock-badge stock-low">Only <?= $stock ?> left</span>
               <?php else: ?><span class="stock-badge stock-high">In stock</span><?php endif; ?>
            </div>

            <div class="add-cart-btn" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;">
              <button type="button" id="detailAddToCart" class="btn-add-to-cart-main" data-product-id="<?= $details["hash_id"] ?>">Add to Cart</button>
              <button type="button" class="modal-wishlist-btn" id="detailWishlist" data-id="<?= $details["hash_id"] ?>">
                <img src="<?= $baseUrl ?>/assets/img/icons/heart-outline.svg" alt="Wishlist" id="detailWishlistImg">
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  const base = window.VENORA_BASE_URL || '';
  const productHash = "<?= $details['hash_id'] ?>";
  const allVariants = <?= json_encode($variants) ?>;
  const initialBasePrice = parseFloat("<?= $basePrice ?>");
  const currencySym = "<?= $sym ?>";

  window.currentVariantId = "";

  document.querySelectorAll(".variant-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      const parent = btn.closest(".modal-variant-options");
      const wasActive = btn.classList.contains("active");
      parent.querySelectorAll(".variant-btn").forEach(b => b.classList.remove("active"));
      if (!wasActive) btn.classList.add("active");
      resolveVariant();
    });
  });

  function resolveVariant() {
    const selected = Array.from(document.querySelectorAll(".variant-btn.active")).map(b => parseInt(b.dataset.valueId));
    const totalGroups = document.querySelectorAll(".modal-variant-options").length;
    if (selected.length === totalGroups) {
      const match = allVariants.find(v => {
        const vOptionIds = v.options.map(o => o.value_id);
        return selected.every(id => vOptionIds.includes(id));
      });
      if (match) {
        const price = "<?= $usdToggle ?>" === "1" ? match.price_usd : match.price_ngn;
        document.getElementById("detailPrice").textContent = currencySym + parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2});
        updateStock(match.inventory);
        window.currentVariantId = match.id;
        updateQuantityPrice();
      }
    } else {
        window.currentVariantId = "";
        document.getElementById("detailPrice").textContent = currencySym + initialBasePrice.toLocaleString(undefined, {minimumFractionDigits: 2});
        updateQuantityPrice();
    }
  }

  function updateStock(s) {
    const el = document.getElementById("stockStatus");
    if (s <= 0) el.innerHTML = '<span class="stock-badge stock-out">Out of stock</span>';
    else if (s <= 5) el.innerHTML = '<span class="stock-badge stock-low">Only ' + s + ' left</span>';
    else el.innerHTML = '<span class="stock-badge stock-high">In stock</span>';
  }

  function updateQuantityPrice() {
    const qty = parseInt(document.getElementById("detailQty").value) || 1;
    const currentPrice = parseFloat(document.getElementById("detailPrice").textContent.replace(currencySym, "").replace(/,/g, ''));
    document.getElementById("detailPriceDisplay").textContent = currencySym + (currentPrice * qty).toLocaleString(undefined, {minimumFractionDigits: 2});
  }
// Helper for venora-app.js
window.getSelectedVariantIds = function() {
  const totalGroups = document.querySelectorAll(".modal-variant-options").length;
  const active = document.querySelectorAll(".variant-btn.active");
  if (totalGroups > 0 && active.length < totalGroups) {
    window.Venora.showToast("Please select all options", "error");
    return false;
  }
  // Return all selected value IDs as a comma-separated string
  return Array.from(active).map(b => b.dataset.valueId).join(',');
};

  document.querySelectorAll(".qty-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      const input = document.getElementById("detailQty");
      let val = parseInt(input.value) || 1;
      if (btn.dataset.action === "increase") val++;
      else if (val > 1) val--;
      input.value = val;
      updateQuantityPrice();
    });
  });
})();
</script>

<style>
  :root { --dash-accent: #072708; }
  .variant-btn { padding: 10px 18px; border: 1.5px solid #eee; background: #fff; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; margin: 4px; color: #333; }
  .variant-btn:hover { border-color: var(--dash-accent); }
  .variant-btn.active { background: var(--dash-accent) !important; color: #fff !important; border-color: var(--dash-accent) !important; }
  .stock-badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
  .stock-out { background: #fee2e2; color: #b91c1c; }
  .stock-low { background: #fff7ed; color: #c2410c; }
  .stock-high { background: #f0fdf4; color: #15803d; }
</style>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
