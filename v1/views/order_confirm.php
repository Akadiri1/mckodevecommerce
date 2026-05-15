<?php
$hash = $uri[2] ?? '';
$orderArr = selectContent($conn, "read_orders", ["hash_id" => $hash]);
if (empty($orderArr)) { include APP_PATH . "/views/404.php"; die; }
$order = $orderArr[0];

$orderItems = selectContent($conn, "read_order_items", ["tb_link" => $hash]);
$page_title = "Order Confirmed";
$bodyClass  = "";
$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");

include APP_PATH . "/views/includes/header.php";
?>

<div data-cbsection="cb1">
<?php/*##cb1o##*/>

<?php/*##cbcode_90001o##*?>
<div data-cbcodesection="cbcode_90001">
<section style="padding:80px 0 120px;background:#f6f6f6;min-height:70vh;">
  <div class="container" style="max-width:680px;">
    <div style="text-align:center;margin-bottom:40px;">
      <div style="width:72px;height:72px;background:#072708;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:white;">✓</div>
      <h1 class="heading-02" style="margin-bottom:12px;">Order Confirmed!</h1>
      <p class="p-01 color-gray">
        Thank you, <?= htmlspecialchars($order["input_first_name"] ?? "Customer", ENT_QUOTES, "UTF-8") ?>!
        Your order <strong>#<?= htmlspecialchars($order["hash_id"], ENT_QUOTES, "UTF-8") ?></strong> has been placed.
      </p>
      <p class="p-02 color-gray" style="margin-top:8px;">
        A confirmation email has been sent to <?= htmlspecialchars($order["input_email"] ?? "", ENT_QUOTES, "UTF-8") ?>
      </p>
    </div>

    <div style="background:white;border-radius:8px;padding:28px;margin-bottom:20px;">
      <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5c5f6a;margin-bottom:20px;">Order Details</div>
      <?php foreach ($orderItems as $item): ?>
        <div class="order-item" style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f0f0f0;">
          <img src="<?= htmlspecialchars($item["image_1"] ?? "/assets/img/icons/cart.svg", ENT_QUOTES, "UTF-8") ?>"
               class="order-item-img" alt="<?= htmlspecialchars($item["input_title"] ?? "", ENT_QUOTES, "UTF-8") ?>">
          <div class="order-item-info">
            <div class="order-item-name"><?= htmlspecialchars($item["input_title"] ?? "", ENT_QUOTES, "UTF-8") ?></div>
            <?php if (!empty($item["input_variant"])): ?>
              <div class="order-item-variant"><?= htmlspecialchars($item["input_variant"], ENT_QUOTES, "UTF-8") ?></div>
            <?php endif; ?>
            <div class="order-item-qty">Qty: <?= (int)$item["input_quantity"] ?></div>
          </div>
          <div class="order-item-price"><?= $sym ?><?= number_format((float)$item["input_total"], 2) ?></div>
        </div>
      <?php endforeach; ?>
      <div class="order-row" style="margin-top:16px;">
        <span class="order-row-label">Subtotal</span>
        <span class="order-row-value"><?= $sym ?><?= number_format((float)$order["input_subtotal"], 2) ?></span>
      </div>
      <div class="order-row">
        <span class="order-row-label">Shipping</span>
        <span class="order-row-value"><?= $sym ?><?= number_format((float)$order["input_shipping"], 2) ?></span>
      </div>
      <div class="order-total-row">
        <span class="order-total-label">Total</span>
        <span class="order-total-value"><?= $sym ?><?= number_format((float)$order["input_total"], 2) ?></span>
      </div>
    </div>

    <div style="background:white;border-radius:8px;padding:28px;margin-bottom:28px;">
      <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5c5f6a;margin-bottom:16px;">Shipping To</div>
      <div class="p-02">
        <?= htmlspecialchars($order["input_first_name"] ?? "", ENT_QUOTES, "UTF-8") ?>
        <?= htmlspecialchars($order["input_last_name"] ?? "", ENT_QUOTES, "UTF-8") ?><br>
        <?= nl2br(htmlspecialchars($order["text_address"] ?? "", ENT_QUOTES, "UTF-8")) ?>
      </div>
    </div>

    <div style="text-align:center;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a class="btn-02-link w-inline-block" href="/products" style="display:inline-flex;">
        <div class="btn-inner"><div class="btn-text-wrap">
          <div class="btn-text-3 _01"><div class="cta-text">Continue Shopping</div></div>
          <div class="btn-text-3 _02"><div class="cta-text">Continue Shopping</div></div>
        </div></div>
      </a>
      <a href="/contact" style="display:inline-flex;align-items:center;padding:12px 24px;border:1.5px solid #dedede;border-radius:4px;font-size:14px;font-weight:500;color:#072708;text-decoration:none;">
        Need Help?
      </a>
    </div>
  </div>
</section>
</div>
<?php/*##cbcode_90001c##*/>

<?php/*##cb1c##*/>
</div>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
