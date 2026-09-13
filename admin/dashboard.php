<?php
require_once '../config/database.php';
include 'header.php';

$vendor_count = $conn->query("SELECT COUNT(*) as total FROM vendors")->fetch_assoc()['total'];
$announcement_count = $conn->query("SELECT COUNT(*) as total FROM announcements")->fetch_assoc()['total'];
$review_count = $conn->query("SELECT COUNT(*) as total FROM reviews")->fetch_assoc()['total'];
?>

<h2>Dashboard Overview</h2>

<div class="stats-grid">
    <div class="card stat-card">
        <h3>Total Vendors</h3>
        <p class="stat-number"><?php echo $vendor_count; ?></p>
    </div>
    <div class="card stat-card">
        <h3>Announcements</h3>
        <p class="stat-number"><?php echo $announcement_count; ?></p>
    </div>
    <div class="card stat-card">
        <h3>Customer Reviews</h3>
        <p class="stat-number"><?php echo $review_count; ?></p>
    </div>
</div>

<?php include 'footer.php'; ?>