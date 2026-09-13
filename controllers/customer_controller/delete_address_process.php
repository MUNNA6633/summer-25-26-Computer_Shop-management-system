<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: ../../views/customer/login.php'); exit; }

$aid = intval($_POST['address_id'] ?? 0);
if ($aid > 0) {
    delete_address($aid, $_SESSION['customer_id']);
    $_SESSION['addr_msg'] = 'Address deleted.';
}
header('Location: ../../views/customer/addresses.php'); exit;