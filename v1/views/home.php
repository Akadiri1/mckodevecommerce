<?php
$page_title = "Home";
$bodyClass  = "";

// ── Safe DB fetch ───────────────────────────────────────────
function safeHomeFetch($conn, $table, $where = []) {
    try {
        $conn->query("SELECT 1 FROM `$table` LIMIT 1");
        return selectContent($conn, $table, $where);
    } catch (Exception $e) { return []; }
}

$heroArr = safeHomeFetch($conn, "settings_home_hero", ["visibility" => "show"]);
$hero    = !empty($heroArr) ? $heroArr[0] : [];

// Fetch UI labels for dynamic text
$uiLabelsArr = safeHomeFetch($conn, "settings_shop_ui_labels", ["visibility" => "show"]);
$uiLabels    = !empty($uiLabelsArr) ? $uiLabelsArr[0] : [];
$loadMoreTxt = $uiLabels['input_load_more'] ?? 'Load More';
$loadingTxt  = $uiLabels['input_adding_to_cart'] ?? 'Loading…'; // Reusing adding text or similar

if (!empty($hero)) {
    $hero['image_client_1']    = fixImagePath($hero['image_client_1'] ?? '');
    $hero['image_client_2']    = fixImagePath($hero['image_client_2'] ?? '');
    $hero['image_client_3']    = fixImagePath($hero['image_client_3'] ?? '');
    $hero['image_partner_1']   = fixImagePath($hero['image_partner_1'] ?? '');
    $hero['image_partner_2']   = fixImagePath($hero['image_partner_2'] ?? '');
    $hero['image_partner_3']   = fixImagePath($hero['image_partner_3'] ?? '');
    $hero['image_scroll_icon'] = fixImagePath($hero['image_scroll_icon'] ?? '');
    $hero['image_1']           = fixImagePath($hero['image_1'] ?? '');
    $hero['input_video_url']   = fixImagePath($hero['input_video_url'] ?? '');
}

// Load first page of products for initial render (6 per page) - newest first (by input_order)
$featuredProducts = selectContentDesc($conn, "panel_product", ["visibility" => "show"], "input_order", 6);

// Pre-index variant prices and has_variants flag
$_fvPrices = selectContent($conn, "variants", []);
$_fvPriceIdx = [];
foreach ($_fvPrices as $_v) {
    $h = $_v['product_hash_id'];
    if (!isset($_fvPriceIdx[$h])) $_fvPriceIdx[$h] = $usdEnabled ? (float)$_v['input_price_usd'] : (float)$_v['input_price_ngn'];
}
$allFeaturedVariants = selectContent($conn, "variants", []);
$variantsByProduct   = [];
foreach ($allFeaturedVariants as $fv) { $variantsByProduct[$fv['product_hash_id']] = true; }
$categories = selectContentAsc($conn, "selection_product_category", ["visibility" => "show"], "id", 4);
$_catById   = [];
foreach ($categories as $_c) { $_catById[(string)$_c['id']] = $_c['input_title'] ?? ''; }

foreach ($featuredProducts as &$fp) {
    $fp['input_price']    = $_fvPriceIdx[$fp['hash_id']] ?? 0;
    $fp['has_variants']   = isset($variantsByProduct[$fp['hash_id']]) ? "true" : "false";
    $fp['_category_name'] = $_catById[(string)($fp['select_product_category'] ?? '')] ?? '';
    $fp['image_2']        = fixImagePath($fp['image_2'] ?? '');
    $fp['image_1']        = fixImagePath($fp['image_1'] ?? '');
}
unset($fp);

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/?>

