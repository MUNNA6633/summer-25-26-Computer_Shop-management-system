<?php
session_start();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

require "../../config/config.php";
require "../../models/vendor_model.php";
$products = get_products($conn);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css?v=2">
</head>
<body>

    <a href="../../controllers/logout.php"
       style="position:absolute; top:15px; right:15px; background-color:#e74c3c; color:#fff;
              padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:bold;
              font-family:Arial, sans-serif; z-index:999;">Logout</a>

    <?php include '../../controllers/vendor_controller/navbar.php'; ?>

    <div class="container">
        <h2>Manage Products</h2>

        <?php if ($flash): ?>
            <section class="summary">
                <p><?= $flash ?></p>
            </section>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Wholesale Price</th>
                <th>Retail Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php if (count($products) === 0): ?>
                <tr><td colspan="7">No products yet. <a href="add_product.php">Add one</a>.</td></tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id']; ?></td>
                    <td><?= $p['name']; ?></td>
                    <td><?= $p['category']; ?></td>
                    <td>Tk <?= $p['wholesale_price']; ?></td>
                    <td>Tk <?= $p['retail_price']; ?></td>
                    <td><?= $p['quantity']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $p['id']; ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="post" action="delete_product.php" style="display:inline"
                              onsubmit="return confirm('Delete this product? This cannot be undone.');">
                            <input type="hidden" name="id" value="<?= $p['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>
