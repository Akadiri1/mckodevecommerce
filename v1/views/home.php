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
$dummyImg = $baseUrl . '/dummy.png';

// ADMC Framework: Fetch dynamic image lists
$heroClients  = selectContent($conn, "images", ["asset_hash_id" => "hero001"]);
$heroPartners = selectContentAsc($conn, "panel_partners", ["visibility" => "show"], "input_order", 12);

// Fetch UI labels for dynamic text
$uiLabelsArr = safeHomeFetch($conn, "settings_shop_ui_labels", ["visibility" => "show"]);
$uiLabels    = !empty($uiLabelsArr) ? $uiLabelsArr[0] : [];
$loadMoreTxt = $uiLabels['input_load_more'] ?? 'Load More';
$loadingTxt  = $uiLabels['input_adding_to_cart'] ?? 'Loading…'; 

if (!empty($hero)) {
    $hero['image_1']           = fixImagePath($hero['image_1'] ?? '');
    $hero['input_video_url']   = fixImagePath($hero['input_video_url'] ?? '');
}

// Load first page of products for initial render (6 per page)
$featuredProducts = selectContentDesc($conn, "panel_product", ["visibility" => "show"], "id", 6);

// Simple pre-indexing for initial PHP render
$categories = selectContentAsc($conn, "selection_product_category", ["visibility" => "show"], "id", 4);
$_catById   = [];
foreach ($categories as $_c) { $_catById[(string)$_c['id']] = $_c['input_title'] ?? ''; }

foreach ($featuredProducts as &$fp) {
    $fp['_category_name'] = $_catById[(string)($fp['select_product_category'] ?? '')] ?? '';
    $fp['image_2']        = fixImagePath($fp['image_2'] ?? '');
    $fp['image_1']        = fixImagePath($fp['image_1'] ?? '');
    
    // Fetch variants to calculate base price and total inventory
    $variants = selectContent($conn, "variants", ["product_hash_id" => $fp['hash_id']]);
    $fp['input_price']  = !empty($variants) ? ($usdEnabled ? $variants[0]['input_price_usd'] : $variants[0]['input_price_ngn']) : 0;
    $fp['has_variants'] = !empty($variants) ? "true" : "false";
    $fp['total_stock']  = array_sum(array_column($variants, 'input_inventory'));
}
unset($fp);

include APP_PATH . "/views/includes/header.php";
?>

