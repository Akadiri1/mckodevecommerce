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

$heroArr = safeHomeFetch($conn, "settings_shop_hero", ["visibility" => "show"]);
$hero    = !empty($heroArr) ? $heroArr[0] : [];

$featuredProducts = selectContent($conn, "panel_products", ["visibility" => "show"], 3);

// Pre-index variants (ADMC pattern: no queries inside loops)
$allFeaturedVariants = selectContent($conn, "addition_product_variants", ["visibility" => "show"]);
$variantsByProduct   = [];
foreach ($allFeaturedVariants as $fv) {
    $variantsByProduct[$fv['tb_link']] = true;
}
foreach ($featuredProducts as &$fp) {
    $fp['has_variants'] = isset($variantsByProduct[$fp['hash_id']]) ? "true" : "false";
}
unset($fp);

$categories       = selectContent($conn, "selection_product_category", ["visibility" => "show"], 4);

include APP_PATH . "/views/includes/header.php";
?>

<!-- [cbcode_10001o] -->

<!-- HERO SECTION -->
<div data-cbsection="cb1" style="margin:0;padding:0;line-height:0;font-size:0;">
  <!-- [cbcode_10001Heroo] -->
  <section class="home-hero" data-w-id="29aa9955-28b7-3f33-84f0-dfd0b6c1b7e0"
           style="margin:0!important;padding:0!important;">
    <div class="container home">
      <div class="home-hero-inner">
        <div></div>
        <div class="home-hero-top" data-w-id="56c8ce7c-05a8-e7f5-8c8d-22339360ef84">
          <h1 class="heading-01" 
              data-w-id="537cb9b7-edf7-dd89-7237-03e80fbacb5e"
              data-admc-manage="settings_shop_hero"
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
              <div class="client-img-box-wrap">
                <div class="client-img-box _01"><img alt="Client" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/693c53af28f5c72b2dd095ad_Rectangle 1089.avif"></div>
                <div class="client-img-box _02"><img alt="Client" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/693c53b06c754a756552a52b_Rectangle 1074.avif"></div>
                <div class="client-img-box _03"><img alt="Client" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/693c53b00537c8c214ac084d_Rectangle 1089-1.avif"></div>
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
            <div class="partner-text"><div class="p-02">Trusted by leading brands</div></div>
            <div class="partner-outer">
              <div class="partner-wrap">
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a348e5306c0feaf17c2_logoipsum-265 (1) 1.svg"></div>
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a34d6d7c7a57259bc81_logoipsum-216 1.svg"></div>
                <div class="partner-logo"><img alt="Partner" class="partner-logo-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a34dac7c1876b03c715_logoipsum-213 1.svg"></div>
              </div>
            </div>
          </div>
          <div class="scroll-text-wrap" data-w-id="b84d2d52-ab61-6c00-c526-8f2d9cdc5053"
               style="opacity:0;-webkit-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            <div class="scroll-icon-wrap">
              <img alt="" class="scroll-icon _01" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692a7fe172c48a17fb624381_Vector (1).svg">
            </div>
            <div class="heading-06">Scroll Down</div>
          </div>
        </div>
      </div>
      <div class="home-hero-img">
        <div class="all-img w-background-video w-background-video-atom" data-autoplay="true" data-loop="true"
          data-video-urls="<?= htmlspecialchars($hero['input_video_url'] ?? "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692cb59221dce1af58faaabd_7304311-hd_1920_1080_30fps_mp4.mp4", ENT_QUOTES, 'UTF-8') ?>"
          data-w-id="ad63aa5c-436f-c615-1239-db03bfa67501" data-wf-ignore="true">
          <video autoplay loop muted playsinline data-object-fit="cover" data-wf-ignore="true" id="ad63aa5c-436f-c615-1239-db03bfa67501-video">
            <source data-wf-ignore="true" src="<?= htmlspecialchars($hero['input_video_url'] ?? "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692cb59221dce1af58faaabd_7304311-hd_1920_1080_30fps_mp4.mp4", ENT_QUOTES, 'UTF-8') ?>" />
          </video>
        </div>
      </div>
    </section>
    <!-- [cbcode_10001Heroc] -->
</div>

