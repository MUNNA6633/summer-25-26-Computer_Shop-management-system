<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="cust-nav">
    <a href="home.php" class="logo">🛒 Shop</a>
    <div class="links">
        <a href="home.php">Products</a>
        <a href="cart.php">Cart</a>
        <a href="addresses.php">Addresses</a>
        <a href="my_orders.php">My Orders</a>
        <?php if (isset($_SESSION['customer_name'])): ?>
            <span>Hi, <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            <a href="../../controllers/customer_controller/logout.php">Logout</a>
        <?php else: ?>
            <a href="../../index.php">Login</a>
        <?php endif; ?>
    </div>
</nav>