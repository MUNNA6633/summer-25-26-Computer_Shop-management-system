<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$cust = get_customer_by_email($email);
if (!$cust || !password_verify($password, $cust['password'])) {
    $_SESSION['err'] = 'Invalid email or password.';
    header('Location: ../../views/customer/login.php'); exit;
}

$_SESSION['customer_id'] = $cust['id'];
$_SESSION['customer_name'] = $cust['name'];
header('Location: ../../views/customer/home.php'); exit;