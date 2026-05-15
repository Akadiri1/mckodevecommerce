<?php
$page_title = "Products";
$bodyClass  = "page-light-navbar";

// ── Fetch categories ─────────────────────────────────────────
$categories = selectContentAsc($conn, "selection_product_category", ["visibility" => "show"], "input_name", 20);

// ── Active tab from URL ───────────────────────────────────────
$activeTab = isset($_GET["tab"]) ? htmlspecialchars($_GET["tab"], ENT_QUOTES, "UTF-8") : "";

// ── Fetch all products ────────────────────────────────────────
$allProducts = selectContentDesc($conn, "panel_products", ["visibility" => "show"], "id", 100);

// Pre-index all variants once (ADMC: no queries inside loops)
$allVariantsRaw   = selectContent($conn, "addition_product_variants", ["visibility" => "show"]);
$variantsIndexed  = [];
foreach ($allVariantsRaw as $av) {
    $variantsIndexed[$av['tb_link']] = true;
}

// Group products by category
$productsByCategory = ["" => []];
foreach ($allProducts as &$p) {
    $p['has_variants'] = isset($variantsIndexed[$p['hash_id']]) ? "true" : "false";
    $productsByCategory[""][] = $p;
    $cat = $p["select_category"] ?? "";
    if (!isset($productsByCategory[$cat])) $productsByCategory[$cat] = [];
    $productsByCategory[$cat][] = $p;
}
unset($p);

// ── Build tab list ────────────────────────────────────────────
$tabList = [["key" => "", "label" => "All products"]];
foreach ($categories as $cat) {
    $tabList[] = ["key" => $cat["input_name"], "label" => $cat["input_name"]];
}
if (empty($activeTab)) $activeTab = "";

$addToCartIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69767e8def202704be8ff087_Vector (1).svg";

include APP_PATH . "/views/includes/header.php";
?>

