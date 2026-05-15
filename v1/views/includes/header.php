<?php
// ── Fetch nav from panel_pages (ADMC compliant — DB-driven) ──
$navPages = selectContentAsc($conn, "panel_pages", ["visibility" => "show"], "input_order", 15);
$navCategories = selectContentAsc($conn, "selection_product_category", ["visibility" => "show"], "input_name", 10);
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
  <meta property="og:type"        content="website">

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

          <!-- Logo — icon only -->
          <div class="nav-left">
            <a href="<?= $baseUrl ?>/" class="navbar-brand w-nav-brand" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
              <!-- Icon logo (leaf/geometric mark) -->
              <img alt="<?= htmlspecialchars($shop_name, ENT_QUOTES, "UTF-8") ?>"
                   class="nav-logo-icon"
                   loading="lazy"
                   src="https://cdn.prod.website-files.com/69142cc410c97b6153a00e32/6914577f7eba7d2c03a6c183_Group 2087333717.svg"
                   style="height:36px;width:auto;display:block;">
            </a>
          </div>

          <!-- Nav links — DB-driven from panel_pages (ADMC compliant) -->
          <nav class="nav-menu-wrapper w-nav-menu" role="navigation"
               data-admc-tb="panel_pages">
            <ul class="nav-menu-two w-list-unstyled" role="list">
              <?php foreach ($navPages as $navPage):
                $navLink   = $baseUrl . rtrim($navPage['input_link'], '/');
                $navName   = htmlspecialchars($navPage['input_name'], ENT_QUOTES, 'UTF-8');
                $linkPath  = rtrim($navPage['input_link'], '/') ?: '/';
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
                <a href="#" class="cart-button w-inline-block" aria-label="Open cart" data-open-cart>
                  <img alt="Cart" class="cart-icon" src="/assets/img/icons/cart.svg">
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
              <span style="width:22px;height:2px;background:currentColor;border-radius:2px;display:block;"></span>
              <span style="width:22px;height:2px;background:currentColor;border-radius:2px;display:block;"></span>
              <span style="width:22px;height:2px;background:currentColor;border-radius:2px;display:block;"></span>
            </div>
          </div>

        </div>
      </div>
    </nav>
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
      <div class="cart-subtotal-row">
        <span class="cart-subtotal-label">Subtotal</span>
        <span class="cart-subtotal-value" id="cartSubtotal"><?= htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8") ?>0.00</span>
      </div>
      <p class="cart-subtotal-note">Shipping &amp; taxes calculated at checkout</p>
      <a href="/checkout" class="cart-checkout-btn" style="text-decoration:none;">Continue to Checkout</a>
      <button class="cart-continue-btn" id="cartContinueShopping">Continue Shopping</button>
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