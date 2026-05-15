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
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$baseUrl = (strpos($request_uri, $script_dir) === 0) ? $script_dir : $parent_dir;

include D_PATH . "/.env/config.php";
require APP_PATH . "/models/model.php";
require APP_PATH . "/controllers/controller.php";

setcookie("admc", "mckodevecommerce", time() + 31536000, "/", "", false, false);

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
$metaImage        = $logo_directory;

$site_email_from             = $shopConfig[0]["input_email_from"]             ?? "";
$site_email_smtp_host        = $shopConfig[0]["input_email_smtp_host"]        ?? "smtp.gmail.com";
$site_email_smtp_secure_type = $shopConfig[0]["input_email_smtp_secure_type"] ?? "tls";
$site_email_smtp_port        = $shopConfig[0]["input_email_smtp_port"]        ?? "587";
$site_email_password         = $shopConfig[0]["input_email_password"]         ?? "";

$sessionId = session_id();
$cartItems = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);
$cartCount = (int)array_sum(array_column($cartItems, "input_quantity"));

$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$wishlistIds   = $_SESSION['wishlist'] ?? [];

include APP_PATH . "/routes/router.php";
include APP_PATH . "/ajax/ajax_router/router.php";
include APP_PATH . "/auth/auth_router/router.php";
include APP_PATH . "/admc_ext/ext_route/router.php";

if ($is404 === true) {
    include APP_PATH . "/views/404.php";
}