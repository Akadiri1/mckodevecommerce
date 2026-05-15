<?php
$testimonialsHeader = selectContent($conn, "settings_home_testimonials", ["visibility" => "show"])[0];
$testimonials = selectContentAsc($conn, "panel_testimonials", ["visibility" => "show"], "input_order", 20);
?>
<?php if (count($testimonials) > 0) { ?>
<section id="testimonials" class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label" data-admc-manage="settings_home_testimonials" data-admc-id="<?= $testimonialsHeader['id'] ?>"><?= $testimonialsHeader['input_label'] ?></span>
      <h2 class="section-title" data-admc-manage="settings_home_testimonials" data-admc-id="<?= $testimonialsHeader['id'] ?>"><?= $testimonialsHeader['input_title'] ?></h2>
      <p class="section-subtitle" data-admc-manage="settings_home_testimonials" data-admc-id="<?= $testimonialsHeader['id'] ?>"><?= $testimonialsHeader['text_subtitle'] ?></p>
    </div>

    <div class="reviews-grid" data-admc-tb="panel_testimonials">
      <?php foreach ($testimonials as $review) { ?>
        <div class="review-card">
          <div class="review-stars">
            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
          </div>
          <p class="review-text" data-admc-manage="panel_testimonials" data-admc-id="<?= $review['id'] ?>">
            "<?= $review['text_review'] ?>"
          </p>
          <div class="review-author">
            <?php if (!empty($review['image_1'])) { ?>
              <div data-admc-image="panel_testimonials" data-admc-id="<?= $review['id'] ?>">
                <img src="<?= $review['image_1'] ?>" alt="<?= $review['input_author_name'] ?>" class="review-avatar" style="object-fit:cover;" />
              </div>
            <?php } else { ?>
              <div class="review-avatar" style="background:<?= $review['bgcolor_avatar'] ?>;"
                   data-admc-manage="panel_testimonials" data-admc-id="<?= $review['id'] ?>">
                <?= $review['input_author_initials'] ?>
              </div>
            <?php } ?>
            <div>
              <div class="review-name" data-admc-manage="panel_testimonials" data-admc-id="<?= $review['id'] ?>"><?= $review['input_author_name'] ?></div>
              <div class="review-role" data-admc-manage="panel_testimonials" data-admc-id="<?= $review['id'] ?>"><?= $review['input_author_role'] ?></div>
            </div>
          </div>
          <div class="review-source" data-admc-manage="panel_testimonials" data-admc-id="<?= $review['id'] ?>"><i class="fa-solid fa-star"></i> <?= $review['input_source'] ?></div>
        </div>
      <?php } ?>
    </div>

    <div class="reviews-cta">
      <div class="google-rating">
        <div class="google-g">G</div>
        <div>
          <div style="display:flex;align-items:center;gap:6px;">
            <span class="google-score" data-admc-manage="settings_home_testimonials" data-admc-id="<?= $testimonialsHeader['id'] ?>"><?= $testimonialsHeader['input_google_score'] ?></span>
            <span style="color:#f59e0b;font-size:0.9rem;">★★★★★</span>
          </div>
          <div class="google-text" data-admc-manage="settings_home_testimonials" data-admc-id="<?= $testimonialsHeader['id'] ?>"><?= $testimonialsHeader['input_google_text'] ?></div>
        </div>
      </div>
    </div>

  </div>
</section>
<?php } else { ?>
<section class="section" style="text-align:center;">
  <div class="container">
    <div class="section-header center">
      <span class="section-label">Reviews</span>
      <h2 class="section-title">Real Reviews Coming Soon</h2>
      <p class="section-subtitle" style="margin: 0 auto;">We are a new service and building our reputation one client at a time. Check back soon for reviews from real Edinburgh customers — or be our first!</p>
    </div>
    <a href="/contact" class="btn btn-primary btn-lg"><i class="fa-solid fa-clipboard-list"></i> Book Your First Clean</a>
  </div>
</section>
<?php } ?>
