<?php
require_once 'config/database.php';

$new_password = 'adminpassword';
$hashed = password_hash($new_password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hashed);

if ($stmt->execute()) {
    echo "Password successfully updated to: <b>adminpassword</b>";
} else {
    echo "Error updating password: " . $conn->error;
}
?>