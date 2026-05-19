<?php
ob_start();
session_start();

$is404 = true;

define("D_PATH", dirname(dirname(__FILE__)));
const APP_PATH = D_PATH . "/v1";

$script_name = $_SERVER['SCRIPT_NAME'];
$script_dir  = rtrim(str_replace('\\', '/', dirname($script_name)), '/');
$parent_dir  = rtrim(str_replace('\\', '/', dirname(dirname($script_name))), '/');

// Use the parent dir if we are being rewritten from it (common for /www/ folders)
// Guard against empty $script_dir when site runs at domain root (avoids strpos empty needle warning)
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if ($script_dir === '') {
    $baseUrl = '';
} else {
    $baseUrl = (strpos($request_uri, $script_dir) === 0) ? $script_dir : $parent_dir;
}

include D_PATH . "/.env/config.php";
require APP_PATH . "/models/model.php";
require APP_PATH . "/controllers/controller.php";
require APP_PATH . "/controllers/ProductController.php";
require APP_PATH . "/controllers/CouponManager.php";
require APP_PATH . "/controllers/PaystackClient.php";
require APP_PATH . "/controllers/cart_functions.php";
require APP_PATH . "/controllers/mailer_helper.php";

if (getenv("ADMC_USERNAME")) {
  $admc_username = getenv("ADMC_USERNAME");
  setcookie("admc", "", time() - 3600, "/", null, false, false);
  setcookie("admc", "", time() - 3600, null, null, false, false);
  setcookie("admc", $admc_username, time() + 31536000, "/", null, false, false);
}

// ADMC header whitelist — must be set before any AJAX file is included
$allowedHeadersArr = selectContent($conn, "panel_allowed_headers", ["visibility" => "show"]);
$headersName       = array_column($allowedHeadersArr, "input_name");

$shopConfig   = selectContent($conn, "settings_shop_config", ["visibility" => "show"]);
$websiteStyle = selectContent($conn, "website_status",       ["visibility" => "show"]);
$fetchFavicon = selectContent($conn, "read_favicon",         ["visibility" => "show"]);

$shop_name        = $shopConfig[0]["input_name"]             ?? "Venora";
$shop_tagline     = $shopConfig[0]["input_tagline"]          ?? "Luxury Skincare";
$shop_email       = $shopConfig[0]["input_email"]            ?? "";
$shop_phone       = $shopConfig[0]["input_phone"]            ?? "";
$shop_address     = $shopConfig[0]["input_address"]          ?? "";
$shop_currency    = $shopConfig[0]["input_currency"]         ?? "USD";
$shop_symbol      = $shopConfig[0]["input_currency_symbol"]  ?? "$";
$shop_tax_rate    = (float)($shopConfig[0]["input_tax_rate"]      ?? 0);
$shop_ship_rate   = (float)($shopConfig[0]["input_shipping_rate"] ?? 0);
$shop_free_ship   = (float)($shopConfig[0]["input_free_shipping"] ?? 0);
$logo_directory   = $shopConfig[0]["image_1"]                ?? "/assets/img/brand/venora-white.svg";
$logo_dark        = $shopConfig[0]["image_2"]                ?? "/assets/img/brand/venora-dark.svg";
$metaDescription  = $shopConfig[0]["text_description"]       ?? "";
$metakeys         = $shopConfig[0]["input_seo_keywords"]     ?? "";
$_seoProtocol     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$metaImage        = $_seoProtocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/screenshot.png';

$site_email_from             = $shopConfig[0]["input_email_from"]             ?? "";
$site_email_smtp_host        = $shopConfig[0]["input_email_smtp_host"]        ?? "smtp.gmail.com";
$site_email_smtp_secure_type = $shopConfig[0]["input_email_smtp_secure_type"] ?? "tls";
$site_email_smtp_port        = $shopConfig[0]["input_email_smtp_port"]        ?? "587";
$site_email_password         = $shopConfig[0]["input_email_password"]         ?? "";

$sessionId = session_id();

// User identity for cart/coupon functions (MD5 of session id for guests)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = md5($sessionId);
}
$userId = $_SESSION['user_id'];

$cartCount = getCartCount($userId);

// ── Currency mode (driven by settings_website_info.input_usd_toggle) ──────────
// input_usd_toggle = 1 → show USD prices with $ symbol
// input_usd_toggle = 0 → show NGN prices with ₦ symbol
$websiteInfoRow = selectContent($conn, "settings_website_info", []);
$usdEnabled     = isset($websiteInfoRow[0]['input_usd_toggle']) ? (int)$websiteInfoRow[0]['input_usd_toggle'] === 1 : false;

// Override the shop symbol based on active currency
// Admin sets input_currency_symbol in settings_shop_config for USD symbol
$shop_symbol   = $usdEnabled
    ? ($shopConfig[0]["input_currency_symbol"] ?? '$')
    : '₦';
$shop_currency = $usdEnabled ? 'USD' : 'NGN';

$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$wishlistIds   = $_SESSION['wishlist'] ?? [];

// Customer session
$customerId         = $_SESSION['customer_id']   ?? null;
$customerHash       = $_SESSION['customer_hash'] ?? null;
$customerName       = $_SESSION['customer_name'] ?? null;
$isCustomerLoggedIn = !empty($customerId);

include APP_PATH . "/routes/router.php";
include APP_PATH . "/ajax/ajax_router/router.php";
include APP_PATH . "/auth/auth_router/router.php";
include APP_PATH . "/admc_ext/ext_route/router.php";

if ($is404 === true) {
    include APP_PATH . "/views/404.php";
}