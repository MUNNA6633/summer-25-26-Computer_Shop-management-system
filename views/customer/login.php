<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Customer Login</h2>
    <?php if (!empty($_SESSION['err'])): ?>
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            <?= htmlspecialchars($_SESSION['err']); unset($_SESSION['err']); ?>
        </div>
    <?php endif; ?>
    <form action="../../controllers/customer_controller/login_process.php" method="post" class="form">
        <label>Email <input type="email" name="email" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <p style="margin-top:14px;">New here? <a href="register.php">Create an account</a></p>
</div>
</body>
</html>