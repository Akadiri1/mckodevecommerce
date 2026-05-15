<?php
$page_title = "About";
$bodyClass  = "";

function safeAboutFetch($conn, $table, $where = []) {
    try {
        $conn->query("SELECT 1 FROM `$table` LIMIT 1");
        return selectContent($conn, $table, $where);
    } catch (Exception $e) { return []; }
}

$aboutHeroContent = safeAboutFetch($conn, "settings_shop_about_hero", ["visibility" => "show"]);
$aboutHero = !empty($aboutHeroContent) ? $aboutHeroContent[0] : [];

$aboutStoryContent = safeAboutFetch($conn, "settings_shop_about", ["visibility" => "show"]);
$aboutStory = !empty($aboutStoryContent) ? $aboutStoryContent[0] : [];

$aboutValues = selectContentAsc($conn, "panel_about_values", ["visibility" => "show"], "input_order", 10);
$aboutTeam   = selectContentAsc($conn, "panel_about_team", ["visibility" => "show"], "input_order", 10);
$aboutFaqs   = selectContentAsc($conn, "panel_about_faqs", ["visibility" => "show"], "input_order", 20);

include APP_PATH . "/views/includes/header.php";
?>

<!-- [cbcode_20001o] -->

<!-- ABOUT HERO -->
<div data-cbsection="cb1">
  <!-- [cbcode_20001Heroo] -->
  <section class="about-hero" data-w-id="29aa9955-28b7-3f33-84f0-dfd0b6c1b7e0">
    <div class="container home">
      <div class="about-hero-inner">
        <div class="about-hero-title" style="opacity:0;transform:translate3d(0, 40px, 0);" data-admc-manage="settings_shop_about_hero" data-admc-id="<?= $aboutHero['id'] ?? 1 ?>">
          <h1 class="heading-01">
            <?= htmlspecialchars($aboutHero['input_heading'] ?? "Luxury skincare, inspired by your beauty", ENT_QUOTES, 'UTF-8') ?>
          </h1>
        </div>
      </div>
    </div>
    <div class="home-hero-img" data-admc-image="settings_shop_about_hero" data-admc-id="<?= $aboutHero['id'] ?? 1 ?>">
      <img alt="About Venora" class="all-img" src="<?= htmlspecialchars($aboutHero['image_1'] ?? "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd6ef0cfc88213b0455_Serene Nature Portrait 1.avif", ENT_QUOTES, 'UTF-8') ?>">
    </div>
  </section>
  <!-- [cbcode_20001Heroc] -->
</div>

