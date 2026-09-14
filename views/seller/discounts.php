<?php
require_once '../../controllers/seller_controller/auth.php';
require_once '../../models/seller_model.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_discount'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $discount = trim($_POST['discount_percent'] ?? '');

    if ($product_id <= 0) {
        $error = 'Choose a product.';
    } elseif ($discount === '' || !is_numeric($discount) || $discount < 0 || $discount > 90) {
        $error = 'Enter a discount between 0 and 90.';
    } else {
        apply_discount($product_id, (float) $discount);
        header('Location: discounts.php?saved=1');
        exit;
    }
}

if (isset($_GET['remove'])) {
    remove_discount(intval($_GET['remove']));
    header('Location: discounts.php?removed=1');
    exit;
}

$products = get_products_with_discount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply Discount - Seller</title>
    <link rel="stylesheet" href="../../assets/seller_style.css">
</head>
<body>

    <a href="../../controllers/logout.php" class="logout-btn" style="color:#fff; text-decoration:none;">Logout</a>

    <?php include '../../controllers/seller_controller/navbar.php'; ?>

    <div class="container">
        <h2>Apply Discount</h2>

        <?php if (isset($_GET['saved'])): ?>
            <p style="color:#0f766e; font-weight:bold;">Discount saved.</p>
        <?php endif; ?>
        <?php if (isset($_GET['removed'])): ?>
            <p style="color:#0f766e; font-weight:bold;">Discount removed.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color:#c0392b; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Current Discount</th>
                <th>Set Discount (%)</th>
                <th>Action</th>
            </tr>
            <?php if (empty($products)): ?>
            <tr><td colspan="7">No products yet - ask a vendor to add stock first.</td></tr>
            <?php else: foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td>Tk <?= number_format($p['retail_price'], 2) ?></td>
                <td>
                    <?php if ($p['discount_percent'] > 0): ?>
                        <span class="discount-badge"><?= number_format($p['discount_percent'], 0) ?>% off</span>
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
                <td>
                    <form class="inline-form" method="post" action="discounts.php">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="number" name="discount_percent" min="0" max="90" step="1"
                               value="<?= $p['discount_percent'] > 0 ? number_format($p['discount_percent'], 0, '.', '') : '' ?>"
                               placeholder="0">
                        <button type="submit" name="apply_discount">Save</button>
                    </form>
                </td>
                <td>
                    <?php if ($p['discount_percent'] > 0): ?>
                        <a href="discounts.php?remove=<?= $p['id'] ?>" onclick="return confirm('Remove the discount on <?= htmlspecialchars($p['name'], ENT_QUOTES) ?>?')" style="color:#c0392b; font-weight:bold;">Remove</a>
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </table>
    </div>

</body>
</html>
