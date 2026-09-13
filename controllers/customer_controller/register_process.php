<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$phone || !$password) {
    $_SESSION['err'] = 'All fields are required.';
    header('Location: ../../views/customer/register.php'); exit;
}

if (get_customer_by_email($email)) {
    $_SESSION['err'] = 'Email already registered.';
    header('Location: ../../views/customer/register.php'); exit;
}

if (register_customer($name, $email, $phone, $password)) {
    $_SESSION['customer_id'] = mysqli_insert_id($GLOBALS['conn']);
    $_SESSION['customer_name'] = $name;
    header('Location: ../../views/customer/home.php'); exit;
}

$_SESSION['err'] = 'Registration failed.';
header('Location: ../../views/customer/register.php'); exit;