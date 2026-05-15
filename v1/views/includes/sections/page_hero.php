<?php
/**
 * Small page hero for sub-pages.
 * Expects $page_hero_title and $page_hero_subtitle to be set before inclusion.
 */
?>
<section class="page-hero">
  <div class="container">
    <h1 class="page-hero-title"><?= $page_hero_title ?? '' ?></h1>
    <?php if (!empty($page_hero_subtitle)) { ?>
      <p class="page-hero-subtitle"><?= $page_hero_subtitle ?></p>
    <?php } ?>
  </div>
</section>
