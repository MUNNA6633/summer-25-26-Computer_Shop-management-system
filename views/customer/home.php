<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
$products = get_products();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Available Products</h2>
    <?php if (empty($products)): ?>
        <p>No products available yet.</p>
    <?php else: ?>
        <table class="table">
            <tr><th>Name</th><th>Category</th><th>Price</th><th>Action</th></tr>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td>$<?= number_format($p['retail_price'], 2) ?></td>
                <td>
                    <form action="../../controllers/customer_controller/add_to_cart_process.php" method="post" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn-primary">Add to Cart</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>