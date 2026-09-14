<?php
require_once __DIR__ . '/../models/admin_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_announcement'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        add_announcement($title, $content);
        header('Location: announcements.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    delete_announcement(intval($_GET['delete']));
    header('Location: announcements.php');
    exit;
}

include 'header.php';
$announcements = get_announcements();
?>

<h2>Manage Announcements</h2>

<div class="card">
    <h3>Post Announcement to Frontend</h3>
    <form action="announcements.php" method="POST">
        <div class="form-group">
            <input type="text" name="title" placeholder="Announcement Title" required>
        </div>
        <div class="form-group">
            <textarea name="content" placeholder="Content text..." rows="4" required></textarea>
        </div>
        <button type="submit" name="post_announcement" class="btn">Post Announcement</button>
    </form>
</div>

<div class="card">
    <h3>Current Announcements</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Content</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($announcements)): ?>
            <tr>
                <td colspan="4">No announcements yet.</td>
            </tr>
            <?php else: foreach ($announcements as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['content']); ?></td>
                <td>
                    <a href="announcements.php?delete=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Delete this announcement?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
