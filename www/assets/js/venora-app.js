/**
 * Venora E-Commerce — App JavaScript
 * Handles: cart drawer, quick-view modal, search, wishlist,
 * toast notifications, filters, sticky bar, back-to-top,
 * newsletter popup, product tabs, image gallery, scroll reveal
 */

(function() {
  'use strict';

  // ── Helpers ──────────────────────────────────────────────────
  function $$(sel, ctx) { return (ctx || document).querySelectorAll(sel); }
  function $(sel, ctx)  { return (ctx || document).querySelector(sel); }
  function on(el, ev, fn, opts) { if (el) el.addEventListener(ev, fn, opts); }
  function off(el, ev, fn)      { if (el) el.removeEventListener(ev, fn); }

  function formatPrice(amount, symbol) {
    symbol = symbol || window.VENORA_CURRENCY_SYMBOL || '$';
    return symbol + parseFloat(amount).toFixed(2);
  }

  // ── Toast notifications ──────────────────────────────────────
  var toastContainer = null;
  function showToast(msg, type, duration) {
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container';
      document.body.appendChild(toastContainer);
    }
    var t = document.createElement('div');
    t.className = 'toast' + (type ? ' ' + type : '');
    t.innerHTML = '<span class="toast-icon">' + (type === 'error' ? '✕' : '✓') + '</span>' + msg;
    toastContainer.appendChild(t);
    setTimeout(function() { t.classList.add('show'); }, 20);
    setTimeout(function() {
      t.classList.remove('show');
      setTimeout(function() { t.remove(); }, 400);
    }, duration || 3000);
  }

  // ── Overlay ──────────────────────────────────────────────────
  var overlay = null;
  function getOverlay() {
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'v-overlay';
      document.body.appendChild(overlay);
      on(overlay, 'click', closeAll);
    }
    return overlay;
  }
  function showOverlay() { getOverlay().classList.add('active'); document.body.style.overflow = 'hidden'; }
  function hideOverlay() { getOverlay().classList.remove('active'); document.body.style.overflow = ''; }

  function closeAll() {
    closeCartDrawer();
    closeQuickView();
    closeSearch();
    hideOverlay();
  }

  // ── Cart Drawer ──────────────────────────────────────────────
  var cartDrawer = null;
  function openCartDrawer() {
    cartDrawer = cartDrawer || $('.cart-drawer');
    if (cartDrawer) { cartDrawer.classList.add('active'); showOverlay(); refreshCartDrawer(); }
  }
  function closeCartDrawer() {
    if (cartDrawer) cartDrawer.classList.remove('active');
  }

  function updateCartBadge(count) {
    var badges = $$('.cart-count-badge');
    badges.forEach(function(b) {
      b.textContent = count;
      b.classList.toggle('has-items', count > 0);
    });
  }

  var baseUrl = window.VENORA_BASE_URL || '';

  function refreshCartDrawer() {
    var itemsEl  = $('.cart-drawer-items');
    var countEl  = $('.cart-drawer-count');
    var subEl    = $('.cart-subtotal-value');
    if (!itemsEl) return;
    itemsEl.innerHTML = '<div style="text-align:center;padding:32px;color:#b5b5b5;">Loading...</div>';
    fetch(baseUrl + '/cart-get', { method:'GET', headers:{'X-Requested-With':'XMLHttpRequest'} })
      .then(function(r) { 
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json(); 
      })
      .then(function(data) {
        if (countEl) countEl.textContent = '(' + data.count + ' item' + (data.count !== 1 ? 's' : '') + ')';
        updateCartBadge(data.count);
        if (!data.items || data.items.length === 0) {
          itemsEl.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛍</div><h3>Your cart is empty</h3><p>Add something you love to get started.</p><a href="/products" class="cart-checkout-btn" style="text-decoration:none;display:inline-block;margin:0 auto;">Shop Now</a></div>';
          if (subEl) subEl.textContent = formatPrice(0);
          return;
        }
        var html = '';
        data.items.forEach(function(item) {
          html += '<div class="cart-item" data-cart-id="' + item.id + '">' +
            '<img src="' + (item.image || '/assets/img/icons/cart.svg') + '" class="cart-item-img" alt="' + item.name + '">' +
            '<div class="cart-item-info">' +
              '<h4>' + item.name + '</h4>' +
              (item.variant ? '<div class="cart-item-variant">' + item.variant + '</div>' : '') +
              '<div class="cart-item-price">' + formatPrice(item.price * item.qty) + '</div>' +
              '<div class="qty-control" style="margin-top:8px;">' +
                '<button class="qty-btn" data-action="decrease" data-id="' + item.id + '">−</button>' +
                '<input class="qty-input" type="number" value="' + item.qty + '" min="1" data-id="' + item.id + '">' +
                '<button class="qty-btn" data-action="increase" data-id="' + item.id + '">+</button>' +
              '</div>' +
            '</div>' +
            '<div class="cart-item-actions">' +
              '<button class="cart-item-remove" data-id="' + item.id + '">Remove</button>' +
            '</div>' +
          '</div>';
        });
        itemsEl.innerHTML = html;
        if (subEl) subEl.textContent = formatPrice(data.subtotal);
        // Bind qty + remove
        $$('.cart-item-remove', itemsEl).forEach(function(btn) {
          on(btn, 'click', function() { cartRemoveItem(btn.dataset.id); });
        });
        $$('.qty-btn', itemsEl).forEach(function(btn) {
          on(btn, 'click', function() {
            var input = itemsEl.querySelector('.qty-input[data-id="' + btn.dataset.id + '"]');
            var qty = parseInt(input.value) + (btn.dataset.action === 'increase' ? 1 : -1);
            if (qty < 1) { cartRemoveItem(btn.dataset.id); return; }
            input.value = qty;
            cartUpdateItem(btn.dataset.id, qty);
          });
        });
      })
      .catch(function() {
        itemsEl.innerHTML = '<div class="cart-empty"><p style="color:#c1121f;">Could not load cart. Please refresh.</p></div>';
      });
  }

  function cartAddItem(productId, variantId, qty, onSuccess, clickedBtn) {
    // If from grid and has variants -> open modal instead
    if (clickedBtn && clickedBtn.dataset.hasVariants === 'true') {
      openQuickView(productId);
      return;
    }

    fetch(baseUrl + '/cart-add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ product_id: productId, variant_id: variantId || '', quantity: qty || 1 })
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
      if (data.success) {
        updateCartBadge(data.cart_count);
        showToast('Added to cart!');
        openCartDrawer();
        if (onSuccess) onSuccess(data);
      } else {
        showToast(data.error || 'Could not add to cart', 'error');
      }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); });
  }

  function cartRemoveItem(cartId) {
    fetch(baseUrl + '/cart-remove', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ cart_id: cartId })
    })
    .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
    .then(function(data) {
      if (data.success) { updateCartBadge(data.cart_count); refreshCartDrawer(); showToast('Item removed'); }
    });
  }

  function cartUpdateItem(cartId, qty) {
    fetch(baseUrl + '/cart-update', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ cart_id: cartId, quantity: qty })
    })
    .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
    .then(function(data) { if (data.success) { updateCartBadge(data.cart_count); refreshCartDrawer(); } });
  }

  // ── Quick View Modal ─────────────────────────────────────────
  var qvModal = null;
  var currentGallery = [];
  var currentGalleryIdx = 0;

  function openQuickView(productId) {
    if (!qvModal) {
      qvModal = document.createElement('div');
      qvModal.className = 'quick-view-modal';
      qvModal.innerHTML =
        '<div class="modal-handle"><div class="modal-handle-bar"></div></div>' +
        '<button class="modal-close" id="qvClose">✕</button>' +
        '<div class="quick-view-modal-inner" id="qvContent">' +
          '<div style="text-align:center;padding:48px;">Loading...</div>' +
        '</div>';
      document.body.appendChild(qvModal);
      on($('#qvClose', qvModal), 'click', function() { closeQuickView(); hideOverlay(); });
      // Swipe to close
      var startY = 0;
      on(qvModal, 'touchstart', function(e) { startY = e.touches[0].clientY; }, { passive: true });
      on(qvModal, 'touchend', function(e) {
        if (e.changedTouches[0].clientY - startY > 80) { closeQuickView(); hideOverlay(); }
      });
    }
    qvModal.classList.add('active');
    showOverlay();
    var content = $('#qvContent', qvModal);
    content.innerHTML = '<div style="text-align:center;padding:48px;grid-column:span 2;">Loading...</div>';

    fetch(baseUrl + '/quick-view?id=' + productId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { 
        if (!r.ok) return r.json().then(function(err){ throw new Error(err.error || 'Server error'); });
        return r.json(); 
      })
      .then(function(p) { renderQuickView(p); })
      .catch(function(err) {
        content.innerHTML = '<div style="color:#c1121f;padding:24px;grid-column:span 2;text-align:center;">' +
          '<div style="font-size:40px;margin-bottom:16px;">⚠️</div>' +
          '<strong>Could not load product.</strong><br>' +
          '<small style="opacity:0.7;">' + err.message + '</small>' +
          '<button onclick="location.reload()" style="display:block;margin:16px auto;padding:8px 16px;background:#eee;border:none;border-radius:4px;cursor:pointer;">Refresh Page</button>' +
        '</div>';
      });
  }

  function renderQuickView(p) {
    var content = $('#qvContent', qvModal);
    currentGallery = p.images || [p.image_1];
    currentGalleryIdx = 0;

    var thumbsHtml = '';
    currentGallery.forEach(function(img, i) {
      thumbsHtml += '<img src="' + img + '" class="modal-thumb' + (i === 0 ? ' active' : '') + '" data-idx="' + i + '" alt="">';
    });

    var sym = window.VENORA_CURRENCY_SYMBOL || '$';
    var mainPriceRaw = parseFloat(p.input_price.toString().replace(/[^0-9.]/g, '')) || 0;

    var variantsHtml = '';
    if (p.variants && p.variants.length) {
      var categories = {};
      p.variants.forEach(function(v) {
        var cat = v.input_name || 'Options';
        if (!categories[cat]) categories[cat] = [];
        categories[cat].push(v);
      });

      Object.keys(categories).forEach(function(catName) {
        variantsHtml += '<div class="modal-variants" style="margin-bottom:20px;">' +
          '<div class="modal-variants-label" style="display:block; margin-bottom:10px; font-weight:700; color:var(--v-black); text-transform:uppercase; font-size:11px; letter-spacing:1px;">' + catName + ':</div>' +
          '<div class="modal-variant-options" data-category="' + catName + '" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">' +
          categories[catName].map(function(v) {
            var vPriceRaw = parseFloat(v.input_price ? v.input_price.toString().replace(/[^0-9.]/g, '') : mainPriceRaw);
            return '<button class="modal-variant-btn' + (v.stock === 0 ? ' out-of-stock' : '') + '" ' +
                   'data-variant-id="' + v.id + '" data-price="' + vPriceRaw + '" data-value="' + v.input_value + '">' + 
                   v.input_value + 
                   '</button>';
          }).join('') +
          '</div></div>';
      });
    }

    var compareHtml = p.input_compare_price ? '<span class="modal-price-compare">' + sym + p.input_compare_price + '</span>' : '';
    var starsHtml = '';
    var rating = parseFloat(p.input_rating || 4.5);
    for (var s = 1; s <= 5; s++) {
      starsHtml += '<img src="' + baseUrl + '/assets/img/icons/star.svg" class="star" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:2px;' + (s > Math.round(rating) ? 'opacity:0.3;' : '') + '" alt="">';
    }

    var reviewsHtml = '';
    if (p.reviews_list && p.reviews_list.length) {
      reviewsHtml = '<div class="modal-reviews-section">' +
        '<div class="modal-variants-label" style="margin-top:24px;">Recent Reviews</div>' +
        '<div class="modal-reviews-list" style="max-height:150px; overflow-y:auto; padding-right:8px;">' +
        p.reviews_list.map(function(rev) {
          return '<div class="modal-review-item" style="margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #eee;">' +
            '<div style="font-weight:600; font-size:13px; color:var(--v-black);">' + rev.name + '</div>' +
            '<div style="font-size:12px; color:var(--v-gray); line-height:1.4;">' + rev.text + '</div>' +
            '</div>';
        }).join('') +
        '</div></div>';
    }

    content.innerHTML =
      '<div class="modal-gallery">' +
        '<img src="' + (currentGallery[0] || '') + '" class="modal-main-img" id="qvMainImg" alt="' + p.input_title + '">' +
        (currentGallery.length > 1 ? '<div class="modal-thumbs">' + thumbsHtml + '</div>' : '') +
      '</div>' +
      '<div class="modal-details">' +
        '<div class="modal-category">' + (p.select_category || '') + '</div>' +
        '<h2 class="modal-title">' + p.input_title + '</h2>' +
        '<div class="modal-rating">' +
          '<div class="modal-stars">' + starsHtml + '</div>' +
          '<span class="modal-rating-text">(' + (p.input_reviews_count || 0) + ' Reviews)</span>' +
        '</div>' +
        '<div class="modal-price">' +
          '<span class="modal-price-current" id="qvPrice">' + sym + p.input_price + '</span>' + compareHtml +
        '</div>' +
        '<p class="modal-short-desc">' + previewBody(p.text_description || '', 30) + '</p>' +
        variantsHtml +
        reviewsHtml +
        '<div class="modal-qty-row" style="margin-top:20px;">' +
          '<span class="modal-qty-label">Qty</span>' +
          '<div class="qty-control">' +
            '<button class="qty-btn" id="qvQtyMinus">−</button>' +
            '<input class="qty-input" id="qvQtyInput" type="number" value="1" min="1">' +
            '<button class="qty-btn" id="qvQtyPlus">+</button>' +
          '</div>' +
        '</div>' +
        '<div class="modal-actions">' +
          '<button class="modal-add-to-cart" id="qvAddToCart" data-product-id="' + p.hash_id + '">' +
            '<img src="' + window.VENORA_BASE_URL + '/assets/img/icons/cart-white.svg" style="width:18px;height:18px;" alt=""> Add to Cart' +
          '</button>' +
          '<button class="modal-wishlist-btn" id="qvWishlist" data-id="' + p.hash_id + '">' +
            '<img src="' + window.VENORA_BASE_URL + '/assets/img/icons/heart-outline.svg" alt="Wishlist" id="qvWishlistImg">' +
          '</button>' +
        '</div>' +
        '<a href="' + baseUrl + '/products/' + p.hash_id + '/' + p.input_slug + '" class="modal-view-full">View full details →</a>' +
      '</div>';

    // Bind thumbs
    $$('.modal-thumb', qvModal).forEach(function(thumb) {
      on(thumb, 'click', function() {
        currentGalleryIdx = parseInt(thumb.dataset.idx);
        $('#qvMainImg', qvModal).src = currentGallery[currentGalleryIdx];
        $$('.modal-thumb', qvModal).forEach(function(t) { t.classList.remove('active'); });
        thumb.classList.add('active');
      });
    });

    // Variant select
    $$('.modal-variant-btn', qvModal).forEach(function(btn) {
      on(btn, 'click', function() {
        if (btn.classList.contains('out-of-stock')) return;
        var parent = btn.closest('.modal-variant-options');
        var wasActive = btn.classList.contains('active');

        // Unpick others in same group
        $$('.modal-variant-btn', parent).forEach(function(b) { b.classList.remove('active'); });

        if (!wasActive) {
          btn.classList.add('active');
          var vPrice = parseFloat(btn.dataset.price);
          if (!isNaN(vPrice)) $('#qvPrice', qvModal).textContent = sym + vPrice.toFixed(2);
        } else {
          // It was active and clicked again -> unpick
          btn.classList.remove('active');
          $('#qvPrice', qvModal).textContent = sym + mainPriceRaw.toFixed(2);
        }
      });
    });

    // Qty
    on($('#qvQtyMinus', qvModal), 'click', function() {
      var input = $('#qvQtyInput', qvModal);
      if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    });
    on($('#qvQtyPlus', qvModal), 'click', function() {
      var input = $('#qvQtyInput', qvModal);
      input.value = parseInt(input.value) + 1;
    });

    // Add to cart
    on($('#qvAddToCart', qvModal), 'click', function() {
      var btn = this;
      
      // Validation: Must select one from EACH category
      var totalGroups = $$('.modal-variant-options', qvModal).length;
      var activeVariants = $$('.modal-variant-btn.active', qvModal);
      
      if (activeVariants.length < totalGroups) {
          showToast('Please select all options (Size, Color, etc.)', 'error');
          return;
      }

      var variantIds = [];
      activeVariants.forEach(function(v) { variantIds.push(v.dataset.variantId); });
      var variantId = variantIds.join(',');
      var qty = parseInt($('#qvQtyInput', qvModal).value) || 1;
      
      btn.classList.add('loading');
      btn.textContent = 'Adding...';
      cartAddItem(p.hash_id, variantId, qty, function() {
        btn.classList.remove('loading');
        btn.innerHTML = '<img src="' + window.VENORA_BASE_URL + '/assets/img/icons/cart-white.svg" style="width:18px;height:18px;" alt=""> Add to Cart';
        closeQuickView();
        hideOverlay();
      });
    });

    // Wishlist
    on($('#qvWishlist', qvModal), 'click', function() { toggleWishlist(p.hash_id, this); });
  }

  function closeQuickView() {
    if (qvModal) qvModal.classList.remove('active');
  }

  function previewBody(str, words) {
    var arr = str.replace(/<[^>]+>/g, '').split(' ');
    return arr.length > words ? arr.slice(0, words).join(' ') + '...' : str;
  }

  // ── Search overlay ───────────────────────────────────────────
  var searchOverlay = $('.search-overlay');
  function openSearch() {
    if (searchOverlay) {
      searchOverlay.classList.add('active');
      showOverlay();
      var inp = $('#searchInput');
      if (inp) setTimeout(function() { inp.focus(); }, 300);
    }
  }
  function closeSearch() { if (searchOverlay) searchOverlay.classList.remove('active'); }

  var searchTimeout;
  var searchGrid = document.getElementById('searchResultsGrid');

  on($('#searchInput'), 'input', function() {
    clearTimeout(searchTimeout);
    var q = this.value.trim();
    if (!q.length) {
      if (searchGrid) searchGrid.innerHTML = '';
      return;
    }
    searchTimeout = setTimeout(function() { liveSearch(q); }, 200);
  });

  function liveSearch(q) {
    if (!searchGrid) return;
    var bUrl = window.VENORA_BASE_URL || '';
    searchGrid.innerHTML = '<p style="color:#888;font-size:13px;padding:8px 0;">Searching...</p>';

    fetch(bUrl + '/search?q=' + encodeURIComponent(q) + '&limit=8', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text(); // read as text first to catch HTML errors
      })
      .then(function(text) {
        var data;
        try { data = JSON.parse(text); } catch(e) {
          searchGrid.innerHTML = '<p style="color:#888;font-size:14px;padding:8px 0;">No results found.</p>';
          return;
        }
        if (!data.products || !data.products.length) {
          searchGrid.innerHTML = '<p style="color:#888;font-size:14px;padding:8px 0;">No results for &ldquo;' + q + '&rdquo;</p>';
          return;
        }
        var sym = window.VENORA_CURRENCY_SYMBOL || '$';
        searchGrid.innerHTML = data.products.map(function(p) {
          var url = bUrl + '/products/' + p.hash_id + '/' + (p.input_slug || '');
          var price = sym + parseFloat(p.input_price || 0).toFixed(2);
          return '<div class="search-result-item" onclick="window.location=\'' + url + '\'" role="button" tabindex="0">' +
            '<img src="' + (p.image_1 || '') + '" class="search-result-img" alt="' + p.input_title + '" onerror="this.style.display=\'none\'">' +
            '<div class="search-result-info">' +
              '<div class="search-result-name">' + p.input_title + '</div>' +
              '<div class="search-result-price">' + price + '</div>' +
            '</div>' +
          '</div>';
        }).join('');
      })
      .catch(function() {
        searchGrid.innerHTML = '<p style="color:#888;font-size:14px;padding:8px 0;">No results found.</p>';
      });
  }

  function updateWishlistBadge(count) {
    var badges = $$('#wishlistBadge');
    badges.forEach(function(b) {
      b.textContent = count;
      b.classList.toggle('has-items', count > 0);
    });
  }

  // ── Wishlist (session-based) ─────────────────────────────────
  function toggleWishlist(productId, btn) {
    return fetch(baseUrl + '/wishlist-toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ product_id: productId })
    })
    .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
    .then(function(data) {
      // Not logged in — redirect to login
      if (data.auth === false) {
        showToast('Sign in to save items to your wishlist', 'info');
        setTimeout(function() {
          window.location.href = (window.VENORA_BASE_URL || '') + '/customer-login';
        }, 1200);
        return data;
      }
      var isAdded = data.added;
      showToast(isAdded ? 'Added to wishlist!' : 'Removed from wishlist');
      updateWishlistBadge(data.count);
      if (btn) {
        btn.classList.toggle('active', isAdded);
        var img = btn.querySelector('img');
        if (img) img.src = window.VENORA_BASE_URL + (isAdded ? '/assets/img/icons/heart-filled.svg' : '/assets/img/icons/heart-outline.svg');
      }
      return data;
    });
  }

  // ── Product tabs (detail page) ───────────────────────────────
  $$('.product-tab-link').forEach(function(tab) {
    on(tab, 'click', function() {
      var target = tab.dataset.tab;
      $$('.product-tab-link').forEach(function(t) { t.classList.remove('w--current'); });
      $$('.w-tab-pane').forEach(function(p) { p.classList.remove('w--tab-active'); });
      tab.classList.add('w--current');
      var pane = $('[data-tab-pane="' + target + '"]');
      if (pane) pane.classList.add('w--tab-active');
    });
  });

  // ── Image gallery (product detail) ──────────────────────────
  var galleryMainImg = $('.product-gallery-main img');
  $$('.product-gallery-thumb').forEach(function(thumb) {
    on(thumb, 'click', function() {
      if (galleryMainImg) galleryMainImg.src = thumb.dataset.src;
      $$('.product-gallery-thumb').forEach(function(t) { t.classList.remove('active'); });
      thumb.classList.add('active');
    });
  });

  // ── Sticky add-to-cart bar ───────────────────────────────────
  var stickyBar = $('.sticky-cart-bar');
  var addToCartSection = $('.add-to-cart-section');
  if (stickyBar && addToCartSection) {
    var stickyObserver = new IntersectionObserver(function(entries) {
      stickyBar.classList.toggle('visible', !entries[0].isIntersecting);
    }, { threshold: 0 });
    stickyObserver.observe(addToCartSection);
    on($('.sticky-cart-btn', stickyBar), 'click', function() {
      var mainBtn = $('.btn-add-to-cart-main');
      if (mainBtn) mainBtn.click();
    });
  }

  // ── Back to top ──────────────────────────────────────────────
  var btt = $('.back-to-top');
  if (btt) {
    window.addEventListener('scroll', function() { btt.classList.toggle('visible', window.scrollY > 400); });
    on(btt, 'click', function() { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  // ── Newsletter popup ─────────────────────────────────────────
  var nlPopup = $('.newsletter-popup');
  if (nlPopup && !sessionStorage.getItem('nl_dismissed')) {
    setTimeout(function() { nlPopup.classList.add('show'); }, 8000);

    function dismissNlPopup() {
      nlPopup.classList.remove('show');
      sessionStorage.setItem('nl_dismissed', '1');
    }

    /* Close (✕) and "No thanks" both dismiss */
    on($('.newsletter-popup-close', nlPopup), 'click', dismissNlPopup);
    on($('.newsletter-popup-dismiss', nlPopup), 'click', dismissNlPopup);

    /* Click outside popup closes it */
    on(document, 'click', function(e) {
      if (nlPopup.classList.contains('show') && !nlPopup.contains(e.target)) {
        dismissNlPopup();
      }
    });

    var nlForm = $('.newsletter-popup-form', nlPopup);
    if (nlForm) {
      on(nlForm, 'submit', function(e) {
        e.preventDefault();
        var emailVal = nlForm.querySelector('input[type=email]').value.trim();
        if (!emailVal) return;
        var btn = nlForm.querySelector('button[type=submit]');
        if (btn) { btn.disabled = true; btn.textContent = '...'; }
        fetch(baseUrl + '/newsletter-subscribe', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'email=' + encodeURIComponent(emailVal)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          var msg = nlPopup.querySelector('p');
          if (msg) msg.textContent = res.success
            ? 'Thank you for subscribing!'
            : 'Something went wrong. Please try again.';
          nlForm.style.display = 'none';
          if (res.success) setTimeout(dismissNlPopup, 2500);
          else if (btn) { btn.disabled = false; btn.textContent = 'Subscribe'; }
        })
        .catch(function() {
          if (btn) { btn.disabled = false; btn.textContent = 'Subscribe'; }
        });
      });
    }
  }

  // ── Scroll reveal (.reveal class — legacy) ───────────────────
  var revealObs = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) { entry.target.classList.add('visible'); revealObs.unobserve(entry.target); }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  $$('.reveal').forEach(function(el) { revealObs.observe(el); });

  // ── GSAP + ScrollTrigger — Webflow IX2 replacement ───────────
  // Replicates the exact venora template animations using GSAP,
  // the same engine Webflow IX2 uses internally.
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);

    // Collect all elements with Webflow initial opacity:0 inline style
    var wfEls = [];
    document.querySelectorAll('[data-w-id]').forEach(function(el) {
      var s = el.getAttribute('style') || '';
      if (s.indexOf('opacity:0') !== -1 || s.indexOf('opacity: 0') !== -1) {
        // Clear verbose Webflow multi-vendor inline transforms
        el.style.removeProperty('-webkit-transform');
        el.style.removeProperty('-moz-transform');
        el.style.removeProperty('-ms-transform');
        gsap.set(el, { opacity: 0, y: 36, clearProps: 'none' });
        wfEls.push(el);
      }
    });

    // Hero elements — animate immediately with stagger (no scroll trigger)
    var heroEls = [];
    document.querySelectorAll('.home-hero [data-w-id], .about-hero [data-w-id]').forEach(function(el) {
      if (wfEls.indexOf(el) !== -1) heroEls.push(el);
    });
    if (heroEls.length) {
      gsap.to(heroEls, {
        opacity: 1, y: 0,
        duration: 0.85,
        ease: 'power2.out',
        stagger: 0.14,
        delay: 0.1
      });
    }

    // Scroll-triggered fade-up for all other animated elements
    wfEls.forEach(function(el) {
      if (el.closest('.home-hero') || el.closest('.about-hero')) return;
      gsap.to(el, {
        opacity: 1, y: 0,
        duration: 0.75,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 88%',
          once: true
        }
      });
    });

    // Images with .speed class — subtle parallax (like the template)
    document.querySelectorAll('.images.speed').forEach(function(img) {
      gsap.to(img, {
        yPercent: -8,
        ease: 'none',
        scrollTrigger: {
          trigger: img.closest('.content-img-box') || img.parentElement,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5
        }
      });
    });

    // Images with .zoom class — scale reveal
    document.querySelectorAll('.all-img.zoom').forEach(function(img) {
      gsap.fromTo(img,
        { scale: 1.08 },
        {
          scale: 1,
          duration: 0.9,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: img.closest('.product-img-box, .features-card, .content-img-box') || img.parentElement,
            start: 'top 85%',
            once: true
          }
        }
      );
    });

  } else {
    // GSAP not loaded — fallback to simple IntersectionObserver
    document.querySelectorAll('[data-w-id]').forEach(function(el) {
      var s = el.getAttribute('style') || '';
      if (s.indexOf('opacity:0') === -1 && s.indexOf('opacity: 0') === -1) return;
      el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
      el.style.opacity = '0';
      el.style.transform = 'translateY(32px)';
      new IntersectionObserver(function(entries, obs) {
        if (entries[0].isIntersecting) {
          requestAnimationFrame(function() { el.style.opacity='1'; el.style.transform='translateY(0)'; });
          obs.disconnect();
        }
      }, { threshold: 0.12 }).observe(el);
    });
  }

  // ── Event delegation ─────────────────────────────────────────
  document.addEventListener('click', function(e) {
    // 1. Quick view open
    var qvBtn = e.target.closest('.quick-view-btn');
    if (qvBtn) {
      e.preventDefault(); e.stopPropagation();
      openQuickView(qvBtn.dataset.id);
      return;
    }

    // 2. Product card add to cart
    var addBtn = e.target.closest('.add-to-card-02');
    if (addBtn) {
      e.preventDefault(); e.stopPropagation();
      if (addBtn.classList.contains('is-adding')) return;
      var pid = addBtn.dataset.productId;
      if (pid) {
        addBtn.classList.add('is-adding');
        cartAddItem(pid, '', 1, function() { addBtn.classList.remove('is-adding'); });
      }
      return;
    }

    // 3. UI Toggles
    if (e.target.closest('[data-open-cart]')) { e.preventDefault(); openCartDrawer(); }
    if (e.target.closest('#cartDrawerClose')) { closeCartDrawer(); hideOverlay(); }
    if (e.target.closest('[data-open-search]')) { e.preventDefault(); openSearch(); }
    if (e.target.closest('.search-close')) { closeSearch(); hideOverlay(); }

    // 4. Main add-to-cart (detail page)
    var mainATC = e.target.closest('.btn-add-to-cart-main');
    if (mainATC) {
      e.preventDefault(); e.stopPropagation();
      var pid2 = mainATC.dataset.productId;
      // Validate variant selection before adding
      var vid = typeof getSelectedVariantIds === 'function' ? getSelectedVariantIds() : '';
      if (vid === false) return; // validation failed — toast shown by getSelectedVariantIds
      var qty2 = parseInt((document.getElementById('detailQty') || {}).value) || 1;
      mainATC.textContent = 'Adding...';
      mainATC.disabled = true;
      cartAddItem(pid2, vid, qty2, function() {
        mainATC.textContent = 'Added!';
        mainATC.disabled = false;
        setTimeout(function() { mainATC.textContent = 'Add to Cart'; }, 2000);
      });
      return;
    }

    // 5. Detail page wishlist button
    var detailWish = e.target.closest('#detailWishlist');
    if (detailWish) {
      e.preventDefault(); e.stopPropagation();
      toggleWishlist(detailWish.dataset.id, detailWish);
      return;
    }
  });

  // ── Expose public API ────────────────────────────────────────
  window.Venora = {
    cartAddItem:    cartAddItem,
    openCartDrawer: openCartDrawer,
    openQuickView:  openQuickView,
    showToast:      showToast,
    updateCartBadge:updateCartBadge,
    toggleWishlist: toggleWishlist
  };

})();