<?php
require_once __DIR__ . '/../models/admin_model.php';
include 'header.php';

$reviews = get_reviews();
?>

<h2>Customer Reviews</h2>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
            <tr>
                <td colspan="5">No reviews submitted yet.</td>
            </tr>
            <?php else: foreach ($reviews as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['rating']); ?>/5</td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
