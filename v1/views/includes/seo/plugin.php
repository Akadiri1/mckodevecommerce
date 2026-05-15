<?php

$websiteTitle = $site_name . (($pageTitle ?? $page_title ?? false) ? " - " . ($pageTitle ?? $page_title) : "");
// var_dump($websiteTitle, $pageTitle); die;
// var_dump($metaTitle); die;

$metaImage = (stringStartWith($metaImage, "http")) ? $metaImage : "https://" . $_SERVER['HTTP_HOST'] . $metaImage;
$current_uri = "https://" . $_SERVER['HTTP_HOST'];


if (!isset($ogMetaImage)) {
  $ogMetaImage = $metaImage;
}

if (!isset($twitterMetaImage)) {
  $twitterMetaImage = $metaImage;
}

$metaDescription = previewBodyWithElipsces($metaDescription, 50, true);

if (isset($siteKeywords)) {
  if (is_array($siteKeywords)) {
    $siteKeywords = implode(", ", $siteKeywords);
  }
} else {
  $siteKeywords = "";
}

?>

<title><?= $websiteTitle ?></title>
<link rel="icon" href="<?= $site_icon ?>">
<meta property="og:locale" content="en_GB" />


<!-- SEO -->
<meta name="title" content="<?= $metaTitle ?? $websiteTitle ?>">
<meta name="description" content="<?= $metaDescription ?>">
<meta name="keywords" content="<?= $siteKeywords ?>">
<meta name="author" content="<?= $site_name ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?= $current_uri ?>">
<meta property="og:title" content="<?= $metaTitle ?? $websiteTitle ?>">
<meta property="og:type" content="article">
<meta property="og:description" content="<?= $metaDescription ?>">
<meta property="og:image" content="<?= $ogMetaImage ?>">
<meta property="og:image:width" content="450">
<meta property="og:image:height" content="300">

<!-- Twitter -->
<meta name="twitter:site" content="@mckodev">
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?= $current_uri ?>">
<meta property="twitter:title" content="<?= $metaTitle ?? $websiteTitle ?>">
<meta property="twitter:description" content="<?= $metaDescription ?>">
<meta property="twitter:image" content="<?= $twitterMetaImage ?>">
<meta name="twitter:image:width" content="280">
<meta name="twitter:image:height" content="150">



<!-- Meta Tags -->
<meta name="theme-color" content="<?= $style_color ?? "" ?>">
<meta name="application-name" content="<?= $site_name ?>">


<link rel="shortcut icon" href="<?= $site_icon ?>">

<link rel="icon" type="image/png" sizes="32x32" href="<?= $site_icon ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= $site_icon ?>">


<!-- Apple -->
<meta name="apple-mobile-web-app-title" content="<?= $metaTitle ?? $websiteTitle ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= $site_icon ?>">
<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="<?= $style_color ?? "" ?>">

<!-- Microsoft -->
<meta name="msapplication-TileColor" content="<?= $style_color ?? "" ?>">



<script async src="https://www.googletagmanager.com/gtag/js?id=G-NEWFXZKMXD"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  gtag('set', 'user_properties', {
    'domain': window.location.hostname
  });
  gtag('config', 'G-NEWFXZKMXD');
</script>



<?php ADMCGoogleAnalytics(); ?>
<?php MCK_Clarity(); ?>