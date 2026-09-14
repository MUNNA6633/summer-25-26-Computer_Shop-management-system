<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/customer_model.php';

$users = [
    'admin'    => ['password' => 'admin123',    'role' => 'admin',    'redirect' => 'admin/dashboard.php'],
    'vendor'   => ['password' => 'vendor123',   'role' => 'vendor',   'redirect' => 'views/vendor/add_product.php'],
    'customer' => ['password' => 'customer123', 'role' => 'customer', 'redirect' => 'views/customer/home.php'],
    'seller'   => ['password' => 'seller123',   'role' => 'seller',   'redirect' => 'views/seller/dashboard.php'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } elseif (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];

        // The customer role needs a real row in the customers table so
        // cart/addresses/orders (which all key off customer_id) work -
        // this attaches the hardcoded login to one seeded demo account.
        if ($username === 'customer') {
            $cust = get_customer_by_email('customer@example.com');
            if ($cust) {
                $_SESSION['customer_id'] = $cust['id'];
                $_SESSION['customer_name'] = $cust['name'];
            } else {
                $error = 'Demo customer record not found - run schema.sql.';
            }
        }

        if (!$error) {
            header('Location: ' . $users[$username]['redirect']);
            exit;
        }
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Vendor System</title>
    <link rel="stylesheet" href="assets/login_style.css">
</head>
<body>

    <div class="login-card">
        <h2>Sign In</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="index.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password">

            <button type="submit">Sign In</button>
        </form>
    </div>

</body>
</html>
