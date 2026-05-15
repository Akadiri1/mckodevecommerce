<?php
$pricingHeader = selectContent($conn, "settings_home_pricing", ["visibility" => "show"])[0];
$eotPricing = selectContentAsc($conn, "panel_pricing_eot", ["visibility" => "show"], "input_order", 20);
$hourlyCards = selectContentAsc($conn, "panel_pricing_hourly", ["visibility" => "show"], "input_order", 10);

$featuresRaw = selectContent($conn, "addition_pricing_features", ["visibility" => "show"]);
$indexedFeatures = [];
foreach ($featuresRaw as $f) {
    $indexedFeatures[$f['tb_link']][] = $f;
}
?>
<section id="pricing" class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label" data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>"><?= $pricingHeader['input_label'] ?></span>
      <h2 class="section-title" data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>"><?= $pricingHeader['input_title'] ?></h2>
      <p class="section-subtitle" data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>"><?= $pricingHeader['text_subtitle'] ?></p>
    </div>

    <!-- Tabs -->
    <div class="pricing-tabs">
      <button class="pricing-tab active" onclick="switchTab('eot', this)"
              data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>">
        <?= $pricingHeader['input_tab_eot'] ?>
      </button>
      <button class="pricing-tab" onclick="switchTab('hourly', this)"
              data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>">
        <?= $pricingHeader['input_tab_hourly'] ?>
      </button>
    </div>

    <!-- EOT Panel -->
    <div class="pricing-panel active" id="panel-eot">
      <div class="eot-table">
        <div class="eot-table-header">
          <span class="eot-th">Property Size</span>
          <span class="eot-th">Approx. Duration</span>
          <span class="eot-th">Our Fixed Price</span>
          <span class="eot-th">Edinburgh Market Range</span>
        </div>

        <div data-admc-tb="panel_pricing_eot">
          <?php foreach ($eotPricing as $row) { ?>
            <div class="eot-row">
              <div>
                <div class="eot-property" data-admc-manage="panel_pricing_eot" data-admc-id="<?= $row['id'] ?>"><?= $row['input_property'] ?></div>
                <div class="eot-detail" data-admc-manage="panel_pricing_eot" data-admc-id="<?= $row['id'] ?>"><?= $row['input_detail'] ?></div>
              </div>
              <div class="eot-time" data-admc-manage="panel_pricing_eot" data-admc-id="<?= $row['id'] ?>"><?= $row['input_duration'] ?></div>
              <div class="eot-price" data-admc-manage="panel_pricing_eot" data-admc-id="<?= $row['id'] ?>"><?= $row['input_price'] ?></div>
              <div class="eot-market" data-admc-manage="panel_pricing_eot" data-admc-id="<?= $row['id'] ?>"><?= $row['input_market'] ?></div>
            </div>
          <?php } ?>
        </div>

        <div class="eot-note" data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>">
          <i class="fa-solid fa-lightbulb"></i> <?= $pricingHeader['text_eot_note'] ?>
        </div>
      </div>

      <div style="margin-top:28px; display:flex; gap:16px; flex-wrap:wrap;">
        <a href="/contact" class="btn btn-primary btn-lg"><i class="fa-solid fa-clipboard-list"></i> Get My EOT Quote</a>
        <p style="font-size:0.85rem;color:var(--grey);align-self:center;"
           data-admc-manage="settings_home_pricing" data-admc-id="<?= $pricingHeader['id'] ?>">
          <?= $pricingHeader['input_agent_note'] ?>
        </p>
      </div>
    </div>

    <!-- Hourly Panel -->
    <div class="pricing-panel" id="panel-hourly">
      <div class="hourly-grid" data-admc-tb="panel_pricing_hourly">
        <?php foreach ($hourlyCards as $card) { ?>
          <div class="hourly-card <?= $card['input_featured'] === 'yes' ? 'featured' : '' ?>">
            <?php if (!empty($card['input_badge'])) { ?>
              <div class="hourly-badge" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><?= $card['input_badge'] ?></div>
            <?php } ?>
            <div <?php if (!empty($card['input_badge'])) { ?>style="margin-top:28px;"<?php } ?>>
              <div class="hourly-icon" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><i class="<?= $card['input_icon'] ?>"></i></div>
              <h3 class="hourly-title" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><?= $card['input_title'] ?></h3>
              <div class="hourly-price-wrap">
                <div class="hourly-price" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><sup>£</sup><?= $card['input_price'] ?></div>
                <div class="hourly-per" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><?= $card['input_per'] ?></div>
              </div>
              <p class="hourly-min" data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>"><?= $card['input_minimum'] ?></p>
              <ul class="hourly-features"
                  data-admc-tb="addition_pricing_features"
                  data-admc-tbadd="panel_pricing_hourly"
                  data-admc-tblink="<?= $card['hash_id'] ?>">
                <?php if (isset($indexedFeatures[$card['hash_id']])) { ?>
                  <?php foreach ($indexedFeatures[$card['hash_id']] as $feat) { ?>
                    <li class="hourly-feature" data-admc-manage="addition_pricing_features" data-admc-id="<?= $feat['id'] ?>"><?= $feat['input_feature'] ?></li>
                  <?php } ?>
                <?php } ?>
              </ul>
              <a href="<?= $card['input_cta_url'] ?>" class="btn <?= $card['input_featured'] === 'yes' ? 'btn-primary' : 'btn-navy' ?>" style="width:100%;justify-content:center;"
                 data-admc-manage="panel_pricing_hourly" data-admc-id="<?= $card['id'] ?>">
                <?= $card['input_cta_text'] ?>
              </a>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

  </div>
</section>