<!-- HERO SECTION -->
<?php/*##cbcode_10001Heroo##*/?>
<div data-cbcodesection="cbcode_10001Hero">
  <section class="home-hero" data-w-id="29aa9955-28b7-3f33-84f0-dfd0b6c1b7e0"
           style="margin:0!important;padding:0!important;">
    <div class="container home">
      <div class="home-hero-inner">
        <div></div>
        <div class="home-hero-top" data-w-id="56c8ce7c-05a8-e7f5-8c8d-22339360ef84">
          <h1 class="heading-01" 
              data-w-id="537cb9b7-edf7-dd89-7237-03e80fbacb5e"
              data-admc-manage="settings_home_hero"
              data-admc-id="<?= $hero['id'] ?? 1 ?>"
              style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <?= htmlspecialchars($hero['input_heading'] ?? "Your natural beauty, expressed with care", ENT_QUOTES, 'UTF-8') ?>
          </h1>
          <div class="home-hero-btns" data-w-id="7e657425-704b-5a08-7f9d-c0375c453184"
               style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="btn-wrap" data-wf--btn--variant="green-bg">
              <a class="btn-02-link w-variant-8ed325c4-2932-a7c6-4370-0e1c5d9dff18 w-inline-block" href="<?= $baseUrl ?>/products">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($hero['input_btn1_label'] ?? "Shop now", ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($hero['input_btn1_label'] ?? "Shop now", ENT_QUOTES, 'UTF-8') ?></div></div>
                  </div>
                </div>
              </a>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="transparent-bg">
              <a class="btn-02-link w-variant-f2854ced-260b-eaaf-b98b-55122a492e08 w-inline-block" href="<?= $baseUrl ?>/products">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($hero['input_btn2_label'] ?? "Our collection", ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($hero['input_btn2_label'] ?? "Our collection", ENT_QUOTES, 'UTF-8') ?></div></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="hero-bottom">
          <!-- <div class="client-review" data-w-id="dbb7e87a-fe5c-32b4-4cba-251dde4a1f67"
               style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="client-review-img">
              <div class="client-img-box-wrap"
                   data-admc-manage="settings_home_hero"
                   data-admc-id="<?= $hero['id'] ?? 1 ?>">
                <div class="client-img-box _01"><img alt="Client" class="all-img" src="<?= htmlspecialchars($hero['image_client_1'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="client-img-box _02"><img alt="Client" class="all-img" src="<?= htmlspecialchars($hero['image_client_2'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="client-img-box _03"><img alt="Client" class="all-img" src="<?= htmlspecialchars($hero['image_client_3'], ENT_QUOTES, 'UTF-8') ?>"></div>
              </div>
            </div>
            <div class="client-review-info">
              <div class="rating">
                <img alt="Star" class="star" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a1e07895c1d9149a7ad_Star 1.svg">
                <div class="tagline-semibold"><?= htmlspecialchars($hero['input_rating'] ?? "(4.9/5)", ENT_QUOTES, 'UTF-8') ?></div>
              </div>
              <div class="tagline"><?= htmlspecialchars($hero['input_trust_text'] ?? "Trusted by 300+ clients", ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div> -->
          <div class="partner" data-w-id="7ed915f2-e7a0-7698-b16b-25f03cf9b171"
               style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="partner-text">
              <div class="p-02"
                   data-admc-manage="settings_home_hero"
                   data-admc-id="<?= $hero['id'] ?? 1 ?>">
                <?= htmlspecialchars($hero['input_partners_heading'] ?? 'Trusted by leading brands', ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
            <div class="partner-outer"
                 data-admc-manage="settings_home_hero"
                 data-admc-id="<?= $hero['id'] ?? 1 ?>">
              <div class="partner-wrap">
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="<?= htmlspecialchars($hero['image_partner_1'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="<?= htmlspecialchars($hero['image_partner_2'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="<?= htmlspecialchars($hero['image_partner_3'], ENT_QUOTES, 'UTF-8') ?>"></div>
              </div>
            </div>
          </div>
          <div class="scroll-text-wrap" data-w-id="b84d2d52-ab61-6c00-c526-8f2d9cdc5053"
               style="opacity:0;-webkit-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="scroll-icon-wrap"
                 data-admc-manage="settings_home_hero"
                 data-admc-id="<?= $hero['id'] ?? 1 ?>">
              <svg class="scroll-icon _01" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:100%; height:100%;">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </div>
            <div class="heading-06"
                 data-admc-manage="settings_home_hero"
                 data-admc-id="<?= $hero['id'] ?? 1 ?>">
              <?= htmlspecialchars($hero['input_scroll_text'] ?? 'Scroll Down', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </div>
      </div>
      <!-- Hero media: video or image — both editable via ADMC -->
      <div class="home-hero-img">
        <?php
          $heroVideo = trim($hero['input_video_url'] ?? '');
          $heroImage = trim($hero['image_1'] ?? '');
        ?>
        <?php if (!empty($heroVideo)): ?>
          <div style="position:relative;width:100%;height:100%;"
               data-admc-manage="settings_home_hero"
               data-admc-id="<?= $hero['id'] ?? 1 ?>">
            <video autoplay loop muted playsinline
                   style="width:100%;height:100%;object-fit:cover;display:block;">
              <source src="<?= htmlspecialchars($heroVideo, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4" />
            </video>
          </div>
        <?php elseif (!empty($heroImage)): ?>
          <div data-admc-image="settings_home_hero"
               data-admc-id="<?= $hero['id'] ?? 1 ?>"
               style="width:100%;height:100%;">
            <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Hero background"
                 style="width:100%;height:100%;object-fit:cover;display:block;">
          </div>
        <?php else: ?>
          <div data-admc-manage="settings_home_hero"
               data-admc-id="<?= $hero['id'] ?? 1 ?>"
               style="width:100%;height:100%;background:#0d2b0d;display:flex;align-items:center;justify-content:center;">
            <p style="color:rgba(255,255,255,0.3);font-size:14px;">Add a video URL or image via ADMC</p>
          </div>
        <?php endif; ?>
      </div>
  </section>
</div>
<?php/*##cbcode_10001Heroc##*/?>

<!-- FEATURED PRODUCTS -->
<?php/*##cbcode_10001Productso##*/?>
<div data-cbcodesection="cbcode_10001Products">
  <section class="products section-120-120">
    <div class="container">
      <div class="header">
        <div class="header-left">
          <h2 class="heading-02"
              data-admc-manage="settings_home_hero"
              data-admc-id="<?= $hero['id'] ?? 1 ?>">
            <?= htmlspecialchars($hero['input_products_intro_heading'] ?? 'We believe skincare is a ritual, not a routine', ENT_QUOTES, 'UTF-8') ?>
          </h2>
        </div>
        <div class="header-right">
          <div class="p-01"
               data-admc-manage="settings_home_hero"
               data-admc-id="<?= $hero['id'] ?? 1 ?>">
            <?= htmlspecialchars($hero['text_products_intro_description'] ?? 'Discover our curated selection of products designed to highlight your unique beauty.', ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>
      </div>

      <!-- Category filter tabs — multi-select AJAX -->
      <div class="home-cat-tabs" id="homeCatTabs">
        <button class="home-cat-btn active" data-cat="">All products</button>
        <?php foreach ($categories as $cat): ?>
          <button class="home-cat-btn" data-cat="<?= htmlspecialchars($cat['input_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($cat['input_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </button>
        <?php endforeach; ?>
      </div>

      <?php
        // Check if more products exist beyond the first 8
        $totalProductCount = count(selectContent($conn, "panel_product", ["visibility" => "show"]));
        $initialHasMore = $totalProductCount > 6;
      ?>
      <div class="product-collection" id="productCollection">
        <div class="product-grid w-dyn-items" id="homeProductGrid" data-admc-tb="panel_product" role="list">
          <?php foreach ($featuredProducts as $product):
             $detailUrl = $baseUrl . "/products/" . $product['hash_id'] . "/" . ($product['input_slug'] ?? cleans($product['input_product_name']));
          ?>
            <div class="product-card-wrap">
                <div class="product-card">
                  <div class="product-card-img">
                    <a class="product-link w-inline-block" href="<?= $detailUrl ?>">
                      <div data-admc-image="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <img alt="<?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?>" 
                             class="all-img" loading="lazy" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      </div>
                      <div class="product-float">
                        <img alt="" class="all-img" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'], ENT_QUOTES, 'UTF-8') ?>">
                      </div>
                    </a>

                    <!-- Wishlist Button -->
                    <?php $inWishlist = in_array($product['hash_id'], $wishlistIds); ?>
                    <button class="wishlist-btn-card <?= $inWishlist ? 'active' : '' ?>" 
                            data-id="<?= $product['hash_id'] ?>" 
                            onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.toggleWishlist('<?= $product['hash_id'] ?>', this);"
                            style="position:absolute; top:12px; left:12px; z-index:15; background:white; border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.1); opacity:0; transition:opacity 0.3s ease;">
                      <img src="<?= $baseUrl ?>/assets/img/icons/<?= $inWishlist ? 'heart-filled.svg' : 'heart-outline.svg' ?>" style="width:18px; height:18px;" alt="Wishlist">
                    </button>

                    <!-- Add to Cart Button -->
                    <div class="add-to-card-02" 
                         data-product-id="<?= $product['hash_id'] ?>" 
                         data-has-variants="<?= $product['has_variants'] ?>"
                         onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.cartAddItem('<?= $product['hash_id'] ?>', '', 1, null, this);">
                      <img alt="" class="add-to-card-icon" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg">
                      <div class="p-01">Add to cart</div>
                    </div>
                  </div>
                  <div class="product-card-bottom">
                    <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($product['_category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="product-name-price">
                      <h3 class="heading-06" data-admc-manage="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <a href="<?= $detailUrl ?>" class="card-title-link">
                          <?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                      </h3>
                      <div class="heading-07" data-admc-manage="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <?= $shop_symbol ?><?= number_format($product['input_price'], 2) ?>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Load More button -->
        <div id="loadMoreWrap" style="text-align:center;margin-top:40px;<?= $initialHasMore ? '' : 'display:none;' ?>">
          <button id="loadMoreBtn"
                  data-admc-manage="settings_shop_ui_labels"
                  data-admc-id="<?= $uiLabels['id'] ?? 1 ?>"
                  style="padding:14px 40px;border:1.5px solid #072708;background:none;color:#072708;font-family:inherit;font-size:15px;font-weight:600;border-radius:7px;cursor:pointer;transition:all 0.2s;"
                  onmouseover="this.style.background='#072708';this.style.color='#fff';"
                  onmouseout="this.style.background='none';this.style.color='#072708';">
            <?= htmlspecialchars($loadMoreTxt, ENT_QUOTES, 'UTF-8') ?>
          </button>
        </div>
      </div>
    </div>
  </section>
</div>
<?php/*##cbcode_10001Productsc##*/?>

<script>
(function() {
  var activeCats  = [];
  var currentPage = 1;
  var perPage     = 6;
  var isLoading   = false;

  var grid          = document.getElementById('homeProductGrid');
  var loadMoreWrap  = document.getElementById('loadMoreWrap');
  var loadMoreBtn   = document.getElementById('loadMoreBtn');
  var base          = window.VENORA_BASE_URL || '';
  
  // Dynamic labels for JS
  var labelLoadMore = <?= json_encode($loadMoreTxt) ?>;
  var labelLoading  = <?= json_encode($loadingTxt) ?>;

  function buildUrl(page) {
    var url = base + '/products-filter?page=' + page + '&limit=' + perPage;
    if (activeCats.length) url += '&cats=' + activeCats.map(encodeURIComponent).join(',');
    return url;
  }

  function setLoading(on) {
    isLoading = on;
    if (grid) grid.style.opacity = on ? '0.5' : '1';
    if (loadMoreBtn) {
      loadMoreBtn.disabled = on;
      loadMoreBtn.textContent = on ? labelLoading : labelLoadMore;
    }
  }

  function updateButtons() {
    document.querySelectorAll('.home-cat-btn').forEach(function(b) {
      var c = b.dataset.cat;
      b.classList.toggle('active', c === '' ? activeCats.length === 0 : activeCats.indexOf(c) !== -1);
    });
  }

  function renderProducts(products, append) {
    if (!grid) return;
    var html = products.map(function(p) { return p.html; }).join('');
    if (append) {
      grid.insertAdjacentHTML('beforeend', html);
    } else {
      grid.innerHTML = html || '<div style="padding:60px 20px;text-align:center;color:#b5b5b5;"><p>No products found.</p></div>';
    }
    // Re-initialize ADMC for dynamic content
    setTimeout(function() {
      document.dispatchEvent(new CustomEvent('admc:init'));
    }, 500);
  }

  function fetchProducts(page, append) {
    if (isLoading) return;
    setLoading(true);

    fetch(buildUrl(page), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        setLoading(false);
        renderProducts(data.products || [], append);
        currentPage = page;
        // Show or hide "Load More"
        if (loadMoreWrap) {
          loadMoreWrap.style.display = data.has_more ? 'block' : 'none';
        }
      })
      .catch(function() { setLoading(false); });
  }

  // Auto-load all products on page load (replaces the PHP-rendered 8)
  // fetchProducts(1, false);

  // Category tab clicks
  document.querySelectorAll('.home-cat-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var cat = btn.dataset.cat;
      if (cat === '') {
        activeCats = [];
      } else {
        var idx = activeCats.indexOf(cat);
        if (idx === -1) activeCats.push(cat);
        else activeCats.splice(idx, 1);
      }
      updateButtons();
      fetchProducts(1, false); // reset to page 1, replace grid
    });
  });

  // Load More click
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      fetchProducts(currentPage + 1, true); // next page, append
    });
  }
})();
</script>

<!-- FEATURES GRID -->
<div data-cbsection="cb3">
  <?php/*##cbcode_10001Featureso##*/?>
  <?php
    $homeBlock1 = safeHomeFetch($conn, "panel_home_blocks", ["hash_id" => "homeblock001"]);
    $homeBlock1 = !empty($homeBlock1) ? $homeBlock1[0] : [];
    $homeBlock2 = safeHomeFetch($conn, "panel_home_blocks", ["hash_id" => "homeblock002"]);
    $homeBlock2 = !empty($homeBlock2) ? $homeBlock2[0] : [];
  ?>
  <section class="content section-0-120">
    <div class="container">
      <div class="content-outer">
        <div class="content-inner">
          <div class="content-img-box" data-admc-image="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>">
            <img alt="<?= htmlspecialchars($homeBlock1['input_title'] ?? 'Dermatology Tested', ENT_QUOTES, 'UTF-8') ?>" class="images speed" src="<?= htmlspecialchars($homeBlock1['image_1'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691940b886702b2d2296b5f3_Rectangle 1071.avif', ENT_QUOTES, 'UTF-8') ?>">
            <div class="content-float">
              <div data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>">
                <?= htmlspecialchars($homeBlock1['input_float_text'] ?? 'Dermatologist tested', ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
          </div>
          <div class="content-text" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <div class="content-text-inner" data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>">
              <h2 class="heading-02"><?= htmlspecialchars($homeBlock1['input_title'] ?? 'Dermatology-Tested Skincare You Can Trust', ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray">
                <div class="p-01"><?= htmlspecialchars($homeBlock1['text_content'] ?? 'Our formulas are developed in collaboration with dermatologists to ensure maximum comfort and visible results for every skin type.', ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg">
              <a class="btn-02-link w-inline-block" href="<?= htmlspecialchars($homeBlock1['input_btn_link'] ?? '/about', ENT_QUOTES, 'UTF-8') ?>">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($homeBlock1['input_btn_label'] ?? 'About us', ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($homeBlock1['input_btn_label'] ?? 'About us', ENT_QUOTES, 'UTF-8') ?></div></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="content-inner space-between">
          <div class="content-text" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <div class="content-text-inner" data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>">
              <h2 class="heading-02"><?= htmlspecialchars($homeBlock2['input_title'] ?? 'Naturally Clean. Always Paraben-Free.', ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray">
                <div class="p-01"><?= htmlspecialchars($homeBlock2['text_content'] ?? 'Your skin deserves only the best. That’s why every product we create is 100% paraben-free, formulated with gentle and natural ingredients.', ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg">
              <a class="btn-02-link w-inline-block" href="<?= htmlspecialchars($homeBlock2['input_btn_link'] ?? '/about', ENT_QUOTES, 'UTF-8') ?>">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($homeBlock2['input_btn_label'] ?? 'Learn more', ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($homeBlock2['input_btn_label'] ?? 'Learn more', ENT_QUOTES, 'UTF-8') ?></div></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div class="content-img-box" data-admc-image="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>">
            <img alt="<?= htmlspecialchars($homeBlock2['input_title'] ?? 'Paraben Free', ENT_QUOTES, 'UTF-8') ?>" class="images speed" src="<?= htmlspecialchars($homeBlock2['image_1'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ec862043fc0be46ab_Rectangle 1072.avif', ENT_QUOTES, 'UTF-8') ?>">
            <div class="content-float _02">
              <div data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>">
                <?= htmlspecialchars($homeBlock2['input_float_text'] ?? 'Paraben free', ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  

  <!-- WHY YOUR SKIN DESERVES THE BEST -->
  <section class="content section-0-120">
    <div class="container">
      <div class="content-inner-02">
        <div class="content-header" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
          <h2 class="heading-02"><?= htmlspecialchars($homeFeatures['input_heading'] ?? 'Why your skin deserves the best', ENT_QUOTES, 'UTF-8') ?></h2>
          <div class="content-header-short">
            <div class="color-gray">
              <div class="p-01"><?= htmlspecialchars($homeFeatures['text_subheading'] ?? 'We combine science, care, and transparency to create skincare you can truly trust.', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>
        </div>
        <div class="content-grid-wrap">
          <div class="content-grid" id="w-node-_78aac54f-d88f-5629-2fa1-44ccd4192e8e-50693cae">
            <div class="content-top">
              <div class="happy-client-card" style="opacity:0;transform:translate3d(0, 40px, 0);"
                   data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="happy-client-img-wrap"
                     data-admc-image="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                  <img alt="Happy clients" class="happy-client-img"
                       src="<?= htmlspecialchars($homeFeatures['image_card1'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0d2b63b5a131ce1cdeef_Group 1171274846.avif', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="happy-client-text">
                  <div class="star-wrap">
                    <img alt="Star" class="star" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a1e07895c1d9149a7ad_Star 1.svg">
                  </div>
                  <div class="heading-05">
                    <?= htmlspecialchars($homeFeatures['input_card1_title'] ?? '100k+ happy clients', ENT_QUOTES, 'UTF-8') ?>
                  </div>
                </div>
              </div>
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);"
                   data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float">
                  <div class="content-float-icon-box"
                       data-admc-image="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                    <img alt="Icon" class="content-float-icon"
                         src="<?= htmlspecialchars($homeFeatures['image_card2_icon'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0ca882f9f72fca4b3c57_Vector (1).svg', ENT_QUOTES, 'UTF-8') ?>">
                  </div>
                  <div class="content-bottom-title">
                    <h2 class="heading-04">
                      <?= htmlspecialchars($homeFeatures['input_card2_title'] ?? 'Connect products with a sense of luxury and self-care', ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                  </div>
                </div>
                <img alt="Luxury" class="content-float-img"
                     src="<?= htmlspecialchars($homeFeatures['image_card2'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c1b4c08ab15934abb_Rectangle 1081.avif', ENT_QUOTES, 'UTF-8') ?>">
                <div class="content-overlay"></div>
              </div>
            </div>
            <div class="content-bottom">
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);"
                   data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float-02">
                  <div class="content-top-title">
                    <h3 class="heading-04">
                      <?= htmlspecialchars($homeFeatures['input_card3_title'] ?? 'Shop easily online or in our stores', ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                  </div>
                </div>
                <img alt="Stores" class="content-float-img"
                     src="<?= htmlspecialchars($homeFeatures['image_card3'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c07895c1d914abe40_Textured Green Surface 1.avif', ENT_QUOTES, 'UTF-8') ?>">
                <div class="content-overlay"></div>
              </div>
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);"
                   data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float">
                   <div class="content-float-icon-box"
                        data-admc-image="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                     <img alt="Icon" class="content-float-icon"
                          src="<?= htmlspecialchars($homeFeatures['image_card4_icon'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a1f7658c01e989aa647c6_Vector (4).svg', ENT_QUOTES, 'UTF-8') ?>">
                   </div>
                   <div class="content-bottom-title">
                     <h3 class="heading-04">
                       <?= htmlspecialchars($homeFeatures['input_card4_title'] ?? 'Natural ingredients with proven effects', ENT_QUOTES, 'UTF-8') ?>
                     </h3>
                   </div>
                </div>
                <img alt="Natural" class="content-float-img"
                     src="<?= htmlspecialchars($homeFeatures['image_card4'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3d62dfb2e1c46b2f45_Rectangle 16.avif', ENT_QUOTES, 'UTF-8') ?>">
                <div class="content-overlay"></div>
              </div>
            </div>
          </div>
          <div class="content-card" id="w-node-_5df0eadb-5d65-6750-d614-b195a45c0318-50693cae" style="opacity:0;transform:translate3d(0, 40px, 0);"
               data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
             <div class="content-card-inner-float large">
                <div class="content-bottom-title">
                   <h3 class="heading-04">
                     <?= htmlspecialchars($homeFeatures['input_card5_title'] ?? 'Visible results in just 2 weeks', ENT_QUOTES, 'UTF-8') ?>
                   </h3>
                </div>
             </div>
             <img alt="Results" class="content-float-img"
                  src="<?= htmlspecialchars($homeFeatures['image_card5'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ecb604588eb85d1dd_Rectangle 1082.avif', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>
    </section>
  <!-- [cbcode_10001Featuresc] -->
</div>

<!-- TESTIMONIAL SECTION -->
<div data-cbsection="cb4">
  <?php/*##cbcode_10001Testimonialo##*/?>
  <?php
    $homeQuoteArr = safeHomeFetch($conn, "settings_home_quote", ["visibility" => "show"]);
    $homeQuote = !empty($homeQuoteArr) ? $homeQuoteArr[0] : [];
    if (!empty($homeQuote)) {
        $homeQuote['image_quote_icon'] = fixImagePath($homeQuote['image_quote_icon'] ?? '');
        $homeQuote['image_author_signature'] = fixImagePath($homeQuote['image_author_signature'] ?? '');
        $homeQuote['image_1'] = fixImagePath($homeQuote['image_1'] ?? '');
        $homeQuote['image_2'] = fixImagePath($homeQuote['image_2'] ?? '');
        $homeQuote['image_3'] = fixImagePath($homeQuote['image_3'] ?? '');
    }
  ?>
  <section class="testimonial section-0-120">
    <div class="container">
      <div class="testimonial-inner">
        <div class="testimonial-wrap">
          <div class="testimonial-top" style="opacity:0;transform:translate3d(0, 40px, 0);"
               data-admc-manage="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <div class="qoute">
              <img alt="Quote icon" class="qoute-img"
                   src="<?= htmlspecialchars($homeQuote['image_quote_icon'] ?? $baseUrl . '/dummy.png', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <h3 class="heading-03">
              <?= htmlspecialchars($homeQuote['text_quote'] ?? 'Beauty is not just what you see in the mirror - it’s how you feel in your own skin. At Venora, every product is crafted to empower that feeling.', ENT_QUOTES, 'UTF-8') ?>
            </h3>
          </div>
          <div class="author-name" style="opacity:0;transform:translate3d(0, 40px, 0);"
               data-admc-manage="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <img alt="<?= htmlspecialchars($homeQuote['input_author_name'] ?? 'Dr. Isabella Hartman', ENT_QUOTES, 'UTF-8') ?>"
                 class="author-img"
                 src="<?= htmlspecialchars($homeQuote['image_author_signature'] ?? $baseUrl . '/dummy.png', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
        <div class="testimonial-img-wrap"
             data-admc-manage="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
          <div class="testimonial-img _01" data-admc-image="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <img alt="Testimonial image 1" class="all-img"
                 src="<?= htmlspecialchars($homeQuote['image_1'] ?? $baseUrl . '/dummy.png', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="testimonial-img" data-admc-image="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <img alt="Testimonial image 2" class="all-img"
                 src="<?= htmlspecialchars($homeQuote['image_2'] ?? $baseUrl . '/dummy.png', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="testimonial-img _02" data-admc-image="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <img alt="Testimonial image 3" class="all-img"
                 src="<?= htmlspecialchars($homeQuote['image_3'] ?? $baseUrl . '/dummy.png', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- GALLERY TICKER -->
<!-- <div data-cbsection="cb5">
  <?php/*##cbcode_10001Tickero##*/?>
  <section class="gallery home-ticker-section">
    <div class="home-ticker-track">
      <?php
      $tickerIcon = htmlspecialchars($homeTicker['image_icon'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691ca9c870e18036642ecd2f_logoipsum-274 (1) 11.svg', ENT_QUOTES, 'UTF-8');
      $tickerText = htmlspecialchars($homeTicker['input_text'] ?? 'GET 25% DISCOUNT', ENT_QUOTES, 'UTF-8');
      ?>
      <?php for ($tw = 0; $tw < 2; $tw++): ?>
        <div class="home-ticker-row" aria-hidden="<?= $tw > 0 ? 'true' : 'false' ?>">
          <?php for ($dc = 0; $dc < 10; $dc++): ?>
            <div class="discount-card">
              <div class="discount-icon-box">
                <img alt="" class="discount-icon" src="<?= $tickerIcon ?>">
              </div>
              <div class="heading-06"><?= $tickerText ?></div>
            </div>
          <?php endfor; ?>
        </div>
      <?php endfor; ?>
    </div>
  </section>
</div> -->

<!-- [cbcode_10001c] -->

<script>
(function() {
  /**
   * ADMC Scroll Reveal Engine
   * Simulates Webflow IX2 (Interactions) for elements with data-w-id
   */
  const initReveal = () => {
    const observerOptions = {
      threshold: 0.15,
      rootMargin: '0px 0px -50px 0px'
    };

    const revealElement = (el) => {
      el.style.setProperty('opacity', '1', 'important');
      el.style.setProperty('-webkit-transform', 'translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)', 'important');
      el.style.setProperty('-moz-transform', 'translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)', 'important');
      el.style.setProperty('-ms-transform', 'translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)', 'important');
      el.style.setProperty('transform', 'translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)', 'important');
      el.style.setProperty('transition', 'opacity 1s cubic-bezier(0.23, 1, 0.32, 1), transform 1s cubic-bezier(0.23, 1, 0.32, 1)', 'important');
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          revealElement(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Capture all elements that should be animated
    const targets = document.querySelectorAll('[data-w-id], [style*="opacity:0"], [style*="opacity: 0"]');
    targets.forEach(el => {
      // Ensure initial state is applied before observing
      if (!el.style.opacity) el.style.opacity = "0";
      observer.observe(el);
    });
  };

  // Run on load
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initReveal, 100);
  } else {
    window.addEventListener('load', initReveal);
  }
})();
</script>

<style>
/* Suppress video background fallback */
.w-background-video > video {
  background-image: none !important;
  background-color: transparent !important;
}

/* Ensure animated elements are hidden initially even if CSS hasn't loaded */
[data-w-id], [style*="opacity:0"], [style*="opacity: 0"] {
  will-change: opacity, transform;
}

@keyframes gallery-scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
.gallery-wrapper { display: flex; overflow: hidden; white-space: nowrap; background-color: #ffffff; }
.gallery-card-wrap { display: flex; }
</style>

<?php include APP_PATH . "/views/includes/footer.php"; ?>