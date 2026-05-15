<?php
$trustItems = selectContentAsc($conn, "panel_trust_items", ["visibility" => "show"], "input_order", 20);
?>
<div id="trust">
  <div class="container">
    <div class="trust-items" data-admc-tb="panel_trust_items">
      <?php foreach ($trustItems as $item) { ?>
        <div class="trust-item">
          <span class="trust-icon" data-admc-manage="panel_trust_items" data-admc-id="<?= $item['id'] ?>"><i class="<?= $item['input_icon'] ?>"></i></span>
          <span class="trust-text" data-admc-manage="panel_trust_items" data-admc-id="<?= $item['id'] ?>"><?= $item['input_text'] ?></span>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
