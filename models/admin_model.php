<?php
require_once __DIR__ . '/../config/config.php';

/*  Dashboard  */

function get_dashboard_counts() {
    global $conn;
    $vendor_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM vendors"))['total'];
    $announcement_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM announcements"))['total'];
    $review_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reviews"))['total'];

    return [
        'vendor_count'       => $vendor_count,
        'announcement_count' => $announcement_count,
        'review_count'       => $review_count,
    ];
}

/*  Vendors  */

function get_vendors() {
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM vendors ORDER BY id DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function add_vendor($name, $email, $phone) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO vendors (vendor_name, contact_email, phone) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $phone);
    return mysqli_stmt_execute($stmt);
}

function delete_vendor($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM vendors WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return mysqli_stmt_execute($stmt);
}

/*  Announcements  */

function get_announcements() {
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function add_announcement($title, $content) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO announcements (title, content) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $title, $content);
    return mysqli_stmt_execute($stmt);
}

function delete_announcement($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM announcements WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return mysqli_stmt_execute($stmt);
}

/*  Reviews  */

function get_reviews() {
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}