<style>
  .stock-badge-main {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 10;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .badge-in-stock { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-out-stock { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
  
  .btn-add-disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    background: #eee !important;
    color: #888 !important;
  }
</style>

<div data-cbsection="cb1">
<?php/*##cb1o##*/?>

<!-- HERO SECTION -->
 <style>
  .heading-01 {
    font-size: 35px;
    line-height: 56px;
  }
 </style>
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
          <div class="client-review" data-w-id="dbb7e87a-fe5c-32b4-4cba-251dde4a1f67"
               style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="client-review-img">
              <div class="client-img-box-wrap" data-admc-tb="images" data-admc-tbadd="settings_home_hero" data-admc-tblink="hero001">
                <?php if (empty($heroClients)): ?>
                  <?php for($i=1; $i<=3; $i++): ?><div class="client-img-box _0<?= $i ?>"><img alt="Client" class="all-img" src="<?= $dummyImg ?>"></div><?php endfor; ?>
                <?php else: ?>
                  <?php foreach ($heroClients as $ci => $client): ?>
                    <div class="client-img-box _0<?= ($ci % 3) + 1 ?>"><img alt="Client" class="all-img" src="<?= fixImagePath($client['image_1']) ?>" data-admc-image="images" data-admc-id="<?= $client['id'] ?>"></div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
            <div class="client-review-info">
              <div class="rating">
                <img alt="Star" class="star" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a1e07895c1d9149a7ad_Star 1.svg">
                <div class="tagline-semibold"><?= htmlspecialchars($hero['input_rating'] ?? "(4.9/5)", ENT_QUOTES, 'UTF-8') ?></div>
              </div>
              <div class="tagline"><?= htmlspecialchars($hero['input_trust_text'] ?? "Trusted by 300+ clients", ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <div class="partner" data-w-id="7ed915f2-e7a0-7698-b16b-25f03cf9b171"
               style="opacity:0;-webkit-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 40px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="partner-text">
              <div class="p-02" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>">
                <?= htmlspecialchars($hero['input_partners_heading'] ?? 'Trusted by leading brands', ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
            <div class="partner-outer">
              <div class="partner-wrap" data-admc-tb="panel_partners">
                <?php if (empty($heroPartners)): ?>
                  <?php for($i=1; $i<=3; $i++): ?><div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="<?= $dummyImg ?>"></div><?php endfor; ?>
                <?php else: ?>
                  <?php foreach ($heroPartners as $prt): ?>
                    <div class="partner-logo" data-admc-image="panel_partners" data-admc-id="<?= $prt['id'] ?>"><img alt="<?= htmlspecialchars($prt['input_name'], ENT_QUOTES, 'UTF-8') ?>" class="partner-logo-img" src="<?= fixImagePath($prt['image_1']) ?>"></div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="scroll-text-wrap" data-w-id="b84d2d52-ab61-6c00-c526-8f2d9cdc5053"
               style="opacity:0;-webkit-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="mouse-scroll-indicator" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>">
              <div class="mouse-wheel"></div>
            </div>
            <div class="heading-06"
                 data-admc-manage="settings_home_hero"
                 data-admc-id="<?= $hero['id'] ?? 1 ?>">
              <?= htmlspecialchars($hero['input_scroll_text'] ?? 'Scroll Down', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </div>
      </div>
      <!-- Hero media -->
      <div class="home-hero-img">
        <?php
          $heroVideo = trim($hero['input_video_url'] ?? '');
          $heroImage = trim($hero['image_1'] ?? '');
        ?>
        <?php if (!empty($heroVideo)): ?>
          <div style="position:relative;width:100%;height:100%;" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>">
            <video autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;display:block;"><source src="<?= htmlspecialchars($heroVideo, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4" /></video>
          </div>
        <?php elseif (!empty($heroImage)): ?>
          <div data-admc-image="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>" style="width:100%;height:100%;">
            <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>" alt="Hero background" style="width:100%;height:100%;object-fit:cover;display:block;">
          </div>
        <?php else: ?>
          <div data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>" style="width:100%;height:100%;background:#0d2b0d;display:flex;align-items:center;justify-content:center;">
            <p style="color:rgba(255,255,255,0.3);font-size:14px;">Add a video URL or image via ADMC</p>
          </div>
        <?php endif; ?>
      </div>
  </section>
</div>
<?php/*##cbcode_10001Heroc##*/>

<!-- FEATURED PRODUCTS -->
<?php/*##cbcode_10001Productso##*/>
<div data-cbcodesection="cbcode_10001Products">
  <section class="products section-120-120">
    <div class="container">
      <div class="header">
        <div class="header-left">
          <h2 class="heading-02" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>"><?= htmlspecialchars($hero['input_products_intro_heading'] ?? 'We believe skincare is a ritual, not a routine', ENT_QUOTES, 'UTF-8') ?></h2>
        </div>
        <div class="header-right">
          <div class="p-01" style="color: var(--primary);" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?? 1 ?>"><?= htmlspecialchars($hero['text_products_intro_description'] ?? 'Discover our selection.', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>

      <div class="home-cat-tabs" id="homeCatTabs">
        <button class="home-cat-btn active" data-cat="">All products</button>
        <?php foreach ($categories as $cat): ?>
          <button class="home-cat-btn" data-cat="<?= htmlspecialchars($cat['input_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cat['input_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></button>
        <?php endforeach; ?>
      </div>

      <div class="product-collection" id="productCollection">
      <?php
        $totalProductCount = count(selectContent($conn, "panel_product", ["visibility" => "show"]));
        $initialHasMore = $totalProductCount > 6;
      ?>
        <div class="product-grid w-dyn-items" id="homeProductGrid" data-admc-tb="panel_product" role="list">
          <?php foreach ($featuredProducts as $product):
             $detailUrl = $baseUrl . "/products/" . $product['hash_id'] . "/" . ($product['input_slug'] ?? cleans($product['input_product_name']));
          ?>
            <div class="product-card-wrap" data-admc-tb="panel_product">
                <div class="product-card">
                  <div class="product-card-img">
                    <!-- Stock Badge -->
                    <?php if ($product['total_stock'] <= 0): ?>
                      <div class="stock-badge-main badge-out-stock">Out of Stock</div>
                    <?php else: ?>
                      <div class="stock-badge-main badge-in-stock">In Stock</div>
                    <?php endif; ?>

                    <a class="product-link w-inline-block" href="<?= $detailUrl ?>">
                      <div data-admc-image="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <img alt="<?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?>" 
                             class="all-img" loading="lazy" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      </div>
                      <div class="product-float">
                        <img alt="" class="all-img" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'], ENT_QUOTES, 'UTF-8') ?>">
                      </div>
                    </a>
                    <?php $inWishlist = in_array($product['hash_id'], $wishlistIds); ?>
                    <button class="wishlist-btn-card <?= $inWishlist ? 'active' : '' ?>" 
                            data-id="<?= $product['hash_id'] ?>" 
                            onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.toggleWishlist('<?= $product['hash_id'] ?>', this);"
                            style="position:absolute; top:12px; left:12px; z-index:15; background:white; border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.1); opacity:0; transition:opacity 0.3s ease;">
                      <img src="<?= $baseUrl ?>/assets/img/icons/<?= $inWishlist ? 'heart-filled.svg' : 'heart-outline.svg' ?>" style="width:18px; height:18px;" alt="Wishlist">
                    </button>
                    <div class="add-to-card-02 <?= ($product['total_stock'] <= 0) ? 'btn-add-disabled' : '' ?>" 
                         data-product-id="<?= $product['hash_id'] ?>" 
                         data-has-variants="<?= $product['has_variants'] ?>"
                         onclick="event.preventDefault(); event.stopPropagation(); if(<?= ($product['total_stock'] <= 0 ? 'false' : 'true') ?> && window.Venora) window.Venora.cartAddItem('<?= $product['hash_id'] ?>', '', 1, null, this);">
                      <img alt="" class="add-to-card-icon" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg">
                      <div class="p-01"><?= ($product['total_stock'] <= 0) ? 'Unavailable' : 'Add to cart' ?></div>
                    </div>
                  </div>
                  <div class="product-card-bottom">
                    <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($product['_category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="product-name-price">
                      <h3 class="heading-06" data-admc-manage="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <a href="<?= $detailUrl ?>" class="card-title-link"><?= htmlspecialchars($product['input_product_name'], ENT_QUOTES, 'UTF-8') ?></a>
                      </h3>
                      <div class="heading-07" data-admc-manage="panel_product" data-admc-id="<?= $product['id'] ?>" data-admc-tb="panel_product">
                        <?= formatPrice($product['input_price'], $shop_symbol) ?>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div id="loadMoreWrap" style="text-align:center;margin-top:40px;<?= $initialHasMore ? 'display:block;' : 'display:none;' ?>">
          <button id="loadMoreBtn" data-admc-manage="settings_shop_ui_labels" data-admc-id="<?= $uiLabels['id'] ?? 1 ?>"
                  style="padding:14px 40px;border:1.5px solid #202c22;background:none;color:#202c22;font-family:inherit;font-size:15px;font-weight:600;border-radius:7px;cursor:pointer;transition:all 0.2s;"
                  onmouseover="this.style.background='#202c22';this.style.color='#fff';" onmouseout="this.style.background='none';this.style.color='#202c22';">
            <?= htmlspecialchars($loadMoreTxt, ENT_QUOTES, 'UTF-8') ?>
          </button>
        </div>
      </div>
      </div>
    </div>
  </section>
</div>
<?php/*##cbcode_10001Productsc##*/>

<script>
(function() {
  var activeCats  = []; var currentPage = 1; var perPage = 6; var isLoading = false;
  var grid = document.getElementById('homeProductGrid'); var loadMoreWrap = document.getElementById('loadMoreWrap'); var loadMoreBtn = document.getElementById('loadMoreBtn');
  var base = window.VENORA_BASE_URL || ''; var labelLoadMore = <?= json_encode($loadMoreTxt) ?>; var labelLoading = <?= json_encode($loadingTxt) ?>;
  function buildUrl(page) {
    var url = base + '/products-filter?page=' + page + '&limit=' + perPage;
    if (activeCats.length) url += '&cats=' + activeCats.map(encodeURIComponent).join(',');
    return url;
  }
  function setLoading(on) {
    isLoading = on; if (grid) grid.style.opacity = on ? '0.5' : '1';
    if (loadMoreBtn) { loadMoreBtn.disabled = on; loadMoreBtn.textContent = on ? labelLoading : labelLoadMore; }
  }
  function updateButtons() {
    document.querySelectorAll('.home-cat-btn').forEach(function(b) {
      var c = b.dataset.cat; b.classList.toggle('active', c === '' ? activeCats.length === 0 : activeCats.indexOf(c) !== -1);
    });
  }
  function renderProducts(products, append) {
    if (!grid) return; var html = products.map(function(p) { return p.html; }).join('');
    if (append) { grid.insertAdjacentHTML('beforeend', html); } else { grid.innerHTML = html || '<div style="padding:60px 20px;text-align:center;color:#b5b5b5;"><p>No products found.</p></div>'; }
    setTimeout(function() { document.dispatchEvent(new CustomEvent('admc:init')); }, 500);
  }
  function fetchProducts(page, append) {
    if (isLoading) return; setLoading(true);
    fetch(buildUrl(page), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); }).then(function(data) {
        setLoading(false); renderProducts(data.products || [], append); currentPage = page;
        if (loadMoreWrap) { loadMoreWrap.style.display = data.has_more ? 'block' : 'none'; }
      }).catch(function() { setLoading(false); });
  }
  document.querySelectorAll('.home-cat-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var cat = btn.dataset.cat; if (cat === '') { activeCats = []; } else { var idx = activeCats.indexOf(cat); if (idx === -1) activeCats.push(cat); else activeCats.splice(idx, 1); }
      updateButtons(); fetchProducts(1, false);
    });
  });
  if (loadMoreBtn) { loadMoreBtn.addEventListener('click', function() { fetchProducts(currentPage + 1, true); }); }
})();
</script>

<!-- FEATURES GRID -->
<div data-cbsection="cb3">
  <?php/*##cbcode_10001Featureso##*/>
  <?php
    $homeBlock1 = safeHomeFetch($conn, "panel_home_blocks", ["hash_id" => "hb001"]);
    $homeBlock1 = !empty($homeBlock1) ? $homeBlock1[0] : [];
    $homeBlock2 = safeHomeFetch($conn, "panel_home_blocks", ["hash_id" => "hb002"]);
    $homeBlock2 = !empty($homeBlock2) ? $homeBlock2[0] : [];
  ?>
  <section class="content section-0-120">
    <div class="container">
      <div class="content-outer">
        <div class="content-inner">
          <div class="content-img-box" data-admc-image="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>">
            <img alt="<?= htmlspecialchars($homeBlock1['input_heading'] ?? 'Tested', ENT_QUOTES, 'UTF-8') ?>" class="images speed" src="<?= fixImagePath($homeBlock1['image_1'] ?? $dummyImg) ?>" style="object-fit:cover; width:100%; height:100%;">
            <div class="content-float"><div data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>"><?= htmlspecialchars($homeBlock1['input_badge'] ?? 'Dermatologist tested', ENT_QUOTES, 'UTF-8') ?></div></div>
          </div>
          <div class="content-text">
            <div class="content-text-inner" data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock1['id'] ?? 0 ?>">
              <h2 class="heading-02"><?= htmlspecialchars($homeBlock1['input_heading'] ?? 'Dermatology-Tested', ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray"><div class="p-01"><?= htmlspecialchars($homeBlock1['text_description'] ?? 'Our formulas are developed in collaboration with dermatologists.', ENT_QUOTES, 'UTF-8') ?></div></div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg"><a class="btn-02-link w-inline-block" href="<?= htmlspecialchars($homeBlock1['input_btn_link'] ?? '/about', ENT_QUOTES, 'UTF-8') ?>"><div class="btn-inner"><div class="btn-text-wrap"><div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($homeBlock1['input_btn_label'] ?? 'About us', ENT_QUOTES, 'UTF-8') ?></div></div><div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($homeBlock1['input_btn_label'] ?? 'About us', ENT_QUOTES, 'UTF-8') ?></div></div></div></div></a></div>
          </div>
        </div>
        <div class="content-inner space-between">
          <div class="content-text">
            <div class="content-text-inner" data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>">
              <h2 class="heading-02"><?= htmlspecialchars($homeBlock2['input_heading'] ?? 'Clean. Paraben-Free.', ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray"><div class="p-01"><?= htmlspecialchars($homeBlock2['text_description'] ?? 'Your skin deserves only the best.', ENT_QUOTES, 'UTF-8') ?></div></div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg"><a class="btn-02-link w-inline-block" href="<?= htmlspecialchars($homeBlock2['input_btn_link'] ?? '/about', ENT_QUOTES, 'UTF-8') ?>"><div class="btn-inner"><div class="btn-text-wrap"><div class="btn-text-3 _01"><div class="cta-text"><?= htmlspecialchars($homeBlock2['input_btn_label'] ?? 'Learn more', ENT_QUOTES, 'UTF-8') ?></div></div><div class="btn-text-3 _02"><div class="cta-text"><?= htmlspecialchars($homeBlock2['input_btn_label'] ?? 'Learn more', ENT_QUOTES, 'UTF-8') ?></div></div></div></div></a></div>
          </div>
          <div class="content-img-box" data-admc-image="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>">
            <img alt="<?= htmlspecialchars($homeBlock2['input_heading'] ?? 'Paraben Free', ENT_QUOTES, 'UTF-8') ?>" class="images speed" src="<?= fixImagePath($homeBlock2['image_1'] ?? $dummyImg) ?>" style="object-fit:cover; width:100%; height:100%;">
            <div class="content-float _02"><div data-admc-manage="panel_home_blocks" data-admc-id="<?= $homeBlock2['id'] ?? 0 ?>"><?= htmlspecialchars($homeBlock2['input_badge'] ?? 'Paraben free', ENT_QUOTES, 'UTF-8') ?></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <?php
    $hfArr = safeHomeFetch($conn, "settings_home_features", ["visibility" => "show"]);
    $homeFeatures = !empty($hfArr) ? $hfArr[0] : [];
    $hfGallery = selectContent($conn, "images", ["asset_hash_id" => "hf001"]);
  ?>
  <section class="content section-0-120">
    <div class="container">
      <div class="content-inner-02">
        <div class="content-header" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
          <h2 class="heading-02"><?= htmlspecialchars($homeFeatures['input_heading'] ?? 'Why your skin deserves the best', ENT_QUOTES, 'UTF-8') ?></h2>
          <div class="content-header-short"><div class="color-gray"><div class="p-01"><?= htmlspecialchars($homeFeatures['text_subheading'] ?? 'Science, care, and transparency.', ENT_QUOTES, 'UTF-8') ?></div></div></div>
        </div>
        <div class="content-grid-wrap">
          <div class="content-grid" data-admc-tb="images" data-admc-tbadd="settings_home_features" data-admc-tblink="hf001">
            <div class="content-top">
              <div class="happy-client-card" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="happy-client-img-wrap" data-admc-image="images" data-admc-id="<?= $hfGallery[0]['id'] ?? 0 ?>"><img alt="Happy clients" class="happy-client-img" src="<?= fixImagePath($hfGallery[0]['image_1'] ?? $dummyImg) ?>"></div>
                <div class="happy-client-text"><div class="star-wrap"><img alt="Star" class="star" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a1e07895c1d9149a7ad_Star 1.svg"></div><div class="heading-05"><?= htmlspecialchars($homeFeatures['input_card1_title'] ?? '100k+ happy clients', ENT_QUOTES, 'UTF-8') ?></div></div>
              </div>
              <div class="content-card" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float"><div class="content-float-icon-box" data-admc-image="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>"><img alt="Icon" class="content-float-icon" src="<?= fixImagePath($homeFeatures['image_card2_icon'] ?? '') ?>"></div><div class="content-bottom-title"><h2 class="heading-04"><?= htmlspecialchars($homeFeatures['input_card2_title'] ?? 'Sense of luxury', ENT_QUOTES, 'UTF-8') ?></h2></div></div>
                <img alt="Luxury" class="content-float-img" src="<?= fixImagePath($hfGallery[1]['image_1'] ?? $dummyImg) ?>" data-admc-image="images" data-admc-id="<?= $hfGallery[1]['id'] ?? 0 ?>">
                <div class="content-overlay"></div>
              </div>
            </div>
            <div class="content-bottom">
              <div class="content-card" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float-02"><div class="content-top-title"><h3 class="heading-04"><?= htmlspecialchars($homeFeatures['input_card3_title'] ?? 'Shop easily', ENT_QUOTES, 'UTF-8') ?></h3></div></div>
                <img alt="Stores" class="content-float-img" src="<?= fixImagePath($hfGallery[2]['image_1'] ?? $dummyImg) ?>" data-admc-image="images" data-admc-id="<?= $hfGallery[2]['id'] ?? 0 ?>"><div class="content-overlay"></div>
              </div>
              <div class="content-card" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
                <div class="content-card-inner-float"><div class="content-float-icon-box" data-admc-image="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>"><img alt="Icon" class="content-float-icon" src="<?= fixImagePath($homeFeatures['image_card4_icon'] ?? '') ?>"></div><div class="content-bottom-title"><h3 class="heading-04"><?= htmlspecialchars($homeFeatures['input_card4_title'] ?? 'Natural ingredients', ENT_QUOTES, 'UTF-8') ?></h3></div></div>
                <img alt="Natural" class="content-float-img" src="<?= fixImagePath($hfGallery[3]['image_1'] ?? $dummyImg) ?>" data-admc-image="images" data-admc-id="<?= $hfGallery[3]['id'] ?? 0 ?>"><div class="content-overlay"></div>
              </div>
            </div>
          </div>
          <div class="content-card" data-admc-manage="settings_home_features" data-admc-id="<?= $homeFeatures['id'] ?? 1 ?>">
             <div class="content-card-inner-float large"><div class="content-bottom-title"><h3 class="heading-04"><?= htmlspecialchars($homeFeatures['input_card5_title'] ?? 'Visible results', ENT_QUOTES, 'UTF-8') ?></h3></div></div>
             <img alt="Results" class="content-float-img" src="<?= fixImagePath($hfGallery[4]['image_1'] ?? $dummyImg) ?>" data-admc-image="images" data-admc-id="<?= $hfGallery[4]['id'] ?? 0 ?>">
          </div>
        </div>
      </div>
    </section>
</div>

<!-- TESTIMONIAL SECTION -->
<div data-cbsection="cb4">
  <?php/*##cbcode_10001Testimonialo##*/>
  <?php
    $hqArr = safeHomeFetch($conn, "settings_home_quote", ["visibility" => "show"]);
    $homeQuote = !empty($hqArr) ? $hqArr[0] : [];
    $hqGallery = selectContent($conn, "images", ["asset_hash_id" => "hq001"]);
  ?>
  <section class="testimonial section-0-120">
    <div class="container">
      <div class="testimonial-inner">
        <div class="testimonial-wrap">
          <div class="testimonial-top" data-admc-manage="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <div class="qoute" style="font-size: 32px; color: var(--primary);"><i class="fas fa-quote-left"></i></div>
            <h3 class="heading-03"><?= htmlspecialchars($homeQuote['text_quote'] ?? 'Beauty is how you feel in your own skin.', ENT_QUOTES, 'UTF-8') ?></h3>
          </div>
          <div class="author-name" data-admc-manage="settings_home_quote" data-admc-id="<?= $homeQuote['id'] ?? 1 ?>">
            <?php if (!empty($homeQuote['image_author_signature'])): ?>
              <img alt="Author" class="author-img" src="<?= fixImagePath($homeQuote['image_author_signature']) ?>">
            <?php endif; ?>
          </div>
        </div>
        <div class="testimonial-img-wrap" data-admc-tb="images" data-admc-tbadd="settings_home_quote" data-admc-tblink="hq001">
          <?php if (empty($hqGallery)): ?>
            <?php for($i=1; $i<=3; $i++): ?><div class="testimonial-img"><img alt="Testimonial" class="all-img" src="<?= $dummyImg ?>"></div><?php endfor; ?>
          <?php else: ?>
            <?php foreach ($hqGallery as $hi => $img): ?>
              <div class="testimonial-img <?= $hi === 0 ? '_01' : ($hi === 2 ? '_02' : '') ?>"><img alt="Testimonial" class="all-img" src="<?= fixImagePath($img['image_1']) ?>" data-admc-image="images" data-admc-id="<?= $img['id'] ?>"></div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- [cbcode_10001c] -->

<script>
(function() {
  const initReveal = () => {
    const observerOptions = { threshold: 0.15, rootMargin: '0px 0px -50px 0px' };
    const revealElement = (el) => {
      el.style.setProperty('opacity', '1', 'important');
      el.style.setProperty('transform', 'translate3d(0, 0, 0) scale3d(1, 1, 1)', 'important');
      el.style.setProperty('transition', 'opacity 1s ease-out, transform 1s ease-out', 'important');
    };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) { revealElement(entry.target); observer.unobserve(entry.target); } });
    }, observerOptions);
    document.querySelectorAll('[data-w-id], [style*="opacity:0"]').forEach(el => observer.observe(el));
  };
  if (document.readyState === 'complete' || document.readyState === 'interactive') { setTimeout(initReveal, 100); } else { window.addEventListener('load', initReveal); }
})();
</script>

<style>
.w-background-video > video { background-image: none !important; background-color: transparent !important; }
[data-w-id], [style*="opacity:0"] { will-change: opacity, transform; }
@keyframes gallery-scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }
.gallery-wrapper { display: flex; overflow: hidden; white-space: nowrap; background-color: #ffffff; }
.gallery-card-wrap { display: flex; }
</style>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