<!-- JOURNEY & MISSION -->
<div data-cbsection="cb2">
  <!-- [cbcode_20001Storyo] -->
  <section class="content section-120-120">
    <div class="container">
      <div class="content-outer">
        <div class="content-inner">
          <div class="content-img-box" data-admc-image="settings_shop_about" data-admc-id="<?= $aboutStory['id'] ?? 1 ?>">
            <img alt="Our Journey" class="images speed" src="<?= htmlspecialchars($aboutStory['image_1'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd7b40efb5491fb2f21_Rectangle 1116.avif', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="content-text" data-admc-manage="settings_shop_about" data-admc-id="<?= $aboutStory['id'] ?? 1 ?>">
            <div class="content-text-inner" style="opacity:0;transform:translate3d(0, 40px, 0);">
              <h2 class="heading-02"><?= htmlspecialchars($aboutStory['input_heading_1'] ?? "Our journey", ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray">
                <div class="p-01"><?= htmlspecialchars($aboutStory['text_content_1'] ?? "VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin.", ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="content-inner _02">
          <div class="content-text" data-admc-manage="settings_shop_about" data-admc-id="<?= $aboutStory['id'] ?? 1 ?>">
            <div class="content-text-inner" style="opacity:0;transform:translate3d(0, 40px, 0);">
              <h2 class="heading-02"><?= htmlspecialchars($aboutStory['input_heading_2'] ?? "Our mission", ENT_QUOTES, 'UTF-8') ?></h2>
              <div class="color-gray">
                <div class="p-01"><?= htmlspecialchars($aboutStory['text_content_2'] ?? "Our mission is to create premium skincare products that nourish, protect, and rejuvenate your skin.", ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>
          </div>
          <div class="content-img-box" data-admc-image="settings_shop_about" data-admc-id="<?= $aboutStory['id'] ?? 1 ?>">
            <img alt="Our Mission" class="images speed" src="<?= htmlspecialchars($aboutStory['image_2'] ?? 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd622ebe87b20e109bc_Rectangle 1118.avif', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- [cbcode_20001Storyc] -->
</div>

<!-- VALUES TABS -->
<div data-cbsection="cb3">
  <!-- [cbcode_20001Valueso] -->
  <section class="content section-0-120">
    <div class="container">
      <div class="content-inner-02">
        <div class="content-header">
          <h2 class="heading-02">Pure & Trusted</h2>
          <div class="content-header-short">
            <div class="color-gray"><div class="p-01">Our products are crafted with your skin’s health in mind.</div></div>
          </div>
        </div>
        
        <div class="features-tabs">
          <div class="features-tabs-menu">
            <?php foreach ($aboutValues as $vi => $val): ?>
            <div class="features-tabs-link <?= $vi === 0 ? 'active' : '' ?>" onclick="showTab(<?= $vi + 1 ?>)" data-admc-manage="panel_about_values" data-admc-id="<?= $val['id'] ?>">
              <div class="faq-item-inner">
                <div class="faq-header"><div class="heading-04"><?= htmlspecialchars($val['input_title'], ENT_QUOTES, 'UTF-8') ?></div></div>
                <div class="body-wrapper"><div class="p-01"><?= htmlspecialchars($val['text_description'], ENT_QUOTES, 'UTF-8') ?></div></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          
          <div class="features-tabs-content">
            <?php foreach ($aboutValues as $vi => $val): ?>
            <div id="tab-<?= $vi + 1 ?>" class="features-tab-pane <?= $vi === 0 ? 'active' : '' ?>" data-admc-image="panel_about_values" data-admc-id="<?= $val['id'] ?>">
              <div class="features-card">
                <img alt="<?= htmlspecialchars($val['input_title'], ENT_QUOTES, 'UTF-8') ?>" class="all-img zoom" src="<?= htmlspecialchars($val['image_1'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- [cbcode_20001Valuesc] -->
</div>

<!-- FAQ SECTION -->
<div data-cbsection="cb5">
  <!-- [cbcode_20001FAQo] -->
  <section class="faq section-0-120">
    <div class="container">
      <div class="faq-inner">
        <div class="faq-left">
          <h2 class="heading-02">Frequently Asked Questions</h2>
        </div>
        <div class="faq-list">
          <?php foreach ($aboutFaqs as $faq): ?>
          <div class="faq-item" onclick="toggleFAQ(this)" data-admc-manage="panel_about_faqs" data-admc-id="<?= $faq['id'] ?>">
            <div class="faq-header">
              <div class="heading-05"><?= htmlspecialchars($faq['input_question'], ENT_QUOTES, 'UTF-8') ?></div>
              <div class="faq-arrow">+</div>
            </div>
            <div class="faq-body">
              <div class="p-01"><?= htmlspecialchars($faq['text_answer'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <!-- [cbcode_20001FAQc] -->
</div>

<!-- INSTAGRAM GALLERY -->
<div data-cbsection="cb6">
  <!-- [cbcode_20001Galleryo] -->
  <?php
  $galleryPhotos = safeAboutFetch($conn, "panel_gallery_photos", ["visibility" => "show"]);
  if (empty($galleryPhotos)) {
    $galleryPhotos = [
      ["image_1" => "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3a62dfb2e1c46b3d2e_Rectangle 1105.avif", "input_alt" => "Gallery"],
      ["image_1" => "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b7f0a63c03c952cf3_Rectangle 1101.avif", "input_alt" => "Gallery"],
      ["image_1" => "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b8256954751ac9026_Rectangle 1102.avif", "input_alt" => "Gallery"],
      ["image_1" => "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b0e2c8d3d0bd0253c_Rectangle 1103.avif", "input_alt" => "Gallery"],
      ["image_1" => "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b26fef69889644991_Rectangle 1104.avif", "input_alt" => "Gallery"],
    ];
  }
  ?>
  <section class="gallery section-0-120 about-gallery-section">
    <div class="about-gallery-track" data-admc-tb="panel_gallery_photos">
      <?php for ($tw = 0; $tw < 2; $tw++): ?>
        <div class="about-gallery-row" aria-hidden="<?= $tw > 0 ? 'true' : 'false' ?>">
          <?php foreach ($galleryPhotos as $photo): ?>
            <div class="gallery-card"
                 data-admc-image="panel_gallery_photos"
                 data-admc-id="<?= $photo['id'] ?? 0 ?>">
              <img alt="<?= htmlspecialchars($photo['input_alt'] ?? 'Gallery', ENT_QUOTES, 'UTF-8') ?>"
                   class="all-img" loading="lazy"
                   src="<?= htmlspecialchars($photo['image_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endfor; ?>
    </div>
  </section>
  <!-- [cbcode_20001Galleryc] -->
</div>

<!-- [cbcode_20001c] -->

<script>
function toggleFAQ(el) {
  el.classList.toggle('active');
}
function showTab(n) {
  document.querySelectorAll('.features-tab-pane').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.features-tabs-link').forEach(l => l.classList.remove('active'));
  document.getElementById('tab-'+n).classList.add('active');
  event.currentTarget.classList.add('active');
}

// Animation observer
const observerOptions = { threshold: 0.1 };
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translate3d(0, 0, 0)';
      entry.target.style.transition = 'all 0.8s ease-out';
    }
  });
}, observerOptions);

document.querySelectorAll('[style*="opacity:0"]').forEach(el => observer.observe(el));
</script>

<style>
.faq-list { flex: 1; display: flex; flex-direction: column; gap: 20px; }
.faq-item { border-bottom: 1px solid #eee; padding: 20px 0; cursor: pointer; }
.faq-header { display: flex; justify-content: space-between; align-items: center; }
.faq-body { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
.faq-item.active .faq-body { max-height: 200px; margin-top: 15px; }
.faq-item.active .faq-arrow { transform: rotate(45deg); }
.faq-arrow { font-size: 24px; transition: transform 0.3s; }
.features-tabs { display: flex; gap: 40px; margin-top: 60px; }
.features-tabs-menu { flex: 1; display: flex; flex-direction: column; gap: 20px; }
.features-tabs-content { flex: 1; position: relative; min-height: 500px; }
.features-tab-pane { position: absolute; top: 0; left: 0; width: 100%; opacity: 0; transition: opacity 0.3s; pointer-events: none; }
.features-tab-pane.active { opacity: 1; pointer-events: auto; position: relative; }
.features-tabs-link { cursor: pointer; opacity: 0.6; transition: opacity 0.3s; }
.features-tabs-link.active { opacity: 1; }
.features-tabs-link .body-wrapper { display: none; margin-top: 10px; }
.features-tabs-link.active .body-wrapper { display: block; }
@media (max-width: 991px) {
  .features-tabs { flex-direction: column-reverse; }
}
</style>

<?php include APP_PATH . "/views/includes/footer.php"; ?>