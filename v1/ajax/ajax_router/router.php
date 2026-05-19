<?php
$uri = explode("/", $_SERVER['REQUEST_URI']);
$segment = $uri[1] ?? '';

// Strip query string from segment
if (strpos($segment, '?') !== false) {
    $segment = explode('?', $segment)[0];
}

switch ($segment) {
    case 'ajax':
        $sub = $uri[2] ?? '';
        if (strpos($sub, '?') !== false) {
            $sub = explode('?', $sub)[0];
        }
        switch ($sub) {
            case 'add':
                include APP_PATH . "/ajax/add.php"; $is404 = false; die;
            case 'put':
                include APP_PATH . "/ajax/put.php"; $is404 = false; die;
            case 'delete':
                include APP_PATH . "/ajax/delete.php"; $is404 = false; die;
            case 'read':
                include APP_PATH . "/ajax/read.php"; $is404 = false; die;
            case 'upload2server':
                include APP_PATH . "/ajax/upload2server.php"; $is404 = false; die;
            case 'change2server':
                include APP_PATH . "/ajax/change2server.php"; $is404 = false; die;
            case 'multiple2server':
                include APP_PATH . "/ajax/multiple2server.php"; $is404 = false; die;
            case 'delete2server':
                include APP_PATH . "/ajax/delete2server.php"; $is404 = false; die;
            case 'serialize':
                include APP_PATH . "/ajax/serialize.php"; $is404 = false; die;
            case 'unserialize':
                include APP_PATH . "/ajax/unserialize.php"; $is404 = false; die;
        }
        break;
}