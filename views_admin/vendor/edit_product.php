<?php require_once __DIR__ . "/../../controllers/vendor_controller/edit_product_process.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css">
</head>
<body>

    <?php include __DIR__ . "/../../controllers/vendor_controller/navbar.php"; ?>

    <div class="container">
        <h2>Edit Product</h2>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?= $id ?>" novalidate>
            <input type="hidden" name="id" value="<?= $id ?>">

            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="<?= $name ?>">
            <?php if ($nameErr): ?><span class="error"><?= $nameErr ?></span><?php endif; ?>

            <label for="category">Category</label>
            <input type="text" id="category" name="category" value="<?= $category ?>">
            <?php if ($categoryErr): ?><span class="error"><?= $categoryErr ?></span><?php endif; ?>

            <label for="wholesale_price">Wholesale Price (Tk)</label>
            <input type="number" step="0.01" id="wholesale_price" name="wholesale_price" value="<?= $wholesale_price ?>">
            <?php if ($wholesaleErr): ?><span class="error"><?= $wholesaleErr ?></span><?php endif; ?>

            <label for="retail_price">Retail Price (Tk)</label>
            <input type="number" step="0.01" id="retail_price" name="retail_price" value="<?= $retail_price ?>">
            <?php if ($retailErr): ?><span class="error"><?= $retailErr ?></span><?php endif; ?>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="<?= $quantity ?>">
            <?php if ($quantityErr): ?><span class="error"><?= $quantityErr ?></span><?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>

        <p><a href="manage_products.php">&larr; Back to Manage Products</a></p>
    </div>

</body>
</html>
