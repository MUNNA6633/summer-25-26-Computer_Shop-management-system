<?php
require_once __DIR__ . '/../config/config.php';

function register_customer($name, $email, $phone, $password) {
    global $conn;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO customers (name, email, phone, password) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $phone, $hash);
    return mysqli_stmt_execute($stmt);
}

function get_customer_by_email($email) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

function get_addresses($customer_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM addresses WHERE customer_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

function add_address($customer_id, $label, $full_name, $phone, $address_line, $city, $postal_code) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO addresses (customer_id, label, full_name, phone, address_line, city, postal_code)
         VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'issssss',
        $customer_id, $label, $full_name, $phone, $address_line, $city, $postal_code);
    return mysqli_stmt_execute($stmt);
}

function delete_address($address_id, $customer_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "DELETE FROM addresses WHERE id = ? AND customer_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $address_id, $customer_id);
    return mysqli_stmt_execute($stmt);
}

function get_cart($customer_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.retail_price
         FROM cart c JOIN products p ON c.product_id = p.id
         WHERE c.customer_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

function add_to_cart($customer_id, $product_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT id FROM cart WHERE customer_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $customer_id, $product_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($row) {
        $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $row['id']);
        return mysqli_stmt_execute($stmt);
    }
    $stmt = mysqli_prepare($conn,
        "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, 1)");
    mysqli_stmt_bind_param($stmt, 'ii', $customer_id, $product_id);
    return mysqli_stmt_execute($stmt);
}

function clear_cart($customer_id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE customer_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    return mysqli_stmt_execute($stmt);
}

function place_order($customer_id, $address_id, $payment_method, $items, $delivery_fee = 50) {
    global $conn;
    $subtotal = 0;
    foreach ($items as $it) { $subtotal += $it['retail_price'] * $it['quantity']; }
    $total = $subtotal + $delivery_fee;

    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (customer_id, address_id, payment_method, subtotal, delivery_fee, total)
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iisddd',
        $customer_id, $address_id, $payment_method, $subtotal, $delivery_fee, $total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    foreach ($items as $it) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
             VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iisdi',
            $order_id, $it['product_id'], $it['name'], $it['retail_price'], $it['quantity']);
        mysqli_stmt_execute($stmt);
    }

    $status = ($payment_method === 'cod') ? 'Pending' : 'Paid';
    $stmt = mysqli_prepare($conn,
        "INSERT INTO payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'isds', $order_id, $payment_method, $total, $status);
    mysqli_stmt_execute($stmt);

    clear_cart($customer_id);
    return $order_id;
}

function get_orders($customer_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

function get_products() {
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}