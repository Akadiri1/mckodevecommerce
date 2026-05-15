<?php
$servicesHeader = selectContent($conn, "settings_home_services", ["visibility" => "show"])[0];
$services = selectContentAsc($conn, "panel_services", ["visibility" => "show"], "input_order", 20);
?>
<section id="services" class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label" data-admc-manage="settings_home_services" data-admc-id="<?= $servicesHeader['id'] ?>"><?= $servicesHeader['input_label'] ?></span>
      <h2 class="section-title" data-admc-manage="settings_home_services" data-admc-id="<?= $servicesHeader['id'] ?>"><?= $servicesHeader['input_title'] ?></h2>
      <p class="section-subtitle" data-admc-manage="settings_home_services" data-admc-id="<?= $servicesHeader['id'] ?>"><?= $servicesHeader['text_subtitle'] ?></p>
    </div>

    <div class="services-grid" data-admc-tb="panel_services">
      <?php foreach ($services as $svc) { ?>
        <?php $svcUrl = '/services/' . $svc['hash_id'] . '/' . ($svc['input_slug'] ?? $svc['hash_id']); ?>
        <div class="service-card">
          <a href="<?= $svcUrl ?>" style="display:block;">
            <div class="service-card-img"
                 style="background: linear-gradient(135deg, <?= $svc['bgcolor_card_start'] ?> 0%, <?= $svc['bgcolor_card_end'] ?> 100%);"
                 data-admc-image="panel_services"
                 data-admc-id="<?= $svc['id'] ?>">
              <?php if (!empty($svc['image_1'])) { ?>
                <img src="<?= $svc['image_1'] ?>" alt="<?= $svc['input_title'] ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;" />
              <?php } else { ?>
                <span><i class="<?= $svc['input_icon'] ?>"></i></span>
              <?php } ?>
              <?php if (!empty($svc['input_badge'])) { ?>
                <span class="service-card-badge" data-admc-manage="panel_services" data-admc-id="<?= $svc['id'] ?>"><?= $svc['input_badge'] ?></span>
              <?php } ?>
            </div>
          </a>
          <div class="service-card-body">
            <h3 class="service-card-title"><a href="<?= $svcUrl ?>" style="color:inherit;" data-admc-manage="panel_services" data-admc-id="<?= $svc['id'] ?>"><?= $svc['input_title'] ?></a></h3>
            <p class="service-card-desc" data-admc-manage="panel_services" data-admc-id="<?= $svc['id'] ?>"><?= $svc['text_description'] ?></p>
            <div class="service-card-price">
              <span class="service-price-tag" data-admc-manage="panel_services" data-admc-id="<?= $svc['id'] ?>"><?= $svc['input_price_tag'] ?></span>
              <a class="service-card-link" href="<?= $svcUrl ?>">Learn more →</a>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
