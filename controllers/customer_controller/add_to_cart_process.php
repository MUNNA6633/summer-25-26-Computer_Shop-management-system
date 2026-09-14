<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';

if (!isset($_SESSION['customer_id'])) { header('Location: ../../index.php'); exit; }

$pid = intval($_POST['product_id'] ?? 0);
if ($pid > 0) {
    add_to_cart($_SESSION['customer_id'], $pid);
}
header('Location: ../../views/customer/home.php'); exit;