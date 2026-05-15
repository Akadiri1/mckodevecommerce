<?php
// Customer logout — unset customer session variables and redirect home
unset($_SESSION['customer_id']);
unset($_SESSION['customer_hash']);
unset($_SESSION['customer_name']);

header('Location: ' . $baseUrl . '/');
exit;
