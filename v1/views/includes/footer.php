<?php
// ── Footer data ──────────────────────────────────────────────
$footerConfig = selectContent($conn, "settings_shop_footer", ["visibility" => "show"]);
$footerConfig = !empty($footerConfig) ? $footerConfig[0] : [];
?>

  <!-- ── Footer ──────────────────────────────────────────────── -->
  <style>
    .footer-section { background-color: transparent !important; }
    .footer-inner { background-color: var(--primary, #202c22) !important; }
    .footer-section .cta {
      background-image: none !important;
      background-color: var(--primary, #202c22) !important;
      color: #ffffff !important;
    }
  </style>
  <section class="footer-section">

    <!-- CTA Banner — editable via ADMC (Only shown on home page) -->
    <?php 
      $isHome = (isset($s1) && ($s1 === '' || $s1 === 'home')) || (isset($currentPath) && ($currentPath === '/' || $currentPath === '/home'));
      if ($isHome): 
    ?>
    <div class="cta">
      <div class="cta-inner">
        <h1 class="heading-01"
            data-admc-manage="settings_shop_footer"
            data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
          <?= htmlspecialchars($footerConfig['input_cta_heading'] ?? 'Ready for Your Best Skin Yet?', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <div class="btn-wrap">
          <a class="btn-02-link w-inline-block" href="/contact"
             data-admc-manage="settings_shop_footer"
             data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
            <div class="btn-inner">
              <div class="btn-text-wrap">
                <div class="btn-text-3 _01"><div class="cta-text">
                  <?= htmlspecialchars($footerConfig['input_cta_btn'] ?? 'Book a Consultation', ENT_QUOTES, 'UTF-8') ?>
                </div></div>
                <div class="btn-text-3 _02"><div class="cta-text">
                  <?= htmlspecialchars($footerConfig['input_cta_btn'] ?? 'Book a Consultation', ENT_QUOTES, 'UTF-8') ?>
                </div></div>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Main footer -->
    <div class="footer">
      <div class="footer-inner">
        <div class="footer-top">

          <!-- Left: newsletter -->
          <div class="footer-left reveal">
            <div class="heading-03"
                 data-admc-manage="settings_shop_footer"
                 data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
              <?= htmlspecialchars($footerConfig['input_newsletter_heading'] ?? 'Stay updated with the latest from ' . $shop_name . '!', ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="newsletter-wrap">
              <form class="newsletter-form-02" id="footerNewsletterForm" onsubmit="handleFooterNewsletter(event)">
                <input class="newsletter-field-02 w-input"
                       type="email" name="email"
                       placeholder="<?= htmlspecialchars($footerConfig['input_newsletter_placeholder'] ?? 'Email address...', ENT_QUOTES, 'UTF-8') ?>"
                       required>
                <button class="submit-button-02 w-button" type="submit">Subscribe</button>
              </form>
              <div id="footerNewsletterMsg" style="display:none;margin-top:8px;font-size:13px;padding:8px 12px;border-radius:4px;" class="p-02"></div>
            </div>
          </div>

          <!-- Right: nav links -->
          <div class="footer-right reveal">
            <div class="footer-link-wrap">
              <a class="nav-link-2 w-inline-block" href="<?= $baseUrl ?>/about">
                <div class="nav-link-text"><p class="p-02 _02">About</p><p class="p-02">About</p></div>
                <div class="footer-underline"></div>
              </a>
              <img alt="" class="slash-icon" src="<?= $baseUrl ?>/assets/img/icons/slash.svg">
              <a class="nav-link-2 w-inline-block" href="<?= $baseUrl ?>/products">
                <div class="nav-link-text"><p class="p-02 _02">Products</p><p class="p-02">Products</p></div>
                <div class="footer-underline"></div>
              </a>
              <img alt="" class="slash-icon" src="<?= $baseUrl ?>/assets/img/icons/slash.svg">
              <a class="nav-link-2 w-inline-block" href="<?= $baseUrl ?>/contact">
                <div class="nav-link-text"><p class="p-02 _02">Contact</p><p class="p-02">Contact</p></div>
                <div class="footer-underline"></div>
              </a>
            </div>
          </div>

        </div>

        <!-- Footer bottom bar -->
        <div class="footer-center">
          <div class="footer-center-inner left">
            <div class="p-02" style="color:#ffffff;"
                 data-admc-manage="settings_shop_footer"
                 data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
              <?= htmlspecialchars($footerConfig['input_powered_by'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
          <div class="footer-center-inner center">
            <a href="<?= $baseUrl ?>/privacy-policy" class="p-02"
               style="color:#ffffff;text-decoration:none;opacity:0.7;transition:opacity 0.2s;"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Privacy policy</a>
          </div>
          <div class="footer-center-inner right">
            <div class="p-02" style="color:#ffffff;opacity:0.7;"
                 data-admc-manage="settings_shop_footer"
                 data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
              &copy; <?= date('Y') ?> <?= htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') ?>. All Rights Reserved.
            </div>
          </div>
        </div>

        <!-- Logo — pulled from settings_shop_config (editable via ADMC) -->
        <a class="footer-logo w-inline-block" href="<?= $baseUrl ?>/">
          <div data-admc-image="settings_shop_config"
               data-admc-id="<?= $shopConfig[0]['id'] ?? 1 ?>">
            <img alt="<?= htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') ?>"
                 class="footer-logo-img"
                 style="max-width:140px;max-height:52px;width:auto;height:auto;object-fit:contain;"
                 src="<?= htmlspecialchars($logo_directory ?: $logo_dark ?: '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </a>

        <!-- Social icons -->
        <div class="footer-bottom">
          <div class="social-block">
          </div>
          <div class="social-block">
            <a class="footer-social-link-02 w-inline-block"
               href="<?= htmlspecialchars($footerConfig['input_instagram'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
               target="_blank" rel="noopener"
               data-admc-manage="settings_shop_footer"
               data-admc-id="<?= $footerConfig['id'] ?? 1 ?>">
              <div class="icon-16"><img alt="Instagram" class="social-icon" src="<?= $baseUrl ?>/assets/img/icons/instagram.svg"></div>
            </a>
            <a class="footer-social-link-02 w-inline-block"
               href="<?= htmlspecialchars($footerConfig['input_facebook'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
               target="_blank" rel="noopener">
              <div class="icon-16"><img alt="Facebook" class="social-icon" src="<?= $baseUrl ?>/assets/img/icons/facebook.svg"></div>
            </a>
            <a class="footer-social-link-02 w-inline-block"
               href="<?= htmlspecialchars($footerConfig['input_linkedin'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
               target="_blank" rel="noopener">
              <div class="icon-16"><img alt="LinkedIn" class="social-icon" src="<?= $baseUrl ?>/assets/img/icons/linkedin.svg"></div>
            </a>
          </div>
        </div>

      </div>
    </div>

  </section>

  <!-- jQuery (for Webflow CSS class compatibility) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- GSAP + ScrollTrigger — same engine Webflow IX2 uses internally -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <!-- Venora App JS -->
  <script src="<?= $baseUrl ?>/assets/js/venora-app.js?v=<?= time() ?>"></script>

  <script>
    // Mobile nav sidebar
    (function() {
      var toggle  = document.getElementById('mobileMenuToggle');
      var sidebar = document.getElementById('mobileNavSidebar');
      var overlay = document.getElementById('mobileNavOverlay');
      var closeBtn = document.getElementById('mobileNavClose');

      function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
      function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }

      if (toggle)  toggle.addEventListener('click', openSidebar);
      if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
      if (overlay) overlay.addEventListener('click', closeSidebar);

      // Close on nav link tap
      sidebar && sidebar.querySelectorAll('.mobile-nav-link').forEach(function(a) {
        a.addEventListener('click', closeSidebar);
      });
    })();


    // Cart drawer close button
    document.getElementById('cartDrawerClose').addEventListener('click', function() {
      document.querySelector('.cart-drawer').classList.remove('active');
      document.querySelector('.v-overlay') && document.querySelector('.v-overlay').classList.remove('active');
      document.body.style.overflow = '';
    });

    // Continue shopping
    document.getElementById('cartContinueShopping').addEventListener('click', function() {
      document.querySelector('.cart-drawer').classList.remove('active');
      document.querySelector('.v-overlay') && document.querySelector('.v-overlay').classList.remove('active');
      document.body.style.overflow = '';
    });

    // Footer newsletter — with validation and server message display
    function handleFooterNewsletter(e) {
      e.preventDefault();
      var form    = e.target;
      var email   = form.querySelector('input[type=email]').value.trim();
      var msgEl   = document.getElementById('footerNewsletterMsg');
      var submitBtn = form.querySelector('button[type=submit]');
      var originalBtnHTML = submitBtn.innerHTML;

      function showMsg(text, isError) {
        if (!msgEl) return;
        msgEl.style.display      = 'flex';
        msgEl.style.alignItems   = 'center';
        msgEl.style.gap          = '8px';
        msgEl.style.padding      = '10px 14px';
        msgEl.style.borderRadius = '8px';
        msgEl.style.fontSize     = '13px';
        msgEl.style.fontWeight   = '500';
        msgEl.style.maxWidth     = '370px';
        msgEl.style.textTransform = 'none';
        msgEl.style.letterSpacing = '0';
        if (isError) {
          msgEl.style.background = 'rgba(193,18,31,0.08)';
          msgEl.style.color      = '#c1121f';
          msgEl.style.border     = '1px solid rgba(193,18,31,0.2)';
          msgEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>' + text + '</span>';
        } else {
          msgEl.style.background = 'rgba(22,163,74,0.08)';
          msgEl.style.color      = '#16a34a';
          msgEl.style.border     = '1px solid rgba(22,163,74,0.2)';
          msgEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><polyline points="20 6 9 17 4 12"/></svg><span>' + text + '</span>';
        }
      }

      if (!email) { showMsg('Please enter your email address.', true); return; }
      var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!re.test(email)) { showMsg('Please enter a valid email address.', true); return; }

      if (submitBtn) { 
        submitBtn.disabled = true; 
        submitBtn.innerHTML = '<img src="' + (window.VENORA_BASE_URL || '') + '/lg.rotating-balls-spinner.gif" alt="Loading..." style="width: 20px; height: 20px;"> Subscribing...';
      }
      if (msgEl) msgEl.style.display = 'none';

      fetch('/newsletter-subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.success) {
          var wrap = form.closest('.newsletter-wrap');
          if (wrap) {
            var text = res.message || 'Thank you! You have been subscribed.';
            wrap.innerHTML = '<div style="display:flex; align-items:center; gap:8px; padding:10px 14px; border-radius:8px; font-size:13px; font-weight:500; max-width:370px; background:rgba(22,163,74,0.08); color:#16a34a; border:1px solid rgba(22,163,74,0.2);">' +
              '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><polyline points="20 6 9 17 4 12"/></svg>' +
              '<span>' + text + '</span>' +
            '</div>';
          }
        } else {
          showMsg(res.message || 'Something went wrong. Please try again.', true);
          if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnHTML; }
        }
      })
      .catch(function() {
        showMsg('Connection error. Please try again.', true);
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnHTML; }
      });
    }
  </script>

  <?php if (isset($_SESSION['admin_id'])): ?>
    <script src="https://admc.dev/admc.min.js" charset="utf-8"></script>
  <?php endif; ?>

</div><!-- /page-wrapper -->
</body>
</html>