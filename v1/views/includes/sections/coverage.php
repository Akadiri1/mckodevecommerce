<?php
$coverageHeader = selectContent($conn, "settings_home_coverage", ["visibility" => "show"])[0];
$coverageAreas = selectContentAsc($conn, "panel_coverage_areas", ["visibility" => "show"], "input_order", 50);

$cityAreas = [];
$surroundingAreas = [];
foreach ($coverageAreas as $area) {
    if ($area['input_group'] === 'city') {
        $cityAreas[] = $area;
    } else {
        $surroundingAreas[] = $area;
    }
}
?>
<section id="coverage" class="section">
  <div class="container">
    <div class="coverage-grid">

      <div class="coverage-areas">
        <span class="section-label" data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>"><?= $coverageHeader['input_label'] ?></span>
        <h2 class="section-title" data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>"><?= $coverageHeader['input_title'] ?></h2>
        <p style="font-size:0.95rem;color:var(--grey);line-height:1.7;margin-bottom:24px;"
           data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>">
          <?= $coverageHeader['text_description'] ?>
        </p>

        <div>
          <p style="font-size:0.82rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;"
             data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>">
            <?= $coverageHeader['input_city_label'] ?>
          </p>
          <div class="area-tags" data-admc-tb="panel_coverage_areas">
            <?php foreach ($cityAreas as $area) { ?>
              <span class="area-tag primary" data-admc-manage="panel_coverage_areas" data-admc-id="<?= $area['id'] ?>"><?= $area['input_name'] ?></span>
            <?php } ?>
          </div>
        </div>
        <div style="margin-top:20px;">
          <p style="font-size:0.82rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;"
             data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>">
            <?= $coverageHeader['input_surrounding_label'] ?>
          </p>
          <div class="area-tags">
            <?php foreach ($surroundingAreas as $area) { ?>
              <span class="area-tag" data-admc-manage="panel_coverage_areas" data-admc-id="<?= $area['id'] ?>"><?= $area['input_name'] ?></span>
            <?php } ?>
          </div>
        </div>
        <div style="margin-top:28px;">
          <p style="font-size:0.88rem;color:var(--grey);"
             data-admc-manage="settings_home_coverage" data-admc-id="<?= $coverageHeader['id'] ?>">
            <i class="fa-solid fa-location-dot"></i> <?= $coverageHeader['input_footer_note'] ?>
          </p>
        </div>
      </div>

      <div class="coverage-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d72033.62044253644!2d-3.2590591!3d55.9532927!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2suk!4v1712800000000"
          width="100%"
          height="100%"
          style="border:0;border-radius:var(--radius-lg);"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="NextShine Cleaning Coverage — Edinburgh & Surrounding Areas">
        </iframe>
      </div>

    </div>
  </div>
</section>
