<?php
require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if (!empty($customer_name) && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (customer_name, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $customer_name, $rating, $comment);

        if ($stmt->execute()) {
            $message = "Thank you! Your review has been saved successfully.";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Customer Review</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="public-nav">
        <h2 style="color: white;">Tech Computer Shop</h2>
        <div>
            <a href="index.php">Home</a>
            <a href="submit_review.php">Leave Review</a>
            <a href="admin/login.php">Admin Login</a>
        </div>
    </nav>

    <div class="public-container" style="max-width: 500px;">
        <div class="card">
            <h2>Write a Customer Review</h2>
            <?php if ($message): ?>
                <p style="margin: 1rem 0; color: #2563eb; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="submit_review.php" method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Rating (1 to 5)</label>
                    <input type="number" name="rating" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label>Review / Comment</label>
                    <textarea name="comment" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>
    </div>
</body>
</html>