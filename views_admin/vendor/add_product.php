<?php
require_once __DIR__ . "/../../controllers/vendor_controller/add_product_process.php";

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Wholesale Price - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css">
</head>
<body>

    <?php include __DIR__ . "/../../controllers/vendor_controller/navbar.php"; ?>

    <div class="container">
        <h2>Add Product / Set Wholesale Price</h2>

        <?php if ($flash): ?>
            <section class="summary">
                <p><?= $flash ?></p>
            </section>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" placeholder="Enter product name" value="<?= $name ?>">
            <?php if ($nameErr): ?><span class="error"><?= $nameErr ?></span><?php endif; ?>

            <label for="category">Category</label>
            <input type="text" id="category" name="category" placeholder="e.g. Accessories, Display" value="<?= $category ?>">
            <?php if ($categoryErr): ?><span class="error"><?= $categoryErr ?></span><?php endif; ?>

            <label for="wholesale_price">Wholesale Price (Tk)</label>
            <input type="number" step="0.01" id="wholesale_price" name="wholesale_price" placeholder="Enter wholesale price" value="<?= $wholesale_price ?>">
            <?php if ($wholesaleErr): ?><span class="error"><?= $wholesaleErr ?></span><?php endif; ?>

            <label for="retail_price">Retail Price (Tk)</label>
            <input type="number" step="0.01" id="retail_price" name="retail_price" placeholder="Enter retail price" value="<?= $retail_price ?>">
            <?php if ($retailErr): ?><span class="error"><?= $retailErr ?></span><?php endif; ?>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" placeholder="Enter stock quantity" value="<?= $quantity ?>">
            <?php if ($quantityErr): ?><span class="error"><?= $quantityErr ?></span><?php endif; ?>

            <button type="submit">Save Product</button>
        </form>

        <h2>Existing Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Wholesale Price</th>
                <th>Retail Price</th>
                <th>Quantity</th>
            </tr>
            <?php
            require __DIR__ . "/../../config/config.php";
            require __DIR__ . "/../../models/vendor_model.php";
            $existingProducts = get_products($conn);
            mysqli_close($conn);
            foreach ($existingProducts as $row):
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['category']; ?></td>
                <td>Tk <?= $row['wholesale_price']; ?></td>
                <td>Tk <?= $row['retail_price']; ?></td>
                <td><?= $row['quantity']; ?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>

</body>
</html>
