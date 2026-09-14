<?php
require_once '../../controllers/seller_controller/auth.php';
require_once '../../models/seller_model.php';

$error = '';
$statusOptions = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];

// Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $address_id = intval($_POST['address_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';

    if ($address_id <= 0 || $product_id <= 0) {
        $error = 'Choose a customer address and a product.';
    } elseif ($quantity < 1) {
        $error = 'Quantity must be at least 1.';
    } elseif (!in_array($payment_method, ['cod', 'card', 'bkash', 'nagad'], true)) {
        $error = 'Choose a payment method.';
    } else {
        $result = create_order($address_id, $product_id, $quantity, $payment_method);
        if (is_numeric($result)) {
            header('Location: orders.php?created=1');
            exit;
        }
        $error = $result;
    }
}

// Update (order status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if ($order_id > 0 && in_array($status, $statusOptions, true)) {
        update_order_status($order_id, $status);
    }
    header('Location: orders.php?updated=1');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    delete_order(intval($_GET['delete']));
    header('Location: orders.php?deleted=1');
    exit;
}

$orders = get_all_orders();
$addresses = get_addresses_for_order_form();
$products = get_products_for_order_form();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order List - Seller</title>
    <link rel="stylesheet" href="../../assets/seller_style.css">
</head>
<body>

    <a href="../../controllers/logout.php" class="logout-btn" style="color:#fff; text-decoration:none;">Logout</a>

    <?php include '../../controllers/seller_controller/navbar.php'; ?>

    <div class="container">
        <h2>Create Order</h2>

        <?php if (isset($_GET['created'])): ?>
            <p style="color:#0f766e; font-weight:bold;">Order created.</p>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <p style="color:#0f766e; font-weight:bold;">Order status updated.</p>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <p style="color:#0f766e; font-weight:bold;">Order deleted.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color:#c0392b; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if (empty($addresses)): ?>
            <p>No customer addresses on file yet, so a manual order can't be created - ask the customer to save a delivery address first.</p>
        <?php elseif (empty($products)): ?>
            <p>No products yet - ask a vendor to add stock first.</p>
        <?php else: ?>
        <form method="post" action="orders.php">
            <label for="address_id">Customer / Delivery Address</label>
            <select id="address_id" name="address_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($addresses as $a): ?>
                <option value="<?= $a['address_id'] ?>">
                    <?= htmlspecialchars($a['customer_name']) ?> - <?= htmlspecialchars($a['label']) ?>, <?= htmlspecialchars($a['city']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="product_id">Product</label>
            <select id="product_id" name="product_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['name']) ?> - Tk <?= number_format($p['retail_price'] * (1 - $p['discount_percent'] / 100), 2) ?>
                    (<?= $p['quantity'] ?> in stock)
                </option>
                <?php endforeach; ?>
            </select>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" value="1" required>

            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
                <option value="cod">Cash on Delivery</option>
                <option value="card">Credit / Debit Card</option>
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
            </select>

            <button type="submit" name="create_order">Create Order</button>
        </form>
        <?php endif; ?>

        <h2>All Orders</h2>

        <table>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment</th>
                <th>Payment Status</th>
                <th>Total</th>
                <th>Order Status</th>
                <th>Placed</th>
                <th>Action</th>
            </tr>
            <?php if (empty($orders)): ?>
            <tr><td colspan="9">No orders placed yet.</td></tr>
            <?php else: foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td><?= $o['item_count'] ?></td>
                <td><?= htmlspecialchars(strtoupper($o['payment_method'])) ?></td>
                <td><?= htmlspecialchars($o['payment_status']) ?></td>
                <td>Tk <?= number_format($o['total'], 2) ?></td>
                <td>
                    <form class="inline-form" method="post" action="orders.php">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <select name="status">
                            <?php foreach ($statusOptions as $s): ?>
                            <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status">Save</button>
                    </form>
                </td>
                <td><?= htmlspecialchars($o['created_at']) ?></td>
                <td>
                    <a href="orders.php?delete=<?= $o['id'] ?>" onclick="return confirm('Delete order #<?= $o['id'] ?>? This also removes its items and payment record.')" style="color:#c0392b; font-weight:bold;">Delete</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </table>
    </div>

</body>
</html>