<!-- FEATURED PRODUCTS -->
<div data-cbsection="cb2">
  <!-- [cbcode_10001Productso] -->
  <section class="products section-120-120">
    <div class="container">
      <div class="header">
        <div class="header-left">
          <h2 class="heading-02">We believe skincare is a ritual, not a routine</h2>
        </div>
        <div class="header-right">
          <div class="p-01">Discover our curated selection of products designed to highlight your unique beauty.</div>
        </div>
      </div>

      <!-- Category filter tabs — multi-select AJAX -->
      <div class="home-cat-tabs" id="homeCatTabs">
        <button class="home-cat-btn active" data-cat="">All products</button>
        <?php foreach ($categories as $cat): ?>
          <button class="home-cat-btn" data-cat="<?= htmlspecialchars($cat['input_name'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($cat['input_name'], ENT_QUOTES, 'UTF-8') ?>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="product-collection">
        <div class="product-grid w-dyn-items" id="homeProductGrid" role="list">
          <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card-wrap">
              <a class="product-link w-inline-block" href="<?= $baseUrl ?>/products/<?= $product['hash_id'] ?>/<?= $product['input_slug'] ?? '' ?>">
                <div class="product-card">
                  <div class="product-card-img">
                    <img alt="<?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?>" 
                         class="all-img" loading="lazy" src="<?= htmlspecialchars($product['image_1'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="product-float">
                      <img alt="" class="all-img" src="<?= htmlspecialchars($product['image_2'] ?? $product['image_1'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    
                    <!-- Quick View Button -->
                    <button class="quick-view-btn" data-id="<?= $product['hash_id'] ?>" 
                            onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.openQuickView('<?= $product['hash_id'] ?>');" aria-label="Quick view">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                      </svg>
                    </button>

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
                    <div class="color-gray"><div class="p-02 caps"><?= htmlspecialchars($product['select_category'] ?? "Skincare", ENT_QUOTES, 'UTF-8') ?></div></div>
                    <div class="product-name-price">
                      <div class="heading-06"><?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?></div>
                      <div class="heading-07"><?= $shop_symbol ?><?= number_format($product['input_price'], 2) ?></div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <!-- [cbcode_10001Productsc] -->
</div>

<script>
(function() {
  var activeCats = []; // multi-select list
  var grid = document.getElementById('homeProductGrid');
  var baseUrl = window.VENORA_BASE_URL || '';

  function setLoading(isLoading) {
    if (grid) grid.style.opacity = isLoading ? '0.4' : '1';
  }

  function updateButtons() {
    document.querySelectorAll('.home-cat-btn').forEach(function(b) {
      var c = b.dataset.cat;
      if (c === '') {
        b.classList.toggle('active', activeCats.length === 0);
      } else {
        b.classList.toggle('active', activeCats.indexOf(c) !== -1);
      }
    });
  }

  function fetchProducts() {
    setLoading(true);
    var url = baseUrl + '/products-filter';
    if (activeCats.length) url += '?cats=' + activeCats.map(encodeURIComponent).join(',');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        setLoading(false);
        if (!grid) return;
        if (!data.products || !data.products.length) {
          grid.innerHTML = '<div style="padding:60px 20px;text-align:center;color:#b5b5b5;"><p>No products found in this category.</p></div>';
          return;
        }
        grid.innerHTML = data.products.map(function(p) { return p.html; }).join('');
      })
      .catch(function() { setLoading(false); });
  }

  document.querySelectorAll('.home-cat-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var cat = btn.dataset.cat;

      if (cat === '') {
        // Reset — show all
        activeCats = [];
      } else {
        var idx = activeCats.indexOf(cat);
        if (idx === -1) {
          activeCats.push(cat);   // add to selection
        } else {
          activeCats.splice(idx, 1); // remove from selection
        }
      }

      updateButtons();
      fetchProducts();
    });
  });
})();
</script>

