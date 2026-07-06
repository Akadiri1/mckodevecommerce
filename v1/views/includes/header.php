<?php
// ── Fetch nav from panel_pages (ADMC compliant — DB-driven) ──
$navPages = selectContentAsc($conn, "panel_pages", ["visibility" => "show"], "input_order", 15);
$navCategories = selectContentAsc($conn, "selection_product_category", ["visibility" => "show"], "id", 10);
$currentPath = '/' . ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$currentPath = rtrim(str_replace($baseUrl, '', $currentPath), '/') ?: '/';
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?> <?= !empty($page_title) ? "— " . htmlspecialchars($page_title, ENT_QUOTES, "UTF-8") : "" ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, "UTF-8") ?>">
  <meta name="keywords"    content="<?= htmlspecialchars($metakeys, ENT_QUOTES, "UTF-8") ?>">
  <meta property="og:title"       content="<?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?>">
  <meta property="og:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, "UTF-8") ?>">
  <meta property="og:image"       content="<?= htmlspecialchars($metaImage, ENT_QUOTES, "UTF-8") ?>">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:type"        content="website">
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, "UTF-8") ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($metaImage, ENT_QUOTES, "UTF-8") ?>">

  <?php if (!empty($fetchFavicon[0]["image_1"])): ?>
    <link rel="icon" href="<?= htmlspecialchars($fetchFavicon[0]["image_1"], ENT_QUOTES, "UTF-8") ?>">
  <?php else: ?>
    <link rel="icon" href="<?= $baseUrl ?>/assets/img/brand/venora-white.svg" type="image/svg+xml">
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Venora CSS + Custom CSS -->
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/venora.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/custom.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <?php
  // ── ADMC Theme & Colour System ──────────────────────────────
  $style = !empty($websiteStyle) ? $websiteStyle[0] : [];
  
  // Refactor: Use 'color' for Primary Text/Brand and 'secondary_color' for Backgrounds
  $primaryColor = $style['color'] ?? '#072708';
  if ($primaryColor && strpos($primaryColor, '#') !== 0) { $primaryColor = '#' . $primaryColor; }
  
  $bgColor = $style['secondary_color'] ?? '#f9f9f7';
  if ($bgColor && strpos($bgColor, '#') !== 0) { $bgColor = '#' . $bgColor; }

  // Mapped tokens for internal consistency
  $textHead  = $primaryColor;
  $textBody  = '#5c5f6a';
  $textMuted = '#9ca3af';
  // Hex to RGB for primary colour
  $hex = ltrim($primaryColor, '#');
  if (strlen($hex) == 3) {
      $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
      $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
      $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
  } else {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
  }
  $primaryRgb = "$r, $g, $b";
  ?>
  <style data-admc-manage="website_status" data-admc-id="<?= htmlspecialchars($style['id'] ?? '1', ENT_QUOTES, 'UTF-8') ?>">
    :root {
      /* Dynamic Template Defaults from ADMC */
      --primary: <?= htmlspecialchars($primaryColor, ENT_QUOTES, 'UTF-8') ?>;
      --primary-rgb: <?= htmlspecialchars($primaryRgb, ENT_QUOTES, 'UTF-8') ?>;
      --dark-green-colour: var(--primary);
      --dash-accent: var(--primary);
      
      --bg-colour: #f9f9f7;
      --v-bg-dark: var(--primary);
      
      --surface-colour: #ffffff;
      --v-white: #ffffff;
      
      --text-primary: <?= htmlspecialchars($textHead, ENT_QUOTES, 'UTF-8') ?>;
      --text-secondary: <?= htmlspecialchars($textBody, ENT_QUOTES, 'UTF-8') ?>;
      --v-gray: <?= htmlspecialchars($textMuted, ENT_QUOTES, 'UTF-8') ?>;
    }
    
    /* Dynamic Color Overrides for Elements Not Previously Using ADMC Variables */
    h1:not(.white), h2:not(.white), h3:not(.white), h4:not(.white), h5:not(.white), h6:not(.white),
    .heading-01, .heading-02, .heading-03, .heading-04, .heading-05, .heading-06, .heading-07,
    .tagline, .product-name, .price, .variant-title, .product-price,
    .p-02.caps, .home-cat-btn, a.card-title-link {
      color: var(--primary) !important;
    }

    /* Keep footer and button text white */
    .footer-inner .heading-01, .footer-inner .heading-02, .footer-inner .heading-03, 
    .footer-inner .heading-04, .footer-inner .heading-05, .footer-inner .heading-06, 
    .footer-inner .p-01, .footer-inner .p-02, .footer-inner .footer-link,
    .btn-text, .submit-button, .btn-01, .btn-text-2, .btn-text-3 {
      color: #ffffff !important;
    }

    /* iOS Safari Fix: Prevent absolute images from stretching proportionally */
    .content-card {
      height: 100% !important; /* Forces Safari to resolve height for absolute children */
      display: flex !important;
      flex-direction: column;
    }
    .content-float-img {
      inset: 0 !important;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      height: 100% !important;
      width: 100% !important;
      object-fit: cover !important;
    }
    .content-card-inner-float, .content-card-inner-float-02 {
      inset: 0 !important;
    }
    
    .footer-inner .heading-07, .footer-inner .tagline,
    .btn-01-link .cta-text, .btn-02-link .cta-text, .submit-button-02 span, .load-more-btn, #loadMoreBtn, .add-to-card-02 .p-01,
    .home-cat-btn.active, .home-cat-btn:hover, .modal-add-to-cart, .cart-checkout-btn, .place-order-btn, .toast:not(.error):not(.warning),
    .badge-in-stock, .stock-high {
      color: #ffffff !important;
    }

    /* -------------------------------------------------------------
       Navbar Dynamic Styling
       - White text/icons on the transparent hero banner
       - Primary color text/icons when scrolled or on light pages
       ------------------------------------------------------------- */
    
    /* 1. Transparent Hero Banner State (Home Page, Not Scrolled) */
    body:not(.page-light-navbar) .navbar:not(.scrolled) .navbar-link, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .navbar-link .p-01, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .nav-list-item .navbar-link .p-01,
    body:not(.page-light-navbar) .navbar:not(.scrolled) .navbar-link.w--current, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .navbar-link.w--current .p-01 {
      color: #ffffff !important;
    }
    
    body:not(.page-light-navbar) .navbar:not(.scrolled) [data-open-search] svg, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .wishlist-btn-wrap svg, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .nav-icon-btn svg,
    body:not(.page-light-navbar) .navbar:not(.scrolled) .cart-icon-svg {
      color: #ffffff !important;
      stroke: #ffffff !important;
    }
    body:not(.page-light-navbar) .navbar:not(.scrolled) [data-open-search] svg path, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) [data-open-search] svg circle,
    body:not(.page-light-navbar) .navbar:not(.scrolled) .wishlist-btn-wrap svg path, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .nav-icon-btn svg path, 
    body:not(.page-light-navbar) .navbar:not(.scrolled) .nav-icon-btn svg circle,
    body:not(.page-light-navbar) .navbar:not(.scrolled) .cart-icon-svg path,
    body:not(.page-light-navbar) .navbar:not(.scrolled) .cart-icon-svg circle {
      stroke: #ffffff !important;
    }

    /* 2. Scrolled State OR Light Pages (Needs Primary Color) */
    .navbar.scrolled .navbar-link, 
    .navbar.scrolled .navbar-link .p-01, 
    .navbar.scrolled .nav-list-item .navbar-link .p-01,
    .navbar.scrolled .navbar-link.w--current, 
    .navbar.scrolled .navbar-link.w--current .p-01,
    body.page-light-navbar .navbar-link, 
    body.page-light-navbar .navbar-link .p-01, 
    body.page-light-navbar .nav-list-item .navbar-link .p-01,
    body.page-light-navbar .navbar-link.w--current, 
    body.page-light-navbar .navbar-link.w--current .p-01 {
      color: var(--primary) !important;
    }

    .navbar.scrolled [data-open-search] svg, 
    .navbar.scrolled .wishlist-btn-wrap svg, 
    .navbar.scrolled .nav-icon-btn svg,
    .navbar.scrolled .cart-icon-svg,
    body.page-light-navbar [data-open-search] svg, 
    body.page-light-navbar .wishlist-btn-wrap svg, 
    body.page-light-navbar .nav-icon-btn svg,
    body.page-light-navbar .cart-icon-svg {
      color: var(--primary) !important;
      stroke: var(--primary) !important;
    }
    .navbar.scrolled [data-open-search] svg path, 
    .navbar.scrolled [data-open-search] svg circle,
    .navbar.scrolled .wishlist-btn-wrap svg path, 
    .navbar.scrolled .nav-icon-btn svg path, 
    .navbar.scrolled .nav-icon-btn svg circle,
    .navbar.scrolled .cart-icon-svg path,
    .navbar.scrolled .cart-icon-svg circle,
    body.page-light-navbar [data-open-search] svg path, 
    body.page-light-navbar [data-open-search] svg circle,
    body.page-light-navbar .wishlist-btn-wrap svg path, 
    body.page-light-navbar .nav-icon-btn svg path, 
    body.page-light-navbar .nav-icon-btn svg circle,
    body.page-light-navbar .cart-icon-svg path,
    body.page-light-navbar .cart-icon-svg circle {
      stroke: var(--primary) !important;
    }

    .footer-social-link-02, .footer-social-link-02:hover {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-01-link, .btn-02-link, .submit-button-02, .w-button, .w-commerce-commercecartapplepaybutton, .w-commerce-commercecartquickcheckoutbutton,
    .home-cat-btn.active, .btn-01-link:hover, .btn-02-link:hover, .submit-button-02:hover, .w-button:hover, 
    .w-commerce-commercecartapplepaybutton:hover, .w-commerce-commercecartquickcheckoutbutton:hover, .home-cat-btn:hover,
    .modal-add-to-cart, .modal-add-to-cart:hover, .cart-checkout-btn, .cart-checkout-btn:hover, .place-order-btn, .place-order-btn:hover,
    .cart-badge, .cart-count-badge, .toast:not(.error):not(.warning), .badge-in-stock, .stock-high {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .load-more-btn, .load-more-btn:hover, #loadMoreBtn, #loadMoreBtn:hover {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .add-to-card-02, .add-to-card-02:hover {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }
    
    /* Slight darkening effect on hover for interactivity */
    .footer-social-link-02:hover, .btn-01-link:hover, .btn-02-link:hover, .submit-button-02:hover, .w-button:hover, 
    .w-commerce-commercecartapplepaybutton:hover, .w-commerce-commercecartquickcheckoutbutton:hover,
    .home-cat-btn:hover, .load-more-btn:hover, #loadMoreBtn:hover, .add-to-card-02:hover,
    .modal-add-to-cart:hover, .cart-checkout-btn:hover, .place-order-btn:hover {
      filter: brightness(0.85) !important;
    }
    
    body {
      background-color: var(--bg-colour);
      color: var(--text-secondary);
    }
    
    /* Professional Contrast Enforcement — Must STAY White on Primary Buttons */
    .btn-02-link, .submit-button-02, .modal-add-to-cart, 
    .v-badge, .add-to-card-02, .cart-checkout-btn, .cart-badge, .cart-count-badge,
    .btn-text-wrap .cta-text, .submit-button-02 span,
    .happy-client-card .heading-05, .product-card .add-to-card-02 .p-01,
    .nav-icon-btn span[style*='background:#16a34a'],
    .place-order-btn, .newsletter-popup-btn,
    .cart-drawer-count {
      color: #ffffff !important;
    }
    
    /* Footer strictly stays dark with white text */
    .footer-section h1, .footer-section h2, .footer-section h3, 
    .footer-section .p-01, .footer-section .p-02, .footer-section a {
      color: #ffffff !important;
    }
    
    /* Force white icons in primary brand elements */
    .btn-02-link svg, .submit-button-02 svg, .modal-add-to-cart svg,
    .add-to-card-02 img { 
      filter: brightness(0) invert(1) !important;
      stroke: #ffffff !important; 
    }

    /* ── Global Section Spacing Reduction ───────────────────────────────── */
    .section-01 { padding-top: 80px !important; padding-bottom: 80px !important; }
    .section-02 { padding-bottom: 80px !important; }
    .section-120-120, .hero-section { 
      padding-top: 60px !important; 
      padding-bottom: 60px !important; 
    }
    .section-140-140 { 
      padding-top: 70px !important; 
      padding-bottom: 70px !important; 
    }
    .section-0-120, .testimonial.section-0-120 { 
      padding-bottom: 60px !important; 
    }
    
    @media screen and (max-width: 767px) {
      .section-01 { padding-top: 40px !important; padding-bottom: 40px !important; }
      .section-02 { padding-bottom: 40px !important; }
      .section-120-120, .hero-section, .section-0-120 { 
        padding-top: 40px !important; 
        padding-bottom: 40px !important; 
      }
      .section-140-140 { 
        padding-top: 50px !important; 
        padding-bottom: 50px !important; 
      }
    }
    
    /* Components */
    .cart-drawer { background: #ffffff !important; border-left: 1px solid #eee; }
    .cart-drawer-header { border-bottom-color: #eee; }
    .cart-drawer-title { color: var(--text-primary) !important; }
    .cart-item { border-bottom-color: #f5f5f5; }
    .cart-item h4 { color: var(--text-primary) !important; }
    .cart-item-variant { color: var(--text-secondary) !important; }
    .cart-item-price { color: var(--primary) !important; }
    .cart-subtotal-label, .cart-subtotal-value { color: var(--text-primary) !important; }
    .cart-subtotal-note { color: var(--text-secondary) !important; }
    
    .product-name-price .heading-06 a { color: var(--text-primary) !important; }
  </style>



  <!-- Pass PHP vars to JS -->
  <script>
    window.VENORA_BASE_URL = "<?= rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') ?>";
    window.VENORA_CURRENCY_SYMBOL = "<?= htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8") ?>";
    window.VENORA_CART_COUNT = <?= (int)$cartCount ?>;
  </script>
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '', ENT_QUOTES, 'UTF-8') ?>">
<div class="page-wrapper">
  <!-- ── Navbar ──────────────────────────────────────────────── -->
  <div class="header-navbar" data-wf--navbar--variant="white-2">
    <nav class="navbar w-nav" role="banner">
      <div class="navbar-container">
        <div class="navbar-wrapper">

          <!-- Logo — from settings_website_info -->
          <div class="nav-left">
            <a href="<?= $baseUrl ?>/" class="navbar-brand w-nav-brand" style="display:flex;align-items:center;text-decoration:none;">
              <div data-admc-image="settings_website_info"
                   data-admc-id="<?= $websiteInfoRow[0]['id'] ?? 1 ?>">
                <img alt="<?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?>"
                     class="nav-logo-icon"
                     loading="lazy"
                     src="<?= htmlspecialchars($logo_directory, ENT_QUOTES, 'UTF-8') ?>"
                     style="max-height:40px;max-width:160px;width:auto;height:auto;object-fit:contain;display:block;">
              </div>
            </a>
          </div>

          <!-- Nav links — DB-driven from panel_pages (ADMC compliant) -->
          <nav class="nav-menu-wrapper w-nav-menu" role="navigation"
               data-admc-tb="panel_pages">
            <ul class="nav-menu-two w-list-unstyled" role="list">
              <?php foreach ($navPages as $navPage):
                $rawLink2  = $navPage['input_link'];
                $navLink   = $baseUrl . ($rawLink2 === '/' ? '/' : rtrim($rawLink2, '/'));
                $navName   = htmlspecialchars($navPage['input_name'], ENT_QUOTES, 'UTF-8');
                $linkPath  = rtrim($rawLink2, '/') ?: '/';
                $isActive  = ($currentPath === $linkPath) || ($linkPath === '/' && $currentPath === '');
              ?>
                <li class="nav-list-item">
                  <a class="navbar-link w-inline-block <?= $isActive ? 'w--current' : '' ?>"
                     href="<?= $navLink ?>"
                     data-admc-manage="panel_pages"
                     data-admc-id="<?= $navPage['id'] ?>">
                    <div class="btn-text _01"><div class="p-01"><?= $navName ?></div></div>
                    <div class="btn-text _02"><div class="p-01"><?= $navName ?></div></div>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>

          <!-- Right: search + cart + CTA -->
          <div class="nav-right">
            <div class="nav-buttons-wrapper">

              <!-- Search -->
              <button class="nav-icon-btn" data-open-search aria-label="Search"
                      style="background:none;border:none;cursor:pointer;padding:8px;color:inherit;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
              </button>

              <!-- Account -->
              <a href="<?= $isCustomerLoggedIn ? $baseUrl . '/customer-dashboard' : $baseUrl . '/customer-login' ?>"
                 class="nav-icon-btn w-inline-block"
                 aria-label="<?= $isCustomerLoggedIn ? 'My Account' : 'Sign In' ?>"
                 style="position:relative;display:flex;align-items:center;justify-content:center;padding:8px;color:inherit;text-decoration:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <?php if ($isCustomerLoggedIn): ?>
                  <span style="position:absolute;top:4px;right:4px;width:7px;height:7px;background:#16a34a;border-radius:50%;border:1.5px solid #fff;"></span>
                <?php endif; ?>
              </a>

              <!-- Wishlist -->
              <div class="wishlist-btn-wrap">
                <a href="<?= $baseUrl ?>/wishlist" class="nav-icon-btn w-inline-block" aria-label="Wishlist"
                   style="position:relative;display:flex;align-items:center;justify-content:center;padding:8px;color:inherit;text-decoration:none;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                  </svg>
                  <div class="cart-count-badge <?= $wishlistCount > 0 ? 'has-items' : '' ?>"
                       id="wishlistBadge" style="top:2px;right:2px;"><?= $wishlistCount ?></div>
                </a>
              </div>

              <!-- Cart -->
              <div class="cart-btn-wrap" data-open-cart>
                <a href="#" class="cart-button w-inline-block" aria-label="Open cart" data-open-cart style="position:relative;display:flex;align-items:center;justify-content:center;padding:8px;color:inherit;text-decoration:none;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon-svg">
                    <path d="M3 3h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96a2 2 0 0 0 2 2h12v-2H7.42a.25.25 0 0 1-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 20 4H5.21l-.94-2H1z"/>
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                  </svg>
                  <div class="cart-count-badge <?= $cartCount > 0 ? 'has-items' : '' ?>"
                       id="cartBadge"><?= $cartCount ?></div>
                </a>
              </div>

              <!-- Book a Call CTA -->
              <div class="nav-btn">
                <div class="btn-wrap">
                  <a class="btn-02-link w-inline-block" href="<?= $baseUrl ?>/contact"
                     data-admc-manage="settings_shop_config"
                     data-admc-id="<?= $shopConfig[0]["id"] ?? 1 ?>">
                    <div class="btn-inner">
                      <div class="btn-text-wrap">
                        <div class="btn-text-3 _01"><div class="cta-text">Book a Call</div></div>
                        <div class="btn-text-3 _02"><div class="cta-text">Book a Call</div></div>
                      </div>
                    </div>
                  </a>
                </div>
              </div>

            </div>
          </div>

          <!-- Mobile hamburger -->
          <div class="menu-button-2 w-nav-button" id="mobileMenuToggle">
            <div class="menu-icon-lines" style="display:flex;flex-direction:column;gap:5px;cursor:pointer;padding:8px;">
              <span class="hamburger-line"></span>
              <span class="hamburger-line"></span>
              <span class="hamburger-line"></span>
            </div>
          </div>

        </div>
      </div>
    </nav>
  </div>

  <!-- ── Mobile Nav Sidebar ────────────────────────────────────── -->
  <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
  <div class="mobile-nav-sidebar" id="mobileNavSidebar">
    <div class="mobile-nav-header">
      <a href="<?= $baseUrl ?>/" class="mobile-nav-logo">
        <img src="<?= htmlspecialchars($logo_directory, ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') ?>"
             style="max-height:32px;max-width:140px;width:auto;height:auto;object-fit:contain;">
      </a>
      <button class="mobile-nav-close" id="mobileNavClose" aria-label="Close menu">✕</button>
    </div>

    <nav class="mobile-nav-links">
      <?php foreach ($navPages as $navPage):
        $rawLink  = $navPage['input_link'];
        // Preserve root '/' — don't strip it to empty string
        $navLink  = $baseUrl . ($rawLink === '/' ? '/' : rtrim($rawLink, '/'));
        $navName  = htmlspecialchars($navPage['input_name'], ENT_QUOTES, 'UTF-8');
        $linkPath = rtrim($rawLink, '/') ?: '/';
        $isActive = ($currentPath === $linkPath);
      ?>
        <a href="<?= $navLink ?>" class="mobile-nav-link <?= $isActive ? 'active' : '' ?>">
          <?= $navName ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="mobile-nav-footer">
      <?php if ($isCustomerLoggedIn): ?>
        <a href="<?= $baseUrl ?>/customer-dashboard" class="mobile-nav-account-btn">
          My Account
        </a>
        <a href="<?= $baseUrl ?>/customer-logout" class="mobile-nav-logout">Sign out</a>
      <?php else: ?>
        <a href="<?= $baseUrl ?>/customer-login" class="mobile-nav-account-btn">
          Sign In / Create Account
        </a>
      <?php endif; ?>
      <a href="<?= $baseUrl ?>/contact" class="mobile-nav-cta">Book a Call</a>
    </div>
  </div>

  <!-- ── Cart Drawer ──────────────────────────────────────────── -->
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
      <div>
        <span class="cart-drawer-title">Your Cart</span>
        <span class="cart-drawer-count" id="cartDrawerCount"></span>
      </div>
      <button class="cart-drawer-close" id="cartDrawerClose" aria-label="Close cart">✕</button>
    </div>
    <div class="cart-drawer-items" id="cartDrawerItems">
      <div class="cart-empty">
        <div class="cart-empty-icon">🛍</div>
        <h3>Your cart is empty</h3>
        <p>Add something you love to get started.</p>
        <a href="/products" class="cart-checkout-btn" style="text-decoration:none;display:inline-block;">Shop Now</a>
      </div>
    </div>
    <div class="cart-drawer-footer">
      <div class="cart-subtotal-row" style="margin-bottom:8px;">
        <span class="cart-subtotal-label">Total Items</span>
        <span class="cart-subtotal-value" id="cartTotalQty">0</span>
      </div>
      <div class="cart-subtotal-row">
        <span class="cart-subtotal-label">Subtotal</span>
        <span class="cart-subtotal-value" id="cartSubtotal"><?= htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8") ?>0.00</span>
      </div>
      <p class="cart-subtotal-note">Shipping &amp; taxes calculated at checkout</p>
      <a href="<?= $baseUrl ?>/checkout" class="cart-checkout-btn" style="text-decoration:none;">Continue to Checkout</a>
      <a href="<?= $baseUrl ?>/cart" class="cart-continue-btn" style="text-decoration:none;text-align:center;display:block;">View Cart</a>
    </div>
  </div>

  <!-- ── Search Overlay ──────────────────────────────────────── -->
  <div class="search-overlay" id="searchOverlay">
    <div class="search-overlay-top">
      <img src="<?= htmlspecialchars($logo_dark, ENT_QUOTES, "UTF-8") ?>"
           alt="<?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?>"
           class="search-overlay-logo">
      <button class="search-close" aria-label="Close search">✕</button>
    </div>
    <div class="search-input-row">
      <input type="text" id="searchInput" class="search-input-field"
             placeholder="Search products...">
      <button class="search-submit-btn" aria-label="Search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </div>
    <div class="search-results-label">Quick Results</div>
    <div class="search-results-grid" id="searchResultsGrid"></div>
  </div>

  <!-- ── Back to top ─────────────────────────────────────────── -->
  <button class="back-to-top" aria-label="Back to top">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M18 15l-6-6-6 6"/>
    </svg>
  </button>

  <!-- ── Toast container ─────────────────────────────────────── -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- Page content starts below -->
   <style>
  a{
    color:inherit;
    text-decoration: none;
  }
</style>