<?php
$hero = selectContent($conn, "settings_home_hero", ["visibility" => "show"])[0];
$whatsappNumber = $websiteInfo[0]['input_whatsapp_number'] ?? '';
$whatsappUrl = !empty($whatsappNumber) ? 'https://wa.me/' . preg_replace('/\D/', '', $whatsappNumber) : '#';
?>
<section id="hero" <?php if (!empty($hero['image_1'])) { ?>style="background-image: linear-gradient(135deg, rgba(26,51,92,0.92) 0%, rgba(0,135,138,0.80) 100%), url('<?= $hero['image_1'] ?>');" class="has-image"<?php } ?>>
  <div class="container">
    <div class="hero-content">
      <!-- Left: Headline & CTA -->
      <div>
        <div class="hero-badge"
             data-admc-manage="settings_home_hero"
             data-admc-id="<?= $hero['id'] ?>">
          <span class="hero-badge-dot"></span>
          <?= $hero['input_badge_text'] ?>
        </div>

        <h1 class="hero-headline">
          <span data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_headline_1'] ?></span><br/>
          <span data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><span><?= $hero['input_headline_2'] ?></span></span><br/>
          <span data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_headline_3'] ?></span>
        </h1>

        <p class="hero-desc"
           data-admc-manage="settings_home_hero"
           data-admc-id="<?= $hero['id'] ?>">
          <?= $hero['text_description'] ?>
        </p>

        <div class="hero-actions">
          <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
          </a>
          <a href="tel:<?= $site_phone ?? '' ?>" class="btn btn-outline btn-lg">
            <i class="fa-solid fa-phone"></i> Call Us Now
          </a>
        </div>

        <div class="hero-stats">
          <?php if (!empty($hero['input_stat_1_value'])) { ?>
          <div class="hero-stat-item">
            <span class="hero-stat-num" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_1_value'] ?></span>
            <span class="hero-stat-label" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_1_label'] ?></span>
          </div>
          <?php } ?>
          <?php if (!empty($hero['input_stat_2_value'])) { ?>
          <div class="hero-stat-item">
            <span class="hero-stat-num" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_2_value'] ?></span>
            <span class="hero-stat-label" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_2_label'] ?></span>
          </div>
          <?php } ?>
          <?php if (!empty($hero['input_stat_3_value'])) { ?>
          <div class="hero-stat-item">
            <span class="hero-stat-num" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_3_value'] ?></span>
            <span class="hero-stat-label" data-admc-manage="settings_home_hero" data-admc-id="<?= $hero['id'] ?>"><?= $hero['input_stat_3_label'] ?></span>
          </div>
          <?php } ?>
        </div>
      </div>

      <!-- Right: Pricing CTA Card -->
      <div class="hero-card" style="text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:320px;">
        <div style="font-size:3.5rem;margin-bottom:20px;color:var(--teal-light);"><i class="fa-solid fa-tag"></i></div>
        <h2 style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:800;color:var(--white);margin-bottom:12px;">Transparent Pricing</h2>
        <p style="font-size:0.95rem;color:rgba(255,255,255,0.7);margin-bottom:28px;line-height:1.6;max-width:320px;">
          Fixed prices for end-of-tenancy. Competitive hourly rates for domestic and commercial. No hidden fees.
        </p>
        <a href="/pricing" class="btn btn-primary btn-lg" style="width:100%;max-width:280px;justify-content:center;">
          <i class="fa-solid fa-tag"></i> Get Current Pricing
        </a>
        <a href="/contact" class="btn btn-outline" style="margin-top:12px;width:100%;max-width:280px;justify-content:center;">
          <i class="fa-solid fa-clipboard-list"></i> Request a Free Quote
        </a>
      </div>
    </div>
  </div>
</section>