<!-- FEATURES GRID -->
<div data-cbsection="cb3">
  <!-- [cbcode_10001Featureso] -->
  <section class="content section-0-120">
    <div class="container">
      <div class="content-outer">
        <div class="content-inner">
          <div class="content-img-box">
            <img alt="Dermatology Tested" class="images speed" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691940b886702b2d2296b5f3_Rectangle 1071.avif">
            <div class="content-float"><div>Dermatologist tested</div></div>
          </div>
          <div class="content-text" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <div class="content-text-inner">
              <h2 class="heading-02">Dermatology-Tested Skincare You Can Trust</h2>
              <div class="color-gray">
                <div class="p-01">Our formulas are developed in collaboration with dermatologists to ensure maximum comfort and visible results for every skin type.</div>
              </div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg">
              <a class="btn-02-link w-inline-block" href="/about">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text">About us</div></div>
                    <div class="btn-text-3 _02"><div class="cta-text">About us</div></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="content-inner space-between">
          <div class="content-text" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <div class="content-text-inner">
              <h2 class="heading-02">Naturally Clean. Always Paraben-Free.</h2>
              <div class="color-gray">
                <div class="p-01">Your skin deserves only the best. That’s why every product we create is 100% paraben-free, formulated with gentle and natural ingredients.</div>
              </div>
            </div>
            <div class="btn-wrap" data-wf--btn--variant="green-bg">
              <a class="btn-02-link w-inline-block" href="/about">
                <div class="btn-inner">
                  <div class="btn-text-wrap">
                    <div class="btn-text-3 _01"><div class="cta-text">Learn more</div></div>
                    <div class="btn-text-3 _02"><div class="cta-text">Learn more</div></div>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div class="content-img-box">
            <img alt="Paraben Free" class="images speed" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ec862043fc0be46ab_Rectangle 1072.avif">
            <div class="content-float _02"><div>Paraben free</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHY YOUR SKIN DESERVES THE BEST -->
  <section class="content section-0-120">
    <div class="container">
      <div class="content-inner-02">
        <div class="content-header">
          <h2 class="heading-02">Why your skin deserves the best</h2>
          <div class="content-header-short">
            <div class="color-gray">
              <div class="p-01">We combine science, care, and transparency to create skincare you can truly trust.</div>
            </div>
          </div>
        </div>
        <div class="content-grid-wrap">
          <div class="content-grid" id="w-node-_78aac54f-d88f-5629-2fa1-44ccd4192e8e-50693cae">
            <div class="content-top">
              <div class="happy-client-card" style="opacity:0;transform:translate3d(0, 40px, 0);">
                <div class="happy-client-img-wrap"><img alt="Happy clients" class="happy-client-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0d2b63b5a131ce1cdeef_Group 1171274846.avif"></div>
                <div class="happy-client-text">
                  <div class="star-wrap">
                    <img alt="Star" class="star" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69192a1e07895c1d9149a7ad_Star 1.svg">
                  </div>
                  <div class="heading-05">100k+ happy clients</div>
                </div>
              </div>
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);">
                <div class="content-card-inner-float">
                  <div class="content-float-icon-box"><img alt="" class="content-float-icon" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0ca882f9f72fca4b3c57_Vector (1).svg"></div>
                  <div class="content-bottom-title"><h2 class="heading-04">Connect products with a sense of luxury and self-care</h2></div>
                </div>
                <img alt="Luxury" class="content-float-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c1b4c08ab15934abb_Rectangle 1081.avif">
                <div class="content-overlay"></div>
              </div>
            </div>
            <div class="content-bottom">
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);">
                <div class="content-card-inner-float-02">
                  <div class="content-top-title"><h3 class="heading-04">Shop easily online or in our stores</h3></div>
                </div>
                <img alt="Stores" class="content-float-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c07895c1d914abe40_Textured Green Surface 1.avif">
                <div class="content-overlay"></div>
              </div>
              <div class="content-card" style="opacity:0;transform:translate3d(0, 40px, 0);">
                <div class="content-card-inner-float">
                   <div class="content-float-icon-box"><img alt="" class="content-float-icon" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a1f7658c01e989aa647c6_Vector (4).svg"></div>
                   <div class="content-bottom-title"><h3 class="heading-04">Natural ingredients with proven effects</h3></div>
                </div>
                <img alt="Natural" class="content-float-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3d62dfb2e1c46b2f45_Rectangle 16.avif">
                <div class="content-overlay"></div>
              </div>
            </div>
          </div>
          <div class="content-card" id="w-node-_5df0eadb-5d65-6750-d614-b195a45c0318-50693cae" style="opacity:0;transform:translate3d(0, 40px, 0);">
             <div class="content-card-inner-float large">
                <div class="content-bottom-title">
                   <h3 class="heading-04">Visible results in just 2 weeks</h3>
                </div>
             </div>
             <img alt="Results" class="content-float-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ecb604588eb85d1dd_Rectangle 1082.avif">
          </div>
        </div>
      </div>
    </section>
  <!-- [cbcode_10001Featuresc] -->
</div>

<!-- TESTIMONIAL SECTION -->
<div data-cbsection="cb4">
  <!-- [cbcode_10001Testimonialo] -->
  <section class="testimonial section-0-120">
    <div class="container">
      <div class="testimonial-inner">
        <div class="testimonial-wrap">
          <div class="testimonial-top" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <div class="qoute"><img alt="Quote" class="qoute-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a259ecc1639613202a8e0_“.svg"></div>
            <h3 class="heading-03">Beauty is not just what you see in the mirror - it’s how you feel in your own skin. At Venora, every product is crafted to empower that feeling.</h3>
          </div>
          <div class="author-name" style="opacity:0;transform:translate3d(0, 40px, 0);">
            <img alt="Dr. Isabella Hartman" class="author-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/6924b08072d2458c6c880ee4_- Dr. Isabella Hartman.svg">
          </div>
        </div>
        <div class="testimonial-img-wrap">
          <div class="testimonial-img _01"><img alt="Nature" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b57599b3f5cb6f09057_Rectangle 25.avif"></div>
          <div class="testimonial-img"><img alt="Skincare ritual" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b5826fef698896435c0_Rectangle 24.avif"></div>
          <div class="testimonial-img _02"><img alt="Detail" class="all-img" src="https://cdn.prod.website-files.com/6918bd445678e83950693c7b/693c3d2fdcd9e59dc0cba40d_Rectangle 1144.avif"></div>
        </div>
      </div>
    </div>
  </section>
  <!-- [cbcode_10001Testimonialc] -->
</div>

<!-- GALLERY TICKER -->
<div data-cbsection="cb5">
  <!-- [cbcode_10001Tickero] -->
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
  <!-- [cbcode_10001Tickerc] -->
</div>

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