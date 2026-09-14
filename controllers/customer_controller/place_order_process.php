<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: ../../index.php'); exit; }

$address_id = intval($_POST['address_id'] ?? 0);
$payment = $_POST['payment_method'] ?? '';

if ($address_id <= 0 || !in_array($payment, ['cod','card','bkash','nagad'])) {
    die('Invalid request.');
}

$items = get_cart($_SESSION['customer_id']);
if (empty($items)) { die('Cart is empty.'); }

$order_id = place_order($_SESSION['customer_id'], $address_id, $payment, $items, 50);
$_SESSION['last_order_id'] = $order_id;
header('Location: ../../views/customer/order_success.php'); exit;