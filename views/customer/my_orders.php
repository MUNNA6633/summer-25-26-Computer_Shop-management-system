<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: login.php'); exit; }
$orders = get_orders($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>My Orders</h2>
    <?php if (empty($orders)): ?>
        <p>You haven't placed any orders yet.</p>
    <?php else: ?>
        <table class="table">
            <tr><th>Order ID</th><th>Payment</th><th>Total</th><th>Status</th><th>Date</th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['payment_method']) ?></td>
                <td>$<?= number_format($o['total'], 2) ?></td>
                <td><?= htmlspecialchars($o['status']) ?></td>
                <td><?= $o['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>