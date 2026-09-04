<?php
session_start();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

require "db.php";
$result = mysqli_query(
    $conn,
    "SELECT id, name, category, wholesale_price, retail_price, quantity FROM products ORDER BY id DESC"
);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
mysqli_free_result($result);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Vendor System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

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
