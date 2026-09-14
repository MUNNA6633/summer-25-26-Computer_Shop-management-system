<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: ../../index.php'); exit; }
$cid = $_SESSION['customer_id'];
$cart = get_cart($cid);
$addresses = get_addresses($cid);
if (empty($cart)) { echo "<p>Cart is empty. <a href='home.php'>Shop</a></p>"; exit; }

$subtotal = 0;
foreach ($cart as $c) { $subtotal += $c['retail_price'] * $c['quantity']; }
$delivery_fee = 50;
$total = $subtotal + $delivery_fee;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Checkout</h2>

    <table class="table">
        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        <?php foreach ($cart as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= $c['quantity'] ?></td>
            <td>$<?= number_format($c['retail_price'], 2) ?></td>
            <td>$<?= number_format($c['retail_price'] * $c['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="3">Subtotal</td><td>$<?= number_format($subtotal, 2) ?></td></tr>
        <tr><td colspan="3">Delivery Fee</td><td>$<?= number_format($delivery_fee, 2) ?></td></tr>
        <tr><td colspan="3"><strong>Total</strong></td><td><strong>$<?= number_format($total, 2) ?></strong></td></tr>
    </table>

    <form action="../../controllers/customer_controller/place_order_process.php" method="post">
        <h3>1. Select Delivery Address</h3>
        <?php if (empty($addresses)): ?>
            <p>No saved addresses. <a href="addresses.php">Add one first</a></p>
        <?php else: ?>
            <?php foreach ($addresses as $a): ?>
                <label class="radio-row">
                    <input type="radio" name="address_id" value="<?= $a['id'] ?>" required>
                    <strong><?= htmlspecialchars($a['label']) ?></strong> — <?= htmlspecialchars($a['full_name']) ?>,
                    <?= htmlspecialchars($a['address_line']) ?>, <?= htmlspecialchars($a['city']) ?>
                </label>
            <?php endforeach; ?>
        <?php endif; ?>

        <h3>2. Payment Method</h3>
        <label class="radio-row"><input type="radio" name="payment_method" value="cod" required> Cash on Delivery</label>
        <label class="radio-row"><input type="radio" name="payment_method" value="card"> Credit / Debit Card</label>
        <label class="radio-row"><input type="radio" name="payment_method" value="bkash"> bKash</label>
        <label class="radio-row"><input type="radio" name="payment_method" value="nagad"> Nagad</label>

        <br>
        <button type="submit" class="btn-primary">Place Order</button>
    </form>
</div>
</body>
</html>