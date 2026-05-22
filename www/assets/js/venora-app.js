(function() {
  'use strict';

  var baseUrl = window.VENORA_BASE_URL || '';
  var $  = function(s, p) { return (p || document).querySelector(s); };
  var $$ = function(s, p) { return Array.from((p || document).querySelectorAll(s)); };
  var on = function(el, ev, fn) { if(el) { if(Array.isArray(el)) el.forEach(e => e.addEventListener(ev, fn)); else el.addEventListener(ev, fn); } };

  function formatPrice(amount, symbol) {
    var val = parseFloat(amount);
    if (isNaN(val)) val = 0;
    var sym = symbol || window.VENORA_CURRENCY_SYMBOL || '₦';
    return sym + val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function previewBody(html, limit) {
    var tmp = document.createElement('div');
    tmp.innerHTML = html;
    var text = tmp.textContent || tmp.innerText || '';
    var words = text.split(/\s+/);
    if (words.length > limit) return words.slice(0, limit).join(' ') + '...';
    return text;
  }

  // ── Cart drawer ──────────────────────────────────────────────
  var cartDrawer = $('.cart-drawer');
  function openCartDrawer() { showOverlay(); if(cartDrawer) cartDrawer.classList.add('active'); refreshCartDrawer(); }
  function closeCartDrawer() { if(cartDrawer) cartDrawer.classList.remove('active'); hideOverlay(); }

  function getOverlay() {
    var overlay = $('.v-overlay');
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
  }

  function updateCartBadge(count) {
    var badges = $$('.cart-badge');
    badges.forEach(function(b) {
      b.textContent = count;
      b.style.display = (count > 0) ? 'flex' : 'none';
    });
  }

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
        updateCartBadge(data.total_quantity || 0);
        if (!data.items || data.items.length === 0) {
          itemsEl.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛍</div><h3>Your cart is empty</h3><p>Add something you love to get started.</p><a href="/products" class="cart-checkout-btn" style="text-decoration:none;display:inline-block;margin:0 auto;">Shop Now</a></div>';
          if (subEl) subEl.textContent = formatPrice(0);
          return;
        }
        var html = '';
        data.items.forEach(function(item) {
          html += '<div class="cart-item" data-cart-id="' + item.cart_id + '">' +
            '<img src="' + (item.image || '/assets/img/icons/cart.svg') + '" class="cart-item-img" alt="' + item.product_name + '">' +
            '<div class="cart-item-info">' +
              '<h4 style="margin:0; font-size:14px; font-weight:700;">' + item.product_name + '</h4>' +
              (item.variant_options ? '<div class="cart-item-variant" style="font-size:11px; color:#888; margin-top:2px;">' + item.variant_options + '</div>' : '') +
              '<div class="cart-item-price-row" style="margin-top:6px;">' +
                '<div class="p-ngn" style="font-weight:800; color:#072708; font-size:14px;">' + item.formatted_price_ngn + '</div>' +
                '<div class="p-usd" style="font-weight:500; color:#888; font-size:12px;">' + item.formatted_price_usd + '</div>' +
              '</div>' +
              '<div class="qty-control" style="margin-top:10px;">' +
                '<button class="qty-btn" data-action="decrease" data-id="' + item.cart_id + '">−</button>' +
                '<input class="qty-input" type="number" value="' + item.quantity + '" min="1" data-id="' + item.cart_id + '">' +
                '<button class="qty-btn" data-action="increase" data-id="' + item.cart_id + '">+</button>' +
              '</div>' +
            '</div>' +
            '<div class="cart-item-actions">' +
              '<button class="cart-item-remove" data-id="' + item.cart_id + '" style="background:none; border:none; color:#c1121f; font-size:11px; font-weight:700; cursor:pointer; padding:0;">REMOVE</button>' +
            '</div>' +
          '</div>';
        });
        itemsEl.innerHTML = html;
        
        var footerEl = $('.cart-drawer-footer');
        if (footerEl) {
          footerEl.innerHTML = '<div class="cart-subtotal-row" style="margin-bottom:8px;">' +
              '<span class="cart-subtotal-label">Total Items</span>' +
              '<span class="cart-subtotal-value" id="cartTotalQty">' + (data.total_quantity || 0) + '</span>' +
            '</div>' +
            '<div class="cart-subtotal-row">' +
              '<span class="cart-subtotal-label">Subtotal (NGN)</span>' +
              '<span class="cart-subtotal-value" style="font-weight:800;">' + data.formatted_total_ngn + '</span>' +
            '</div>' +
            '<div class="cart-subtotal-row" style="margin-top:4px;">' +
              '<span class="cart-subtotal-label">Subtotal (USD)</span>' +
              '<span class="cart-subtotal-value" style="color:#888;">' + data.formatted_total_usd + '</span>' +
            '</div>' +
            '<p class="cart-subtotal-note">Shipping &amp; taxes calculated at checkout</p>' +
            '<a href="' + baseUrl + '/checkout" class="cart-checkout-btn" style="text-decoration:none;">Continue to Checkout</a>' +
            '<a href="' + baseUrl + '/cart" class="cart-continue-btn" style="text-decoration:none;text-align:center;display:block;">View Cart</a>';
        }

        // Bind qty + remove
        $$('.cart-item-remove', itemsEl).forEach(function(btn) {
          on(btn, 'click', function() { cartRemoveItem(btn.dataset.id); });
        });
        $$('.qty-btn', itemsEl).forEach(function(btn) {
          on(btn, 'click', function() {
            var input = itemsEl.querySelector('.qty-input[data-id="' + btn.dataset.id + '"]');
            var qty = parseInt(input.value) + (btn.dataset.action === 'increase' ? 1 : -1);
            if (qty < 1) { cartRemoveItem(btn.dataset.id); return; }
            cartUpdateItem(btn.dataset.id, qty);
          });
        });
        $$('.qty-input', itemsEl).forEach(function(input) {
          on(input, 'change', function() {
            var qty = parseInt(input.value);
            if (isNaN(qty) || qty < 1) { refreshCartDrawer(); return; }
            cartUpdateItem(input.dataset.id, qty);
          });
        });
      })
      .catch(function(err) { itemsEl.innerHTML = '<div style="padding:20px;text-align:center;color:#ef4444;">Error loading cart</div>'; });
  }

  function cartAddItem(productId, variantId, qty, onSuccess, clickedBtn) {
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
        animateFlyToCart(clickedBtn);
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
    .then(function(r) { return r.json(); })
    .then(function(data) { 
        if (data.success) { 
            updateCartBadge(data.cart_count); 
            refreshCartDrawer(); 
        } else {
            showToast(data.error || 'Could not update quantity', 'error');
            refreshCartDrawer(); 
        }
    })
    .catch(function() { showToast('Update failed', 'error'); });
  }

  function animateFlyToCart(clickedBtn) {
    if (!clickedBtn) return;
    var cartIcon = $('[data-open-cart]');
    if (!cartIcon) return;
    
    // Find the product image. Support both grid cards and detail page.
    var parent = clickedBtn.closest('.product-card') || clickedBtn.closest('.product-single') || clickedBtn.closest('.quick-view-modal');
    if (!parent) return;
    var sourceImg = parent.querySelector('.all-img') || parent.querySelector('#mainProductImg') || parent.querySelector('#qvMainImg');
    if (!sourceImg) return;

    var rect = sourceImg.getBoundingClientRect();
    var targetRect = cartIcon.getBoundingClientRect();
    var clone = document.createElement('img');
    clone.src = sourceImg.src;
    clone.className = 'cart-flyer';
    clone.style.position = 'fixed';
    clone.style.top = rect.top + 'px';
    clone.style.left = rect.left + 'px';
    var startSize = Math.min(rect.width, 120);
    var ratio = startSize / rect.width;
    clone.style.width = startSize + 'px';
    clone.style.height = (rect.height * ratio) + 'px';
    clone.style.zIndex = '999999';
    clone.style.transition = 'all 1.5s cubic-bezier(0.19, 1, 0.22, 1)';
    clone.style.pointerEvents = 'none';
    clone.style.borderRadius = '8px';
    document.body.appendChild(clone);
    clone.offsetWidth;
    clone.style.top = (targetRect.top + (targetRect.height / 2) - 10) + 'px';
    clone.style.left = (targetRect.left + (targetRect.width / 2) - 10) + 'px';
    clone.style.width = '20px';
    clone.style.height = '20px';
    clone.style.opacity = '0.05';
    clone.style.transform = 'scale(0.05) rotate(1080deg)';
    setTimeout(function() { clone.remove(); cartIcon.classList.add('cart-wiggle'); setTimeout(function() { cartIcon.classList.remove('cart-wiggle'); }, 600); }, 1500);
  }

  function showToast(msg, type, duration) {
    var toastContainer = $('.toast-container');
    if (!toastContainer) { toastContainer = document.createElement('div'); toastContainer.className = 'toast-container'; document.body.appendChild(toastContainer); }
    var t = document.createElement('div'); t.className = 'toast' + (type ? ' ' + type : '');
    t.innerHTML = '<span class="toast-icon">' + (type === 'error' ? '✕' : '✓') + '</span>' + msg;
    toastContainer.appendChild(t); setTimeout(function() { t.classList.add('show'); }, 20);
    setTimeout(function() { t.classList.remove('show'); setTimeout(function() { t.remove(); }, 400); }, duration || 3000);
  }

  // ── Quick View Modal ─────────────────────────────────────────
  var qvModal = null;
  var currentGallery = [];
  var currentGalleryIdx = 0;
  var currentProduct = null;

  function openQuickView(productId) {
    if (!qvModal) {
      qvModal = document.createElement('div');
      qvModal.className = 'quick-view-modal';
      qvModal.innerHTML = '<div class="modal-handle"><div class="modal-handle-bar"></div></div><button class="modal-close" id="qvClose">✕</button><div class="quick-view-modal-inner" id="qvContent"><div style="text-align:center;padding:48px;">Loading...</div></div>';
      document.body.appendChild(qvModal);
      on($('#qvClose', qvModal), 'click', function() { closeQuickView(); hideOverlay(); });
    }
    qvModal.classList.add('active');
    showOverlay();
    var content = $('#qvContent', qvModal);
    content.innerHTML = '<div style="text-align:center;padding:48px;grid-column:span 2;">Loading...</div>';
    fetch(baseUrl + '/quick-view?id=' + productId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { if (!r.ok) return r.json().then(function(err){ throw new Error(err.error || 'Server error'); }); return r.json(); })
      .then(function(p) { currentProduct = p; renderQuickView(p); })
      .catch(function(err) { content.innerHTML = '<div style="color:#c1121f;padding:24px;grid-column:span 2;text-align:center;"><div style="font-size:40px;margin-bottom:16px;">⚠️</div><strong>Could not load product.</strong></div>'; });
  }

  function renderQuickView(p) {
    var content = $('#qvContent', qvModal);
    currentGallery = [p.primary_image].concat(p.images || []).filter(Boolean);
    currentGalleryIdx = 0;
    var thumbsHtml = currentGallery.map(function(img, i) { return '<div class="modal-thumb' + (i === 0 ? ' active' : '') + '" data-idx="' + i + '"><img src="' + img + '" alt=""></div>'; }).join('');
    var variantsHtml = '';
    if (p.variants && p.variants.length) {
      var allOptions = {};
      p.variants.forEach(function(v) { v.options.forEach(function(opt) {
          if (!allOptions[opt.option_id]) { allOptions[opt.option_id] = { name: opt.option_name, values: {} }; }
          allOptions[opt.option_id].values[opt.value_id] = opt.value_name;
      }); });
      Object.keys(allOptions).forEach(function(oid) {
        var opt = allOptions[oid];
        variantsHtml += '<div class="modal-variants" style="margin-bottom:16px;"><div class="modal-variant-label" style="font-weight:700; margin-bottom:8px; font-size:13px; color:var(--text-primary); text-transform:uppercase;">' + opt.name + ':</div><div class="modal-variant-options" data-option-id="' + oid + '" style="display:flex; flex-wrap:wrap; gap:8px;">' + Object.keys(opt.values).map(function(vid) { return '<button type="button" class="modal-variant-btn" data-value-id="' + vid + '">' + opt.values[vid] + '</button>'; }).join('') + '</div></div>';
      });
    }
    var rating = parseFloat(p.input_rating || 4.5);
    var starsHtml = '';
    for (var s = 1; s <= 5; s++) { starsHtml += '<img src="' + baseUrl + '/assets/img/icons/star.svg" class="star" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:2px;' + (s > Math.round(rating) ? 'opacity:0.3;' : '') + '" alt="">'; }
    
    var priceNgn = p.price_range_ngn ? (p.price_range_ngn.price || p.price_range_ngn.min || 0) : 0;
    var priceUsd = p.price_range_usd ? (p.price_range_usd.price || p.price_range_usd.min || 0) : 0;
    var isRangeNgn = p.price_range_ngn && p.price_range_ngn.min && p.price_range_ngn.min != p.price_range_ngn.max;
    var isRangeUsd = p.price_range_usd && p.price_range_usd.min && p.price_range_usd.min != p.price_range_usd.max;

    content.innerHTML = '<div class="modal-gallery"><img src="' + (currentGallery[0] || '') + '" class="modal-main-img" id="qvMainImg" alt="' + p.input_title + '">' + (currentGallery.length > 1 ? '<div class="modal-thumbs">' + thumbsHtml + '</div>' : '') + '</div><div class="modal-details"><div class="modal-category">' + (p.select_category || '') + '</div><h2 class="modal-title">' + p.input_title + '</h2><div class="modal-rating"><div class="modal-stars">' + starsHtml + '</div><span class="modal-rating-text">(' + (p.input_reviews_count || 0) + ' Reviews)</span></div><p class="modal-short-desc">' + previewBody(p.text_description || '', 30) + '</p><div class="modal-price" style="display:flex !important; flex-direction:column !important; gap:4px !important; align-items: flex-start !important; text-align: left !important; margin-bottom: 20px;"><span class="modal-price-current" id="qvPrice" style="text-align: left !important;">' + (isRangeNgn ? "From " : "") + formatPrice(priceNgn, "₦") + '</span><span style="font-size:14px; color:#888; font-weight:500; text-align: left !important;" id="qvPriceUSD">' + (isRangeUsd ? "From " : "") + formatPrice(priceUsd, "$") + '</span></div>' + variantsHtml + '<div class="modal-qty-row" style="margin-top:20px;"><span class="modal-qty-label">Qty</span><div class="qty-control"><button class="qty-btn" id="qvQtyMinus">−</button><input class="qty-input" id="qvQtyInput" type="number" value="1" min="1"><button class="qty-btn" id="qvQtyPlus">+</button></div></div><div class="product-stock-status" style="margin:16px 0;"></div><div class="modal-actions"><button class="modal-add-to-cart" id="qvAddToCart" data-product-id="' + p.hash_id + '"><img src="' + window.VENORA_BASE_URL + '/assets/img/icons/cart-white.svg" style="width:18px;height:18px;" alt=""> Add to Cart</button><button class="modal-wishlist-btn" id="qvWishlist" data-id="' + p.hash_id + '"><img src="' + window.VENORA_BASE_URL + '/assets/img/icons/heart-outline.svg" alt="Wishlist" id="qvWishlistImg"></button></div></div>';

    // Auto-select first options in modal
    $$('.modal-variant-options', qvModal).forEach(function(group) {
        var firstBtn = $('.modal-variant-btn', group);
        if (firstBtn) firstBtn.classList.add('active');
    });

    resolveVariantInModal();

    $$('.modal-thumb', qvModal).forEach(function(thumb) { on(thumb, 'click', function() { currentGalleryIdx = parseInt(thumb.dataset.idx); $('#qvMainImg', qvModal).src = currentGallery[currentGalleryIdx]; $$('.modal-thumb', qvModal).forEach(function(t) { t.classList.remove('active'); }); thumb.classList.add('active'); }); });
    $$('.modal-variant-btn', qvModal).forEach(function(btn) { on(btn, 'click', function() { var parent = btn.closest('.modal-variant-options'); var wasActive = btn.classList.contains('active'); $$('.modal-variant-btn', parent).forEach(function(b) { b.classList.remove('active'); }); if (!wasActive) btn.classList.add('active'); resolveVariantInModal(); }); });
    on($('#qvQtyMinus', qvModal), 'click', function() { var input = $('#qvQtyInput', qvModal); if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1; });
    on($('#qvQtyPlus', qvModal), 'click', function() { var input = $('#qvQtyInput', qvModal); input.value = parseInt(input.value) + 1; });
    on($('#qvAddToCart', qvModal), 'click', function() {
      var btn = this; var totalGroups = $$('.modal-variant-options', qvModal).length; var activeVariants = $$('.modal-variant-btn.active', qvModal);
      if (activeVariants.length < totalGroups) { showToast('Please select all options', 'error'); return; }
      var selectedValueIds = Array.from(activeVariants).map(v => parseInt(v.dataset.valueId));
      var match = p.variants.find(function(v) { var vOptionValueIds = v.options.map(o => o.value_id); return selectedValueIds.every(id => vOptionValueIds.includes(id)); });
      if (!match) { showToast('Selection unavailable.', 'error'); return; }
      var qty = parseInt($('#qvQtyInput', qvModal).value) || 1;
      btn.disabled = true; btn.textContent = 'Adding...';
      cartAddItem(p.hash_id, match.id, qty, function() { btn.disabled = false; btn.innerHTML = '<img src="' + window.VENORA_BASE_URL + '/assets/img/icons/cart-white.svg" style="width:18px;height:18px;" alt=""> Add to Cart'; closeQuickView(); hideOverlay(); }, btn);
    });
    on($('#qvWishlist', qvModal), 'click', function() { toggleWishlist(p.hash_id, this); });

    // Auto-select first value in each option group so price shows immediately
    $$('.modal-variant-options', qvModal).forEach(function(group) {
      var firstBtn = group.querySelector('.modal-variant-btn');
      if (firstBtn && !group.querySelector('.modal-variant-btn.active')) {
        firstBtn.classList.add('active');
      }
    });
    resolveVariantInModal();
  }

  function resolveVariantInModal() {
    if (!currentProduct) return;
    var activeBtns = Array.from($$('.modal-variant-btn.active', qvModal));
    var selectedValueIds = activeBtns.map(b => parseInt(b.dataset.valueId));
    var priceEl = $('#qvPrice', qvModal), priceUsdEl = $('#qvPriceUSD', qvModal), stockEl = $('.product-stock-status', qvModal), addBtn = $('#qvAddToCart', qvModal), totalGroups = $$('.modal-variant-options', qvModal).length;
    var possibleVariants = currentProduct.variants.filter(function(v) { var vValIds = v.options.map(o => o.value_id); return selectedValueIds.every(id => vValIds.includes(id)); });

    $$('.modal-variant-options', qvModal).forEach(function(group) {
        $$('.modal-variant-btn', group).forEach(function(btn) {
            var valId = parseInt(btn.dataset.valueId);
            var otherSelections = activeBtns.filter(b => b.closest('.modal-variant-options') !== group).map(b => parseInt(b.dataset.valueId));
            var isPossible = currentProduct.variants.some(v => { var vValIds = v.options.map(o => o.value_id); return [...otherSelections, valId].every(id => vValIds.includes(id)); });
            btn.style.opacity = isPossible ? "1" : "0.2"; btn.style.pointerEvents = isPossible ? "auto" : "none";
        });
    });

    if (possibleVariants.length > 0) {
      if (selectedValueIds.length === totalGroups) {
        var match = possibleVariants[0];
        priceEl.textContent = formatPrice(match.price_ngn, "₦");
        if (priceUsdEl) priceUsdEl.textContent = formatPrice(match.price_usd, "$");
        if (match.inventory <= 0) { stockEl.innerHTML = '<span class="stock-badge stock-out">Out of stock</span>'; addBtn.disabled = true; addBtn.textContent = 'Out of Stock'; addBtn.style.opacity = '0.6'; }
        else { stockEl.innerHTML = (match.inventory <= 5) ? '<span class="stock-badge stock-low">Only ' + match.inventory + ' left</span>' : '<span class="stock-badge stock-high">In stock</span>'; addBtn.disabled = false; addBtn.innerHTML = '<img src="' + window.VENORA_BASE_URL + '/assets/img/icons/cart-white.svg" style="width:18px;height:18px;" alt=""> Add to Cart'; addBtn.style.opacity = '1'; }
      } else {
          var pricesNgn = possibleVariants.map(v => parseFloat(v.price_ngn)), pricesUsd = possibleVariants.map(v => parseFloat(v.price_usd));
          var minNgn = Math.min(...pricesNgn), minUsd = Math.min(...pricesUsd);
          priceEl.textContent = (pricesNgn.length > 1 ? "From " : "") + formatPrice(minNgn, "₦");
          if (priceUsdEl) priceUsdEl.textContent = (pricesUsd.length > 1 ? "From " : "") + formatPrice(minUsd, "$");
          addBtn.disabled = false; addBtn.textContent = 'Add to Cart';
      }
    } else {
      priceEl.textContent = formatPrice(currentProduct.price_range_ngn.min || 0, "₦");
      if (priceUsdEl) priceUsdEl.textContent = formatPrice(currentProduct.price_range_usd.min || 0, "$");
    }
  }

  function openSearch() { var s = $('#searchOverlay'); if(s) { s.classList.add('active'); showOverlay(); setTimeout(() => $('#searchInput').focus(), 100); } }
  function closeSearch() { var s = $('#searchOverlay'); if(s) { s.classList.remove('active'); hideOverlay(); } }

  // ── Live Search ─────────────────────────────────────────────
  var searchInput = $('#searchInput');
  var searchGrid  = $('#searchResultsGrid');
  if (searchInput && searchGrid) {
    on(searchInput, 'input', function() {
      var query = this.value.trim();
      if (query.length < 2) { searchGrid.innerHTML = ''; return; }
      fetch(baseUrl + '/search-backend?q=' + encodeURIComponent(query))
      .then(r => r.json()).then(data => {
        if (!data.products || !data.products.length) { searchGrid.innerHTML = '<div style="grid-column:span 3;text-align:center;padding:40px;color:#888;">No products found.</div>'; return; }
        
        var html = data.products.map(function(p) {
            var url = baseUrl + '/products/' + p.hash_id + '/' + (p.input_slug || '');
            var img = p.image_1 || '';
            var name = p.input_title || 'Unnamed Product';
            var price = formatPrice(p.input_price || 0);

            return '<a href="' + url + '" class="search-result-item">' +
                     '<img src="' + img + '" class="search-result-img" alt="">' +
                     '<div class="search-result-info">' +
                       '<div class="search-result-name">' + name + '</div>' +
                       '<div class="search-result-price">' + price + '</div>' +
                     '</div>' +
                   '</a>';
        }).join('');
        searchGrid.innerHTML = html;
      });
    });
  }

  function closeQuickView() { if (qvModal) qvModal.classList.remove('active'); }

  function toggleWishlist(id, btn) {
    fetch(baseUrl + '/wishlist-toggle', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({product_id:id}) })
    .then(r => r.json()).then(data => {
      if (data.login_required) { showToast('Please login first', 'error'); setTimeout(() => { window.location.href = baseUrl + '/customer-login'; }, 1200); return; }
      showToast(data.added ? 'Added to wishlist!' : 'Removed from wishlist');
      if (btn) { btn.classList.toggle('active', data.added); var img = btn.querySelector('img'); if (img) img.src = baseUrl + (data.added ? '/assets/img/icons/heart-filled.svg' : '/assets/img/icons/heart-outline.svg'); }
    });
  }

  // ── Event delegation ─────────────────────────────────────────
  document.addEventListener('click', function(e) {
    var qvBtn = e.target.closest('.quick-view-btn');
    if (qvBtn) { e.preventDefault(); e.stopPropagation(); openQuickView(qvBtn.dataset.id); return; }
    var cartToggle = e.target.closest('[data-open-cart]');
    if (cartToggle) { e.preventDefault(); openCartDrawer(); return; }
    var searchOpen = e.target.closest('[data-open-search]');
    if (searchOpen) { e.preventDefault(); openSearch(); return; }
    var searchClose = e.target.closest('.search-close');
    if (searchClose) { e.preventDefault(); closeSearch(); return; }
  });

  window.Venora = {
    cartAddItem:    cartAddItem,
    refreshCartDrawer: refreshCartDrawer,
    showToast:      showToast,
    toggleWishlist: toggleWishlist
  };

  if (typeof window.VENORA_CART_COUNT !== 'undefined') { updateCartBadge(window.VENORA_CART_COUNT); }
  refreshCartDrawer();

})();
