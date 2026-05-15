<?php
$howHeader = selectContent($conn, "settings_home_how", ["visibility" => "show"])[0];
$steps = selectContentAsc($conn, "panel_how_steps", ["visibility" => "show"], "input_order", 10);
?>
<section id="how" class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label" data-admc-manage="settings_home_how" data-admc-id="<?= $howHeader['id'] ?>"><?= $howHeader['input_label'] ?></span>
      <h2 class="section-title" data-admc-manage="settings_home_how" data-admc-id="<?= $howHeader['id'] ?>"><?= $howHeader['input_title'] ?></h2>
      <p class="section-subtitle" data-admc-manage="settings_home_how" data-admc-id="<?= $howHeader['id'] ?>"><?= $howHeader['text_subtitle'] ?></p>
    </div>

    <div class="steps-grid" data-admc-tb="panel_how_steps">
      <?php foreach ($steps as $step) { ?>
        <div class="step-item">
          <div class="step-num" data-admc-manage="panel_how_steps" data-admc-id="<?= $step['id'] ?>"><?= $step['input_step_number'] ?></div>
          <h3 class="step-title" data-admc-manage="panel_how_steps" data-admc-id="<?= $step['id'] ?>"><?= $step['input_title'] ?></h3>
          <p class="step-desc" data-admc-manage="panel_how_steps" data-admc-id="<?= $step['id'] ?>"><?= $step['text_description'] ?></p>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
