<?php
require_once '../../controllers/seller_controller/auth.php';
require_once '../../models/seller_model.php';

$summary = get_sales_summary();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Summary - Seller</title>
    <link rel="stylesheet" href="../../assets/seller_style.css">
</head>
<body>

    <a href="../../controllers/logout.php" class="logout-btn" style="color:#fff; text-decoration:none;">Logout</a>

    <?php include '../../controllers/seller_controller/navbar.php'; ?>

    <div class="container">
        <h2>Sell Summary</h2>

        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Orders</h3>
                <div class="value"><?= $summary['order_count'] ?></div>
            </div>
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="value">Tk <?= number_format($summary['revenue'], 2) ?></div>
            </div>
            <div class="summary-card">
                <h3>Avg. Order Value</h3>
                <div class="value">Tk <?= number_format($summary['avg_order_value'], 2) ?></div>
            </div>
            <div class="summary-card">
                <h3>Items Sold</h3>
                <div class="value"><?= $summary['items_sold'] ?></div>
            </div>
        </div>

        <h2>Top-Selling Products</h2>
        <table>
            <tr>
                <th>Product</th>
                <th>Units Sold</th>
                <th>Revenue</th>
            </tr>
            <?php if (empty($summary['top_products'])): ?>
            <tr><td colspan="3">No sales yet.</td></tr>
            <?php else: foreach ($summary['top_products'] as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= $p['units_sold'] ?></td>
                <td>Tk <?= number_format($p['revenue'], 2) ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </table>
    </div>

</body>
</html>
