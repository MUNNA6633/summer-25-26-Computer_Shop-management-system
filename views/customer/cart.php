<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: ../../index.php'); exit; }
$cart = get_cart($_SESSION['customer_id']);
$total = 0;
foreach ($cart as $c) { $total += $c['retail_price'] * $c['quantity']; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Your Cart</h2>
    <?php if (empty($cart)): ?>
        <p>Your cart is empty. <a href="home.php">Shop now</a></p>
    <?php else: ?>
        <table class="table">
            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
            <?php foreach ($cart as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $c['quantity'] ?></td>
                <td>$<?= number_format($c['retail_price'], 2) ?></td>
                <td>$<?= number_format($c['retail_price'] * $c['quantity'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <h3>Total: $<?= number_format($total, 2) ?></h3>
        <a href="checkout.php" class="btn-primary">Proceed to Checkout</a>
    <?php endif; ?>
</div>
</body>
</html>