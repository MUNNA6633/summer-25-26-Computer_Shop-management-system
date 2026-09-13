<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {
    $name = trim($_POST['vendor_name']);
    $email = trim($_POST['contact_email']);
    $phone = trim($_POST['phone']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO vendors (vendor_name, contact_email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $phone);
        $stmt->execute();
        header('Location: vendors.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM vendors WHERE id = $id");
    header('Location: vendors.php');
    exit;
}

include 'header.php';
$vendors = $conn->query("SELECT * FROM vendors ORDER BY id DESC");
?>

<h2>Manage Vendors</h2>

<div class="card">
    <h3>Add New Vendor</h3>
    <form action="vendors.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; margin-top: 1rem;">
        <input type="text" name="vendor_name" placeholder="Vendor Name" required>
        <input type="email" name="contact_email" placeholder="Contact Email" required>
        <input type="text" name="phone" placeholder="Phone Number">
        <button type="submit" name="add_vendor" class="btn">Add Vendor</button>
    </form>
</div>

<div class="card">
    <h3>Vendor Records</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $vendors->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><span style="background: #22c55e; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem;"><?php echo $row['status']; ?></span></td>
                <td>
                    <a href="vendors.php?delete=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Delete this vendor?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>