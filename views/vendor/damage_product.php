<?php
require_once __DIR__ . "/../../controllers/vendor_controller/damage_product_process.php";

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Damage Product - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css">
</head>
<body>

    <?php include __DIR__ . "/../../controllers/vendor_controller/navbar.php"; ?>

    <div class="container">
        <h2>Report Damaged Product</h2>

        <?php if ($flash): ?>
            <section class="summary">
                <p><?= $flash ?></p>
            </section>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
            <label for="product">Product Name / ID</label>
            <input type="text" id="product" name="product" placeholder="Enter product name or ID" value="<?= $product ?>">
            <?php if ($productErr): ?><span class="error"><?= $productErr ?></span><?php endif; ?>

            <label for="damage_qty">Damaged Quantity</label>
            <input type="number" id="damage_qty" name="damage_qty" placeholder="Enter damaged quantity" value="<?= $damage_qty ?>">
            <?php if ($damageQtyErr): ?><span class="error"><?= $damageQtyErr ?></span><?php endif; ?>

            <label for="note">Damage Note (optional)</label>
            <textarea id="note" name="note" rows="3" placeholder="Describe the damage (optional)"><?= $note ?></textarea>
            <?php if ($noteErr): ?><span class="error"><?= $noteErr ?></span><?php endif; ?>

            <button type="submit">Report Damage</button>
        </form>

        <h2>Recent Damage Reports</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Damaged Qty</th>
                <th>Note</th>
                <th>Reported At</th>
            </tr>
            <?php
            require __DIR__ . "/../../config/config.php";
            require __DIR__ . "/../../models/vendor_model.php";
            $reports = get_damage_reports($conn);
            mysqli_close($conn);
            foreach ($reports as $row):
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['damage_qty']; ?></td>
                <td><?= $row['note'] ?: '-'; ?></td>
                <td><?= $row['reported_at']; ?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>

</body>
</html>
