<?php
require_once __DIR__ . '/../config/config.php';

/* ================= Products / Discounts (Create, Read, Update, Delete) ================= */

function get_products_with_discount() {
    global $conn;
    $res = mysqli_query($conn,
        "SELECT id, name, category, retail_price, discount_percent, quantity
         FROM products ORDER BY id DESC");
    return $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
}

// Also used to set a discount for the first time - discount_percent already
// exists (default 0) on every product row, so "create" and "update" are the
// same UPDATE statement.
function apply_discount($product_id, $discount_percent) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE products SET discount_percent = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'di', $discount_percent, $product_id);
    return mysqli_stmt_execute($stmt);
}

// Delete = clear the discount back to 0.
function remove_discount($product_id) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE products SET discount_percent = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    return mysqli_stmt_execute($stmt);
}

/* ================= Orders (Create, Read, Update, Delete) ================= */

function get_all_orders() {
    global $conn;
    $res = mysqli_query($conn,
        "SELECT o.id, o.payment_method, o.payment_status, o.subtotal, o.delivery_fee,
                o.total, o.status, o.created_at, c.name AS customer_name,
                (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
         FROM orders o
         JOIN customers c ON o.customer_id = c.id
         ORDER BY o.created_at DESC");
    return $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
}

// Addresses to pick from when the seller creates a manual order - one
// per saved customer address, same "addresses" table the customer flow uses.
function get_addresses_for_order_form() {
    global $conn;
    $res = mysqli_query($conn,
        "SELECT a.id AS address_id, a.label, a.city, c.name AS customer_name
         FROM addresses a
         JOIN customers c ON a.customer_id = c.id
         ORDER BY c.name, a.label");
    return $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
}

// Same "products" table the vendor stocks and the customer shops from.
function get_products_for_order_form() {
    global $conn;
    $res = mysqli_query($conn,
        "SELECT id, name, retail_price, discount_percent, quantity FROM products ORDER BY name");
    return $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
}

// Seller-side manual order entry (e.g. a phone/in-store sale). Writes to the
// exact same orders / order_items / payments tables the customer checkout
// flow uses, so every order - customer-placed or seller-entered - lives in
// one place.
function create_order($address_id, $product_id, $quantity, $payment_method) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT customer_id FROM addresses WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $address_id);
    mysqli_stmt_execute($stmt);
    $address = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if (!$address) {
        return 'Address not found.';
    }

    $stmt = mysqli_prepare($conn,
        "SELECT name, quantity AS stock,
                ROUND(retail_price * (1 - discount_percent / 100), 2) AS unit_price
         FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if (!$product) {
        return 'Product not found.';
    }
    if ($quantity < 1) {
        return 'Quantity must be at least 1.';
    }
    if ($quantity > $product['stock']) {
        return 'Only ' . $product['stock'] . ' in stock.';
    }

    $customer_id = $address['customer_id'];
    $delivery_fee = 50;
    $subtotal = $product['unit_price'] * $quantity;
    $total = $subtotal + $delivery_fee;

    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (customer_id, address_id, payment_method, subtotal, delivery_fee, total)
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iisddd',
        $customer_id, $address_id, $payment_method, $subtotal, $delivery_fee, $total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iisdi',
        $order_id, $product_id, $product['name'], $product['unit_price'], $quantity);
    mysqli_stmt_execute($stmt);

    $payment_status = ($payment_method === 'cod') ? 'Pending' : 'Paid';
    $stmt = mysqli_prepare($conn,
        "INSERT INTO payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'isds', $order_id, $payment_method, $total, $payment_status);
    mysqli_stmt_execute($stmt);

    return $order_id;
}

function update_order_status($order_id, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
    return mysqli_stmt_execute($stmt);
}

// order_items and payments both have ON DELETE CASCADE back to orders, so
// deleting the order cleans up its line items and payment record too.
function delete_order($order_id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    return mysqli_stmt_execute($stmt);
}

/* ================= Sell Summary ================= */

function get_sales_summary() {
    global $conn;

    $totals = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS order_count,
                COALESCE(SUM(total), 0) AS revenue,
                COALESCE(AVG(total), 0) AS avg_order_value
         FROM orders"));

    $items_sold = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COALESCE(SUM(quantity), 0) AS total_items FROM order_items"));

    $top_products = mysqli_query($conn,
        "SELECT oi.product_id, oi.product_name, SUM(oi.quantity) AS units_sold,
                SUM(oi.price * oi.quantity) AS revenue
         FROM order_items oi
         GROUP BY oi.product_id, oi.product_name
         ORDER BY units_sold DESC
         LIMIT 5");

    return [
        'order_count'     => (int) $totals['order_count'],
        'revenue'         => (float) $totals['revenue'],
        'avg_order_value' => (float) $totals['avg_order_value'],
        'items_sold'      => (int) $items_sold['total_items'],
        'top_products'    => $top_products ? mysqli_fetch_all($top_products, MYSQLI_ASSOC) : [],
    ];
}
