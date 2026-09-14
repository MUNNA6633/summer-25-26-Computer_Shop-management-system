<?php
require_once __DIR__ . '/../models/admin_model.php';
include 'header.php';

$counts = get_dashboard_counts();
?>

<h2>Dashboard Overview</h2>

<div class="stats-grid">
    <div class="card stat-card">
        <h3>Total Vendors</h3>
        <p class="stat-number"><?php echo $counts['vendor_count']; ?></p>
    </div>
    <div class="card stat-card">
        <h3>Announcements</h3>
        <p class="stat-number"><?php echo $counts['announcement_count']; ?></p>
    </div>
    <div class="card stat-card">
        <h3>Customer Reviews</h3>
        <p class="stat-number"><?php echo $counts['review_count']; ?></p>
    </div>
</div>

<?php include 'footer.php'; ?>
