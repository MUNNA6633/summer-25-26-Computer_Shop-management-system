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
            <?php foreach ($products as $p):
                $discount = (float) ($p['discount_percent'] ?? 0);
                $final_price = $discount > 0 ? $p['retail_price'] * (1 - $discount / 100) : $p['retail_price'];
            ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td>
                    <?php if ($discount > 0): ?>
                        <span style="text-decoration:line-through; color:#999;">$<?= number_format($p['retail_price'], 2) ?></span>
                        $<?= number_format($final_price, 2) ?>
                        <span style="background:#0f766e; color:#fff; padding:0.1rem 0.4rem; border-radius:4px; font-size:0.75rem;"><?= number_format($discount, 0) ?>% off</span>
                    <?php else: ?>
                        $<?= number_format($p['retail_price'], 2) ?>
                    <?php endif; ?>
                </td>
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