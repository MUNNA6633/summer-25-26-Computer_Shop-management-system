<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Computer Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="brand">Admin Panel</div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="vendors.php">Vendors</a></li>
                <li><a href="announcements.php">Announcements</a></li>
                <li><a href="reviews.php">Customer Reviews</a></li>
                <li><a href="../index.php" target="_blank">View Website</a></li>
                <li><a href="logout.php" style="color: #f87171;">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <h3>Logged in as: <?php echo htmlspecialchars($_SESSION['admin_user'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?></h3>
            </header>
            <div class="content">