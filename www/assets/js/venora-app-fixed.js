  // ── Event delegation ─────────────────────────────────────────
  document.addEventListener('click', function(e) {
    // 1. Quick view open
    var qvBtn = e.target.closest('.quick-view-btn');
    if (qvBtn) {
      e.preventDefault();
      e.stopPropagation();
      openQuickView(qvBtn.dataset.id);
      return;
    }

    // 2. Product card add to cart
    var addBtn = e.target.closest('.add-to-card-02');
    if (addBtn) {
      e.preventDefault();
      e.stopPropagation();
      if (addBtn.classList.contains('is-adding')) return;
      
      var pid = addBtn.dataset.productId;
      if (!pid) {
          var card = addBtn.closest('[data-product-id]');
          pid = card ? card.dataset.productId : null;
      }
      
      if (pid) {
        addBtn.classList.add('is-adding');
        cartAddItem(pid, '', 1, function() {
          addBtn.classList.remove('is-adding');
        });
      }
      return;
    }

    // 3. Cart open buttons
    if (e.target.closest('[data-open-cart]') || e.target.closest('.cart-btn-wrap')) {
      e.preventDefault(); openCartDrawer();
    }
    // 4. Cart drawer close
    if (e.target.closest('#cartDrawerClose')) { closeCartDrawer(); hideOverlay(); }
    
    // 5. Wishlist on card
    var wlBtn = e.target.closest('.wishlist-btn-card');
    if (wlBtn) { e.preventDefault(); e.stopPropagation(); toggleWishlist(wlBtn.dataset.id, wlBtn); }
    
    // 6. Search open/close
    if (e.target.closest('[data-open-search]')) { e.preventDefault(); openSearch(); }
    if (e.target.closest('.search-close')) { closeSearch(); hideOverlay(); }
    
    // 7. Main add-to-cart button on product detail
    var mainATC = e.target.closest('.btn-add-to-cart-main');
    if (mainATC) {
      e.preventDefault();
      var pid2 = mainATC.dataset.productId;
      var vid  = typeof getSelectedVariantIds === 'function' ? getSelectedVariantIds() : '';
      var qty2 = parseInt((document.getElementById('detailQty') || {}).value) || 1;
      mainATC.textContent = 'Adding...';
      cartAddItem(pid2, vid, qty2, function() {
        mainATC.textContent = 'Added!';
        setTimeout(function() { mainATC.textContent = 'Add to Cart'; }, 2000);
      });
    }
  });

  // ── Expose public API ────────────────────────────────────────
  window.Venora = {
    cartAddItem:    cartAddItem,
    openCartDrawer: openCartDrawer,
    openQuickView:  openQuickView,
    showToast:      showToast,
    updateCartBadge:updateCartBadge
  };

})();