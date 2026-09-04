<?php
require_once "delivery_status_process.php";

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Delivery Status - Vendor System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

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
            require "db.php";
            $result = mysqli_query($conn, "SELECT * FROM deliveries ORDER BY updated_at DESC");
            while ($row = mysqli_fetch_assoc($result)):
                $cls = "status-pending";
                if ($row['status'] === "Shipped") $cls = "status-shipped";
                if ($row['status'] === "Delivered") $cls = "status-delivered";
            ?>
            <tr>
                <td><?= $row['order_id']; ?></td>
                <td><span class="<?= $cls; ?>"><?= $row['status']; ?></span></td>
                <td><?= $row['updated_at']; ?></td>
            </tr>
            <?php
            endwhile;
            mysqli_free_result($result);
            mysqli_close($conn);
            ?>
        </table>
    </div>

</body>
</html>
