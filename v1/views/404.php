<?php
$page_title = "Page Not Found";
$bodyClass  = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — Page Not Found | <?= htmlspecialchars($shop_name ?? 'Venora', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/venora.css">
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body>
  <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px 24px;background:#f6f6f6;">
    <div>
      <div style="margin-bottom:24px;">
        <img src="/assets/img/icons/404.svg" alt="404" style="height:180px;opacity:0.6;">
      </div>
      <h1 style="font-family:'Instrument Serif',serif;font-size:clamp(2.5rem,6vw,4rem);color:#072708;margin-bottom:16px;line-height:1.1;">
        Page Not Found
      </h1>
      <p style="font-size:16px;color:#5c5f6a;max-width:480px;margin:0 auto 32px;line-height:1.7;">
        The page you're looking for doesn't exist or has been moved. Let's get you back on track.
      </p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="/" style="display:inline-flex;align-items:center;padding:13px 28px;background:#072708;color:white;border-radius:4px;font-weight:600;font-size:14px;text-decoration:none;">
          Go Home
        </a>
        <a href="/products" style="display:inline-flex;align-items:center;padding:13px 28px;border:1.5px solid #dedede;border-radius:4px;font-weight:500;font-size:14px;text-decoration:none;color:#072708;">
          Shop Products
        </a>
      </div>
    </div>
  </div>
</body>
</html>
