<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: ../../index.php'); exit; }

add_address(
    $_SESSION['customer_id'],
    trim($_POST['label']),
    trim($_POST['full_name']),
    trim($_POST['phone']),
    trim($_POST['address_line']),
    trim($_POST['city']),
    trim($_POST['postal_code'] ?? '')
);

$_SESSION['addr_msg'] = 'Address added successfully.';
header('Location: ../../views/customer/addresses.php'); exit;