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

// ── Paystack webhook (two-segment: /paystack/webhook) ────────────────────────
if (!empty($s1) && !empty($s2) && $s1 === 'paystack' && $s2 === 'webhook') {
    header('Content-Type: application/json');
    $input = @file_get_contents("php://input");
    if (!$input) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No request body received.']);
        exit;
    }
    $signature         = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
    $paystackSecretKey = getenv('PAYSTACK_SECRET_KEY') ?: '';
    $expected          = hash_hmac('sha512', $input, $paystackSecretKey);
    if (empty($signature) || !hash_equals($expected, $signature)) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Signature verification failed.']);
        exit;
    }
    $data = json_decode($input, true);
    handlePaystackWebhook($data);
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    $is404 = false;
    exit;
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
    case "payment-invoice":
        include APP_PATH . "/views/payment_invoice.php"; $is404 = false; die;
    case "purchases":
        include APP_PATH . "/views/purchases.php"; $is404 = false; die;
    case "verify-paystack":
        include APP_PATH . "/views/verify_paystack_backend.php"; $is404 = false; die;
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
    case "fetch-state-backend":
        include APP_PATH . "/views/fetch_state_backend.php"; $is404 = false; die;
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

    // ── ProductController API endpoints ──────────────────────────
    case "products-api":
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');
            $limit    = 20;
            $page     = isset($_GET['page'])     ? (int) $_GET['page']     : 1;
            $offset   = ($page - 1) * $limit;
            $search   = isset($_GET['search'])   ? trim($_GET['search'])   : null;
            $category = isset($_GET['category_id']) ? trim($_GET['category_id']) : null;
            $minPrice = isset($_GET['min_price']) ? (float) $_GET['min_price'] : null;
            $maxPrice = isset($_GET['max_price']) ? (float) $_GET['max_price'] : null;
            $attrFilters = isset($_GET['attribute_filters']) ? $_GET['attribute_filters'] : [];

            $websiteInfo = selectContent($conn, "settings_website_info", []);
            $usdToggle   = isset($websiteInfo[0]['input_usd_toggle']) ? (int) $websiteInfo[0]['input_usd_toggle'] : 0;

            $controller  = new ProductController($conn, $usdToggle === 1);
            $products    = $controller->fetchProducts([
                'offset'            => $offset,
                'limit'             => $limit,
                'search'            => $search,
                'category_id'       => $category,
                'attribute_filters' => $attrFilters,
                'min_price'         => $minPrice,
                'max_price'         => $maxPrice,
            ]);
            $totalCount  = $controller->fetchProductCount([
                'search'      => $search,
                'category_id' => $category,
            ]);

            echo json_encode([
                'success'      => true,
                'data'         => $products,
                'page'         => $page,
                'total_count'  => $totalCount,
                'total_pages'  => ceil($totalCount / $limit),
                'next_page'    => $page * $limit < $totalCount ? $page + 1 : null,
                'prev_page'    => $page > 1 ? $page - 1 : null,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
        $is404 = false; exit;

    case "fetch-product-details":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data   = json_decode(file_get_contents('php://input'), true);
            $hashId = $data['hashId'] ?? null;
            if (!$hashId) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                exit;
            }
            $websiteInfo = selectContent($conn, "settings_website_info", []);
            $usdToggle   = isset($websiteInfo[0]['input_usd_toggle']) ? (int) $websiteInfo[0]['input_usd_toggle'] : 0;
            $controller  = new ProductController($conn, $usdToggle === 1);
            $details     = $controller->fetchProductDetailsByHashId($hashId);
            if ($details) {
                echo json_encode(['status' => 'success', 'data' => $details]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
            }
        }
        $is404 = false; exit;

    case "fetch-variant":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data     = json_decode(file_get_contents('php://input'), true);
            $hashId   = $data['product_hash_id']     ?? null;
            $valueIds = $data['selected_value_ids']  ?? [];
            if (!$hashId || empty($valueIds)) {
                echo json_encode(['error' => 'Invalid request']);
                exit;
            }
            $websiteInfo = selectContent($conn, "settings_website_info", []);
            $usdToggle   = isset($websiteInfo[0]['input_usd_toggle']) ? (int) $websiteInfo[0]['input_usd_toggle'] : 0;
            $controller  = new ProductController($conn, $usdToggle === 1);
            echo json_encode($controller->fetchSpecificVariant($hashId, $valueIds));
        }
        $is404 = false; exit;

    case "fetch-master-attributes":
        header('Content-Type: application/json');
        $controller = new ProductController($conn);
        echo json_encode(['status' => 'success', 'data' => $controller->fetchMasterAttributes()]);
        $is404 = false; exit;

    case "fetch-shipping-locations":
        header('Content-Type: application/json');
        $controller = new ProductController($conn);
        echo json_encode(['status' => 'success', 'data' => $controller->fetchShippingLocations()]);
        $is404 = false; exit;

    // ── Cart API endpoints ────────────────────────────────────────
    case "add-to-cart-backend":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            addToCart($data);
        }
        $is404 = false; exit;

    case "update-cart-quantity":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            updateCartQuantity($data);
        }
        $is404 = false; exit;

    case "remove-cart-item":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            removeCartItem($data);
        }
        $is404 = false; exit;

    case "get-cart":
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');
            try {
                echo json_encode(getCartItems());
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Failed to fetch cart']);
            }
        }
        $is404 = false; exit;

    // ── Coupon validation ─────────────────────────────────────────
    case "validate-coupon":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data        = json_decode(file_get_contents('php://input'), true);
            $cartData    = getCartItems();
            $totalNgn    = $cartData['total_ngn'] ?? null;
            $websiteInfo = selectContent($conn, "settings_website_info", []);
            $usdToggle   = isset($websiteInfo[0]['input_usd_toggle']) ? (int) $websiteInfo[0]['input_usd_toggle'] : 0;
            $totalUsd    = ($usdToggle === 1) ? ($cartData['total_usd'] ?? null) : null;
            $identifier  = $_SESSION['customer_id'] ?? ($_SESSION['user_id'] ?? 'guest');

            $mgr = new CouponManager($conn);
            $res = $mgr->validateDual($data['code'] ?? '', $totalNgn, $totalUsd, $identifier);

            if ($res['success']) {
                $dualResults = $mgr->calculateDualFinalPrices($res['coupon'], $totalNgn, $totalUsd);
                echo json_encode(['success' => true, 'message' => 'Coupon applied!', 'ngn' => $dualResults['ngn'], 'usd' => $dualResults['usd']]);
            } else {
                echo json_encode(['success' => false, 'message' => $res['message']]);
            }
        }
        $is404 = false; exit;

    // ── Customer auth & account ───────────────────────────────────
    case "customer-login":
        include APP_PATH . "/views/customer_login.php"; $is404 = false; die;
    case "customer-logout":
        include APP_PATH . "/views/customer_logout.php"; $is404 = false; die;
    case "customer-dashboard":
        include APP_PATH . "/views/customer_dashboard.php"; $is404 = false; die;
    case "customer-verify":
        include APP_PATH . "/views/customer_verify.php"; $is404 = false; die;
    case "customer-forgot-password":
        include APP_PATH . "/views/customer_forgot.php"; $is404 = false; die;
    case "customer-reset-password":
        include APP_PATH . "/views/customer_reset.php"; $is404 = false; die;
    case "customer-login-submit":
        include APP_PATH . "/views/customer_login_backend.php"; $is404 = false; die;
    case "customer-register-submit":
        include APP_PATH . "/views/customer_register_backend.php"; $is404 = false; die;
    case "customer-update-profile":
        include APP_PATH . "/views/customer_update_backend.php"; $is404 = false; die;
    case "customer-forgot-submit":
        include APP_PATH . "/views/customer_forgot_backend.php"; $is404 = false; die;
    case "customer-reset-submit":
        include APP_PATH . "/views/customer_reset_backend.php"; $is404 = false; die;
}
?>