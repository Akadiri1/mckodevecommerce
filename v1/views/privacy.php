<?php
$page_title = "Privacy Policy";
$bodyClass  = "page-light-navbar";

function safePrivacyFetch($conn, $table, $where = []) {
    try {
        $conn->query("SELECT 1 FROM `$table` LIMIT 1");
        return selectContent($conn, $table, $where);
    } catch (Exception $e) { return []; }
}

$privacyData = safePrivacyFetch($conn, "settings_shop_privacy", ["visibility" => "show"]);
$privacy = !empty($privacyData) ? $privacyData[0] : [];

include APP_PATH . "/views/includes/header.php";
?>

<div class="page-wrapper" style="background-color: #ffffff !important;">
  <!-- HEADER SPACER -->
  <div style="height: 120px; background-color: #ffffff !important;"></div>

  <section class="section-120-120" style="background-color: #ffffff;">
    <div class="container">
      <div class="content-outer">
        <div style="max-width: 800px; margin: 0 auto; color: var(--black-colour, #000);">
          <h1 class="heading-02" style="color: var(--dark-green-colour, #072708); margin-bottom: 40px; text-align: center;" data-admc-manage="settings_shop_privacy" data-admc-id="<?= $privacy['id'] ?? 1 ?>">
            <?= htmlspecialchars($privacy['input_heading'] ?? 'Privacy Policy', ENT_QUOTES, 'UTF-8') ?>
          </h1>
          
          <div class="p-01 rich-text" style="color: var(--gray, #5c5f6a);" data-admc-manage="settings_shop_privacy" data-admc-id="<?= $privacy['id'] ?? 1 ?>">
            <?= $privacy['text_content'] ?? '' ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include APP_PATH . "/views/includes/footer.php"; ?>