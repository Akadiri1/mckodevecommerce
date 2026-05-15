<?php
$_SESSION['ext_redirect'] = $_SERVER['HTTP_REFERER'];

if (!isset($_GET['ext_link'])) {
    header("Location:". $_SERVER['HTTP_REFERER']);
    exit();
} else {
    $admin = selectContent($conn, "admin", ["hash_id" => base64url_decode($_GET['admin'])]);

    if (count($admin) < 1) {
        header("Location:". $_SERVER['HTTP_REFERER']);
        exit();
    }

    $_SESSION['admin_id'] = base64url_decode($_GET['admin']);

    // Check if 'si' is present in the GET request
    if (isset($_GET['si'])) {
        // Set the cookie "session_only" to "true"
        // 0 expiry ensures it lasts only for the current browser session
        setcookie("session_only", "true", 0, "/");
    }

    header("Location:".$_GET['ext_link']);
    exit();
}
?>