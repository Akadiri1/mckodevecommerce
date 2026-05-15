<?php
$whyHeader = selectContent($conn, "settings_home_why", ["visibility" => "show"])[0];
$whyCards = selectContentAsc($conn, "panel_why_us", ["visibility" => "show"], "input_order", 20);
?>
<section id="why" class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label" style="color:var(--teal-light)" data-admc-manage="settings_home_why" data-admc-id="<?= $whyHeader['id'] ?>"><?= $whyHeader['input_label'] ?></span>
      <h2 class="section-title" data-admc-manage="settings_home_why" data-admc-id="<?= $whyHeader['id'] ?>"><?= $whyHeader['input_title'] ?></h2>
      <p class="section-subtitle" data-admc-manage="settings_home_why" data-admc-id="<?= $whyHeader['id'] ?>"><?= $whyHeader['text_subtitle'] ?></p>
    </div>

    <div class="why-grid" data-admc-tb="panel_why_us">
      <?php foreach ($whyCards as $card) { ?>
        <div class="why-card">
          <div class="why-icon" data-admc-manage="panel_why_us" data-admc-id="<?= $card['id'] ?>"><i class="<?= $card['input_icon'] ?>"></i></div>
          <h3 class="why-title" data-admc-manage="panel_why_us" data-admc-id="<?= $card['id'] ?>"><?= $card['input_title'] ?></h3>
          <p class="why-desc" data-admc-manage="panel_why_us" data-admc-id="<?= $card['id'] ?>"><?= $card['text_description'] ?></p>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
