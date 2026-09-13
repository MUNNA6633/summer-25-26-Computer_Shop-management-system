<?php
require_once 'config/database.php';

$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
$reviews = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Computer Shop - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="public-nav">
        <h2 style="color: white;">Tech Computer Shop</h2>
        <div>
            <a href="index.php">Home</a>
            <a href="submit_review.php">Leave Review</a>
            <a href="admin/login.php">Admin Login</a>
        </div>
    </nav>

    <div class="public-container">
        <h2>Latest Shop Announcements</h2>
        <?php if ($announcements->num_rows > 0): ?>
            <?php while($row = $announcements->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <small style="color: #64748b;"><?php echo $row['created_at']; ?></small>
                    <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card"><p>No announcements available.</p></div>
        <?php endif; ?>

        <h2 style="margin-top: 2rem;">Customer Feedback</h2>
        <?php if ($reviews->num_rows > 0): ?>
            <?php while($rev = $reviews->fetch_assoc()): ?>
                <div class="card">
                    <strong><?php echo htmlspecialchars($rev['customer_name']); ?></strong> 
                    <span style="color: #f59e0b;">(Rating: <?php echo $rev['rating']; ?>/5)</span>
                    <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars($rev['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card"><p>No reviews yet. Be the first to leave one!</p></div>
        <?php endif; ?>
    </div>
</body>
</html>