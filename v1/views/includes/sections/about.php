<?php
$about = selectContent($conn, "settings_home_about", ["visibility" => "show"])[0];
$aboutBullets = selectContentAsc($conn, "panel_about_bullets", ["visibility" => "show"], "input_order", 10);
?>
<section id="about" class="section">
  <div class="container">
    <div class="about-grid">

      <div class="about-img-wrap">
        <div class="about-img" data-admc-image="settings_home_about" data-admc-id="<?= $about['id'] ?>">
          <?php if (!empty($about['image_1'])) { ?>
            <img src="<?= $about['image_1'] ?>" alt="About NextShine" />
          <?php } else { ?>
            <span class="about-img-icon"><i class="fa-solid fa-people-group"></i></span>
            <p class="about-img-placeholder"><i class="fa-solid fa-camera"></i> Replace with a professional photo</p>
          <?php } ?>
        </div>
        <div class="about-floatcard">
          <div class="about-floatcard-icon"><i class="fa-solid fa-house"></i></div>
          <div>
            <div class="about-floatcard-num" data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>"><?= $about['input_floatcard_title'] ?></div>
            <div class="about-floatcard-label" data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>"><?= $about['input_floatcard_label'] ?></div>
          </div>
        </div>
      </div>

      <div>
        <span class="section-label" data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>"><?= $about['input_label'] ?></span>
        <h2 class="section-title" data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>"><?= $about['input_title'] ?></h2>

        <div class="about-quote" data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>">
          "<?= $about['text_quote'] ?>"
          <strong style="display:block;margin-top:8px;color:var(--teal);"
                  data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>">
            <?= $about['input_quote_author'] ?>
          </strong>
        </div>

        <p style="font-size:0.95rem;color:var(--grey-dark);line-height:1.75;margin-bottom:20px;"
           data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>">
          <?= $about['text_description'] ?>
        </p>

        <div class="about-bullets" data-admc-tb="panel_about_bullets">
          <?php foreach ($aboutBullets as $bullet) { ?>
            <div class="about-bullet">
              <div class="about-bullet-check">✓</div>
              <span data-admc-manage="panel_about_bullets" data-admc-id="<?= $bullet['id'] ?>"><?= $bullet['input_text'] ?></span>
            </div>
          <?php } ?>
        </div>

        <div style="display:flex;gap:14px;flex-wrap:wrap;">
          <a href="<?= $about['input_cta_primary_url'] ?>" class="btn btn-primary"
             data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>">
            <?= $about['input_cta_primary_text'] ?>
          </a>
          <a href="<?= $about['input_cta_secondary_url'] ?>" class="btn btn-outline" style="color:var(--navy);border-color:var(--navy);"
             data-admc-manage="settings_home_about" data-admc-id="<?= $about['id'] ?>">
            <?= $about['input_cta_secondary_text'] ?>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>
