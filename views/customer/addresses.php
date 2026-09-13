<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/customer_model.php';
if (!isset($_SESSION['customer_id'])) { header('Location: login.php'); exit; }
$cid = $_SESSION['customer_id'];
$addresses = get_addresses($cid);
$msg = $_SESSION['addr_msg'] ?? ''; unset($_SESSION['addr_msg']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Addresses</title>
    <link rel="stylesheet" href="../../assets/customer_style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>My Delivery Addresses</h2>
    <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <?php if (empty($addresses)): ?>
        <p>No addresses saved yet.</p>
    <?php else: ?>
        <div class="address-grid">
        <?php foreach ($addresses as $a): ?>
            <div class="address-card">
                <h4><?= htmlspecialchars($a['label']) ?></h4>
                <p><strong><?= htmlspecialchars($a['full_name']) ?></strong></p>
                <p><?= htmlspecialchars($a['phone']) ?></p>
                <p><?= htmlspecialchars($a['address_line']) ?>, <?= htmlspecialchars($a['city']) ?> - <?= htmlspecialchars($a['postal_code']) ?></p>
                <form action="../../controllers/customer_controller/delete_address_process.php" method="post"
                      onsubmit="return confirm('Delete this address?');" style="margin-top:8px;">
                    <input type="hidden" name="address_id" value="<?= $a['id'] ?>">
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3>Add New Address</h3>
    <form action="../../controllers/customer_controller/add_address_process.php" method="post" class="form">
        <label>Label (Home / Office) <input type="text" name="label" required></label>
        <label>Full Name <input type="text" name="full_name" required></label>
        <label>Phone <input type="text" name="phone" required></label>
        <label>Address Line <input type="text" name="address_line" required></label>
        <label>City <input type="text" name="city" required></label>
        <label>Postal Code <input type="text" name="postal_code"></label>
        <button type="submit" class="btn-primary">Save Address</button>
    </form>
</div>
</body>
</html>