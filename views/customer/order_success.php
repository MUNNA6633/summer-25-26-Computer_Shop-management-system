<?php
session_start();
if (!isset($_SESSION['customer_id'])) { header('Location: login.php'); exit; }
$oid = $_SESSION['last_order_id'] ?? '—';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Placed</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container" style="text-align:center; padding:60px 0;">
    <h1>✅ Order Placed Successfully!</h1>
    <p>Your order ID is <strong>#<?= htmlspecialchars($oid) ?></strong></p>
    <p>Thank you for your purchase.</p>
    <a href="home.php" class="btn-primary">Continue Shopping</a>
</div>
</body>
</html>