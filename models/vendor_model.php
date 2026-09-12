<?php


function get_products($conn) {
    $res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function search_products($conn, $term) {
    $like = "%" . $term . "%";
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, category, wholesale_price, retail_price, quantity
         FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY name ASC"
    );
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function get_low_stock_products($conn, $threshold) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE quantity <= ? ORDER BY quantity ASC");
    mysqli_stmt_bind_param($stmt, "i", $threshold);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function get_product($conn, $id) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT name, category, wholesale_price, retail_price, quantity FROM products WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $name, $category, $wholesale_price, $retail_price, $quantity);

    $found = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$found) {
        return null;
    }
    return [
        "name" => $name,
        "category" => $category,
        "wholesale_price" => $wholesale_price,
        "retail_price" => $retail_price,
        "quantity" => $quantity,
    ];
}

function insert_product($conn, $name, $category, $wholesale_price, $retail_price, $quantity) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products (name, category, wholesale_price, retail_price, quantity) VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssddi", $name, $category, $wholesale_price, $retail_price, $quantity);

    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : $error;
}

function update_product($conn, $id, $name, $category, $wholesale_price, $retail_price, $quantity) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE products SET name = ?, category = ?, wholesale_price = ?, retail_price = ?, quantity = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "ssddii", $name, $category, $wholesale_price, $retail_price, $quantity, $id);

    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : $error;
}

function delete_product_by_id($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

/* Damage reports */

function get_damage_reports($conn) {
    $res = mysqli_query($conn, "SELECT * FROM damage_reports ORDER BY id DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function search_damage_reports($conn, $term) {
    $like = "%" . $term . "%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM damage_reports WHERE product_name LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function insert_damage_report($conn, $product, $damage_qty, $note) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO damage_reports (product_name, damage_qty, note) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sis", $product, $damage_qty, $note);

    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : $error;
}

/* Deliveries  */

function get_deliveries($conn) {
    $res = mysqli_query($conn, "SELECT * FROM deliveries ORDER BY updated_at DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function search_deliveries($conn, $term) {
    $like = "%" . $term . "%";
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM deliveries WHERE order_id LIKE ? OR status LIKE ? ORDER BY updated_at DESC"
    );
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function delivery_exists($conn, $order_id) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM deliveries WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function update_delivery_status($conn, $order_id, $status) {
    $stmt = mysqli_prepare($conn, "UPDATE deliveries SET status = ? WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, "ss", $status, $order_id);

    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : $error;
}


function get_system_stats($conn) {
    $stats = [];

    $stats['total_products'] = (int) mysqli_fetch_row(
        mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];

    $stats['low_stock_count'] = (int) mysqli_fetch_row(mysqli_query($conn,
        "SELECT COUNT(*) FROM products WHERE quantity <= " . (int)LOW_STOCK))[0];

    $stats['total_damage_reports'] = (int) mysqli_fetch_row(
        mysqli_query($conn, "SELECT COUNT(*) FROM damage_reports"))[0];

    $stats['pending_deliveries'] = (int) mysqli_fetch_row(mysqli_query($conn,
        "SELECT COUNT(*) FROM deliveries WHERE status = 'Pending'"))[0];

    return $stats;
}
