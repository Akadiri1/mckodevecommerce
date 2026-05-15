<?php
$request_uri_path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Find project base path: find common prefix between REQUEST_URI and SCRIPT_NAME
$script_name = $_SERVER['SCRIPT_NAME']; // e.g. /mckodevecommerce/www/index.php
$script_dir  = rtrim(str_replace('\\', '/', dirname($script_name)), '/'); // e.g. /mckodevecommerce/www

// If the URI starts with the full script directory, strip it
if ($script_dir !== '' && strpos($request_uri_path, $script_dir) === 0) {
    $request_uri_path = substr($request_uri_path, strlen($script_dir));
} else {
    // If not, maybe we are being redirected from a root folder (stripping just the parent)
    $parent_dir = rtrim(str_replace('\\', '/', dirname(dirname($script_name))), '/');
    if ($parent_dir !== '' && strpos($request_uri_path, $parent_dir) === 0) {
        $request_uri_path = substr($request_uri_path, strlen($parent_dir));
    }
}

// Ensure the path always starts with a slash
$request_uri_path = '/' . ltrim($request_uri_path, '/');

// Split into segments
$uri = explode("/", $request_uri_path);
// Indices: [0] is empty, [1] is first segment...

$s1 = $uri[1] ?? "";
$s2 = $uri[2] ?? "";

// Handle routes with sub-segments first (e.g. /products/abc)
if (!empty($s1) && !empty($s2)) {
    if ($s1 === "products") {
        include APP_PATH . "/views/product_detail.php";
        $is404 = false; die;
    }
    if ($s1 === "orders") {
        include APP_PATH . "/views/order_confirm.php";
        $is404 = false; die;
    }
}

// Handle single segment routes
switch ($s1) {
    case "": case "home":
        include APP_PATH . "/views/home.php"; $is404 = false; die;
    case "products":
        include APP_PATH . "/views/products.php"; $is404 = false; die;
    case "wishlist":
        include APP_PATH . "/views/wishlist.php"; $is404 = false; die;
    case "cart":
        include APP_PATH . "/views/cart.php"; $is404 = false; die;
    case "checkout":
        include APP_PATH . "/views/checkout.php"; $is404 = false; die;
    case "about":
        include APP_PATH . "/views/about.php"; $is404 = false; die;
    case "privacy-policy":
        include APP_PATH . "/views/privacy.php"; $is404 = false; die;
    case "contact":
        include APP_PATH . "/views/contact.php"; $is404 = false; die;
    case "cart-add":
        include APP_PATH . "/views/cart_add_backend.php"; $is404 = false; die;
    case "cart-remove":
        include APP_PATH . "/views/cart_remove_backend.php"; $is404 = false; die;
    case "cart-update":
        include APP_PATH . "/views/cart_update_backend.php"; $is404 = false; die;
    case "cart-get":
        include APP_PATH . "/views/cart_get_backend.php"; $is404 = false; die;
    case "quick-view":
        include APP_PATH . "/views/quick_view_backend.php"; $is404 = false; die;
    case "checkout-process":
        include APP_PATH . "/views/checkout_process_backend.php"; $is404 = false; die;
    case "contact-submit":
        include APP_PATH . "/views/contact_backend.php"; $is404 = false; die;
    case "newsletter-subscribe":
        include APP_PATH . "/views/newsletter_backend.php"; $is404 = false; die;
    case "search":
        include APP_PATH . "/views/search_backend.php"; $is404 = false; die;
    case "products-filter":
        include APP_PATH . "/views/products_filter_backend.php"; $is404 = false; die;
    case "wishlist-toggle":
        include APP_PATH . "/views/wishlist_backend.php"; $is404 = false; die;
    case "review-submit":
        include APP_PATH . "/views/review_submit_backend.php"; $is404 = false; die;
}
?>