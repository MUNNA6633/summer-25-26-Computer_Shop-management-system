<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Create an Account</h2>
    <?php if (!empty($_SESSION['err'])): ?>
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            <?= htmlspecialchars($_SESSION['err']); unset($_SESSION['err']); ?>
        </div>
    <?php endif; ?>
    <form action="../../controllers/customer_controller/register_process.php" method="post" class="form">
        <label>Name <input type="text" name="name" required></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Phone <input type="text" name="phone" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit" class="btn-primary">Register</button>
    </form>
    <p style="margin-top:14px;">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>