<!-- PRODUCTS HERO -->
<section class="products hero-section">
  <div class="container">

    <div class="header">
      <div class="header-left" style="margin-bottom:8px;">
        <h2 class="heading-02">Explore products</h2>
      </div>
    </div>

    <!-- Search + Filter toolbar -->
    <div class="products-toolbar-row" style="margin-top:32px;">

      <!-- Search -->
      <div class="products-search-wrap">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="productSearch" class="products-search-input"
               placeholder="Search products…" autocomplete="off">
        <button class="products-search-clear" id="searchClear" style="display:none;">✕</button>
      </div>

      <!-- Filters -->
      <div class="products-filters-wrap">

        <!-- Sort -->
        <select class="products-filter-select" id="sortSelect">
          <option value="">Sort by</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="name-asc">Name: A–Z</option>
          <option value="name-desc">Name: Z–A</option>
        </select>

        <!-- Price range -->
        <div class="products-price-filter">
          <span class="products-filter-label">Price</span>
          <input type="number" id="priceMin" class="products-price-input" placeholder="Min" min="0">
          <span>–</span>
          <input type="number" id="priceMax" class="products-price-input" placeholder="Max" min="0">
          <button class="products-price-apply" id="priceApply">Apply</button>
        </div>

        <!-- Clear all -->
        <button class="products-filter-clear" id="filterClear" style="display:none;">Clear filters</button>

      </div>
    </div>

    <!-- Results count -->
    <div class="products-results-count" id="resultsCount" style="display:none;"></div>

    <!-- Category tabs -->
    <div class="product-tabs-02 w-tabs venora-product-tabs" data-current="<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>">

      <div class="product-tab-menu-02 w-tab-menu">
        <?php foreach ($tabList as $i => $tab): ?>
          <a class="product-tab-link-02 w-inline-block w-tab-link venora-ptab-trigger<?= $activeTab === $tab['key'] ? ' w--current' : ($i === 0 && $activeTab === '' ? ' w--current' : '') ?>"
             data-tab="<?= htmlspecialchars($tab['key'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="p-02-medium"><?= htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8') ?></div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="w-tab-content">
        <?php foreach ($tabList as $i => $tab):
          $tabProducts = $productsByCategory[$tab['key']] ?? [];
          $isActive = ($activeTab === $tab['key']) || ($i === 0 && $activeTab === '');
        ?>
          <div class="w-tab-pane<?= $isActive ? ' w--tab-active' : '' ?>"
               data-tab-pane="<?= htmlspecialchars($tab['key'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="product-collection">
              <div class="w-dyn-list">
                <?php if (empty($tabProducts)): ?>
                  <div class="w-dyn-empty" style="padding:60px 0;text-align:center;color:#b5b5b5;">
                    <div style="font-size:40px;margin-bottom:12px;">🌿</div>
                    <p class="p-01">No products in this category yet.</p>
                  </div>
                <?php else: ?>
                  <div class="product-grid w-dyn-items" data-admc-tb="panel_products" role="list">
                    <?php foreach ($tabProducts as $product):
                      $imgSrc   = htmlspecialchars($product["image_1"] ?? "", ENT_QUOTES, "UTF-8");
                      $imgHover = htmlspecialchars($product["image_2"] ?? $product["image_1"] ?? "", ENT_QUOTES, "UTF-8");
                      $detailUrl= $baseUrl . "/products/" . $product["hash_id"] . "/" . cleans($product["input_title"]);
                      $sym      = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");
                    ?>
                      <div class="w-dyn-item" role="listitem">
                        <a class="product-link w-inline-block" href="<?= $detailUrl ?>">
                          <div class="product-card">
                            <div class="product-card-img"
                                 data-admc-image="panel_products"
                                 data-admc-id="<?= $product['id'] ?>">
                              <img alt="<?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="all-img" loading="lazy"
                                   src="<?= $imgSrc ?>">
                              <?php if (!empty($product["image_2"]) && $product["image_2"] !== $product["image_1"]): ?>
                                <div class="product-float">
                                  <img alt="" class="all-img" loading="lazy" src="<?= $imgHover ?>">
                                </div>
                              <?php endif; ?>

                              <!-- Wishlist Button -->
                              <?php $inWishlist = in_array($product['hash_id'], $wishlistIds); ?>
                              <button class="wishlist-btn-card <?= $inWishlist ? 'active' : '' ?>" 
                                      data-id="<?= $product['hash_id'] ?>" 
                                      onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.toggleWishlist('<?= $product['hash_id'] ?>', this);"
                                      style="position:absolute; top:12px; left:12px; z-index:15; background:white; border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.1); opacity:0; transition:opacity 0.3s ease;">
                                <img src="<?= $baseUrl ?>/assets/img/icons/<?= $inWishlist ? 'heart-filled.svg' : 'heart-outline.svg' ?>" style="width:18px; height:18px;" alt="Wishlist">
                              </button>

                              <div class="add-to-card-02" 
                                   data-product-id="<?= $product['hash_id'] ?>"
                                   data-has-variants="<?= $product['has_variants'] ?>"
                                   onclick="event.preventDefault(); event.stopPropagation(); if(window.Venora) window.Venora.cartAddItem('<?= $product['hash_id'] ?>', '', 1, null, this);">
                                <img alt="" class="add-to-card-icon" loading="lazy" src="<?= $addToCartIcon ?>">
                                <div class="p-01">Add to cart</div>
                              </div>
                            </div>
                            <div class="product-card-bottom">
                              <div class="color-gray">
                                <div class="p-02 caps"><?= htmlspecialchars($product['select_category'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                              </div>
                              <div class="product-name-price">
                                <div class="heading-06"
                                     data-admc-manage="panel_products"
                                     data-admc-id="<?= $product['id'] ?>">
                                  <?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="heading-07"
                                     data-admc-manage="panel_products"
                                     data-admc-id="<?= $product['id'] ?>">
                                  <?php if (!empty($product['input_compare_price'])): ?>
                                    <span style="text-decoration:line-through;color:#b5b5b5;font-size:13px;margin-right:6px;">
                                      <?= $sym ?><?= number_format((float)$product['input_compare_price'], 2) ?>
                                    </span>
                                  <?php endif; ?>
                                  <?= $sym ?><?= number_format((float)$product['input_price'], 2) ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <!-- Load More button (only shown when there are more than 6 products) -->
                  <?php if (count($tabProducts) > 6): ?>
                    <div class="load-more-wrap">
                      <button class="load-more-btn" data-pane="<?= htmlspecialchars($tab['key'], ENT_QUOTES, 'UTF-8') ?>">
                        Load More
                      </button>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>

<script>
(function() {
  var PAGE_SIZE = 6;
  var activeFilters = { search: '', priceMin: 0, priceMax: Infinity, sort: '' };

  // ── Category tabs ────────────────────────────────────────────
  document.querySelectorAll('.venora-ptab-trigger').forEach(function(tab) {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.venora-ptab-trigger').forEach(function(t) { t.classList.remove('w--current'); });
      document.querySelectorAll('[data-tab-pane]').forEach(function(p) { p.classList.remove('w--tab-active'); });
      tab.classList.add('w--current');
      var pane = document.querySelector('[data-tab-pane="' + tab.dataset.tab + '"]');
      if (pane) { pane.classList.add('w--tab-active'); applyFilters(); }
    });
  });

  // ── Core filter engine ───────────────────────────────────────
  function getActivePane() {
    return document.querySelector('.w-tab-pane.w--tab-active');
  }

  function applyFilters() {
    var pane = getActivePane();
    if (!pane) return;
    var q = activeFilters.search.toLowerCase().trim();
    var minP = activeFilters.priceMin;
    var maxP = activeFilters.priceMax;
    var items = pane.querySelectorAll('.w-dyn-item');
    var visible = 0;

    // Sort
    if (activeFilters.sort) {
      var grid = pane.querySelector('.product-grid');
      if (grid) {
        var arr = Array.from(items);
        arr.sort(function(a, b) {
          if (activeFilters.sort === 'price-asc' || activeFilters.sort === 'price-desc') {
            var pa = parseFloat(a.querySelector('.heading-07') ? a.querySelector('.heading-07').textContent.replace(/[^0-9.]/g,'') : 0);
            var pb = parseFloat(b.querySelector('.heading-07') ? b.querySelector('.heading-07').textContent.replace(/[^0-9.]/g,'') : 0);
            return activeFilters.sort === 'price-asc' ? pa - pb : pb - pa;
          }
          if (activeFilters.sort === 'name-asc' || activeFilters.sort === 'name-desc') {
            var na = (a.querySelector('.heading-06') || {}).textContent || '';
            var nb = (b.querySelector('.heading-06') || {}).textContent || '';
            return activeFilters.sort === 'name-asc' ? na.localeCompare(nb) : nb.localeCompare(na);
          }
          return 0;
        });
        arr.forEach(function(el) { grid.appendChild(el); });
      }
    }

    // Show/hide based on search + price
    var shown = 0;
    pane.querySelectorAll('.w-dyn-item').forEach(function(item) {
      var title = (item.querySelector('.heading-06') || {}).textContent || '';
      var priceEl = item.querySelector('.heading-07');
      var price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.]/g,'')) : 0;
      var matchQ = !q || title.toLowerCase().includes(q);
      var matchP = price >= minP && price <= maxP;
      var show = matchQ && matchP;
      item.style.display = show ? '' : 'none';
      if (show) { shown++; if (shown > PAGE_SIZE) item.style.display = 'none'; }
    });

    // Update load more button
    updateLoadMore(pane, shown);

    // Results count
    var total = Array.from(pane.querySelectorAll('.w-dyn-item')).filter(function(i) {
      var title = (i.querySelector('.heading-06') || {}).textContent || '';
      var priceEl = i.querySelector('.heading-07');
      var price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.]/g,'')) : 0;
      return (!q || title.toLowerCase().includes(q)) && price >= minP && price <= maxP;
    }).length;
    var countEl = document.getElementById('resultsCount');
    if (q || minP > 0 || maxP < Infinity) {
      countEl.style.display = 'block';
      countEl.textContent = total + ' product' + (total !== 1 ? 's' : '') + ' found';
    } else {
      countEl.style.display = 'none';
    }

    // Clear button visibility
    var hasFilter = q || minP > 0 || maxP < Infinity || activeFilters.sort;
    document.getElementById('filterClear').style.display = hasFilter ? 'inline-flex' : 'none';
  }

  function updateLoadMore(pane, initialShown) {
    var btn = pane.querySelector('.load-more-btn');
    if (!btn) return;
    var allMatch = Array.from(pane.querySelectorAll('.w-dyn-item')).filter(function(i) {
      return i.style.display !== 'none' || !i.dataset.hidden;
    });
    btn.disabled = false;
    btn.textContent = 'Load More';
    btn.style.display = initialShown > PAGE_SIZE ? 'inline-flex' : 'none';
  }

  // ── Initial load more setup ──────────────────────────────────
  document.querySelectorAll('.w-tab-pane').forEach(function(pane) {
    var items = pane.querySelectorAll('.w-dyn-item');
    items.forEach(function(item, i) {
      if (i >= PAGE_SIZE) item.style.display = 'none';
    });
    var btn = pane.querySelector('.load-more-btn');
    if (btn) btn.style.display = items.length > PAGE_SIZE ? 'inline-flex' : 'none';
  });

  // ── Load More click ──────────────────────────────────────────
  document.querySelectorAll('.load-more-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var pane = btn.closest('[data-tab-pane]');
      if (!pane) return;
      var q = activeFilters.search.toLowerCase().trim();
      var minP = activeFilters.priceMin;
      var maxP = activeFilters.priceMax;
      var hidden = Array.from(pane.querySelectorAll('.w-dyn-item')).filter(function(item) {
        var title = (item.querySelector('.heading-06') || {}).textContent || '';
        var priceEl = item.querySelector('.heading-07');
        var price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.]/g,'')) : 0;
        return item.style.display === 'none'
          && (!q || title.toLowerCase().includes(q))
          && price >= minP && price <= maxP;
      });
      hidden.slice(0, PAGE_SIZE).forEach(function(item) { item.style.display = ''; });
      if (hidden.length <= PAGE_SIZE) {
        btn.disabled = true;
        btn.textContent = 'All products shown';
      }
    });
  });

  // ── Search input ─────────────────────────────────────────────
  var searchInput = document.getElementById('productSearch');
  var searchClear = document.getElementById('searchClear');
  var searchTimer;
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimer);
      activeFilters.search = searchInput.value;
      searchClear.style.display = searchInput.value ? 'inline-flex' : 'none';
      searchTimer = setTimeout(applyFilters, 200);
    });
  }
  if (searchClear) {
    searchClear.addEventListener('click', function() {
      searchInput.value = '';
      activeFilters.search = '';
      searchClear.style.display = 'none';
      applyFilters();
    });
  }

  // ── Sort ─────────────────────────────────────────────────────
  var sortSel = document.getElementById('sortSelect');
  if (sortSel) {
    sortSel.addEventListener('change', function() {
      activeFilters.sort = sortSel.value;
      applyFilters();
    });
  }

  // ── Price filter ─────────────────────────────────────────────
  document.getElementById('priceApply').addEventListener('click', function() {
    var mn = parseFloat(document.getElementById('priceMin').value);
    var mx = parseFloat(document.getElementById('priceMax').value);
    activeFilters.priceMin = isNaN(mn) ? 0 : mn;
    activeFilters.priceMax = isNaN(mx) ? Infinity : mx;
    applyFilters();
  });

  // ── Clear all filters ────────────────────────────────────────
  document.getElementById('filterClear').addEventListener('click', function() {
    activeFilters = { search: '', priceMin: 0, priceMax: Infinity, sort: '' };
    if (searchInput) { searchInput.value = ''; searchClear.style.display = 'none'; }
    if (sortSel) sortSel.value = '';
    document.getElementById('priceMin').value = '';
    document.getElementById('priceMax').value = '';
    applyFilters();
  });
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
