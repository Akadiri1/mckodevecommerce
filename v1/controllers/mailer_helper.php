<?php
/**
 * Sends a new-product notification email to all active newsletter subscribers.
 * Called from ajax/add.php when a panel_product row is inserted.
 */
function notifySubscribersNewProduct(PDO $conn, array $productData): void
{
    $subscribers = selectContent($conn, "read_newsletter", ["visibility" => "show"]);
    if (empty($subscribers)) return;

    $siteInfo = selectContent($conn, "settings_website_info", ["visibility" => "show"]);
    $site     = !empty($siteInfo) ? $siteInfo[0] : [];

    $smtpHost   = $site['input_email_smtp_host']        ?? 'smtp.gmail.com';
    $smtpPort   = (int)($site['input_email_smtp_port']  ?? 587);
    $smtpSecure = $site['input_email_smtp_secure_type'] ?? 'tls';
    $smtpUser   = $site['input_email_from']             ?? '';
    $smtpPass   = $site['input_email_password']         ?? '';
    $fromName   = $site['input_name']                   ?? 'Venora';
    $fromEmail  = $site['input_email_from']             ?? 'hello@venora.com';

    if (empty($smtpPass)) return; // SMTP not configured yet

    $productName  = htmlspecialchars($productData['input_product_name'] ?? 'New Product', ENT_QUOTES, 'UTF-8');
    $productDesc  = htmlspecialchars(substr($productData['text_description'] ?? '', 0, 180), ENT_QUOTES, 'UTF-8');
    $productImage = $productData['image_2'] ?? '';
    $productHash  = $productData['hash_id'] ?? '';
    $siteUrl      = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'venora.com');
    $productUrl   = $siteUrl . '/products/' . $productHash;

    try {
        require_once APP_PATH . '/phpm/PHPMailerAutoload.php';

        foreach ($subscribers as $sub) {
            $toEmail = $sub['input_email'] ?? '';
            if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) continue;

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port       = $smtpPort;
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = '✨ New Arrival: ' . $productName . ' — ' . $fromName;
            $mail->Body    = '
<!DOCTYPE html><html><body style="font-family:sans-serif;background:#f6f6f6;margin:0;padding:20px;">
<div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;">
  <div style="background:#072708;padding:32px;text-align:center;">
    <h1 style="color:#ffffff;margin:0;font-size:28px;letter-spacing:4px;">VENORA</h1>
  </div>
  <div style="padding:32px;">
    <p style="color:#5c5f6a;font-size:13px;text-transform:uppercase;letter-spacing:2px;margin-top:0;">New Arrival</p>
    <h2 style="color:#072708;margin-top:4px;">' . $productName . '</h2>'
    . (!empty($productImage) ? '<img src="' . $siteUrl . $productImage . '" alt="' . $productName . '" style="width:100%;max-height:300px;object-fit:cover;border-radius:4px;margin-bottom:16px;">' : '')
    . '<p style="color:#5c5f6a;line-height:1.7;">' . $productDesc . '...</p>
    <a href="' . $productUrl . '"
       style="display:inline-block;margin-top:16px;padding:14px 28px;background:#072708;color:#ffffff;text-decoration:none;border-radius:4px;font-size:14px;">
      Shop Now
    </a>
  </div>
  <div style="padding:16px 32px;border-top:1px solid #eee;text-align:center;">
    <p style="color:#b5b5b5;font-size:12px;margin:0;">&copy; ' . date('Y') . ' Venora. All Rights Reserved.</p>
  </div>
</div>
</body></html>';
            try { $mail->send(); } catch (Exception $e) {
                error_log("Product notify failed for {$toEmail}: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        error_log("Product notification mailer setup failed: " . $e->getMessage());
    }
}
