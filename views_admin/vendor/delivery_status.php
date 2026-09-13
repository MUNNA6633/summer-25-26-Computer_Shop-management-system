<?php
require_once __DIR__ . "/../../controllers/vendor_controller/delivery_status_process.php";

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Delivery Status - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css">
</head>
<body>

    <?php include __DIR__ . "/../../controllers/vendor_controller/navbar.php"; ?>

    <div class="container">
        <h2>Update Delivery Status</h2>

        <?php if ($flash): ?>
            <section class="summary">
                <p><?= $flash ?></p>
            </section>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
            <label for="order_id">Order ID</label>
            <input type="text" id="order_id" name="order_id" placeholder="Enter order ID" value="<?= $order_id ?>">
            <?php if ($orderIdErr): ?><span class="error"><?= $orderIdErr ?></span><?php endif; ?>

            <label for="status">Delivery Status</label>
            <select id="status" name="status">
                <option value="" disabled <?= ($status === "") ? "selected" : "" ?>>Select status...</option>
                <?php foreach ($statusOptions as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($status === $opt) ? "selected" : "" ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($statusErr): ?><span class="error"><?= $statusErr ?></span><?php endif; ?>

            <button type="submit">Update Status</button>
        </form>

        <h2>Delivery Records</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Status</th>
                <th>Last Updated</th>
            </tr>
            <?php
            require __DIR__ . "/../../config/config.php";
            require __DIR__ . "/../../models/vendor_model.php";
            $deliveries = get_deliveries($conn);
            mysqli_close($conn);
            foreach ($deliveries as $row):
                $cls = "status-pending";
                if ($row['status'] === "Shipped") $cls = "status-shipped";
                if ($row['status'] === "Delivered") $cls = "status-delivered";
                if ($row['status'] === "Cancelled") $cls = "status-damaged";
            ?>
            <tr>
                <td><?= $row['order_id']; ?></td>
                <td><span class="<?= $cls; ?>"><?= $row['status']; ?></span></td>
                <td><?= $row['updated_at']; ?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>

</body>
</html>
