<?php
session_start();
require __DIR__ . "/../../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["id"]) && ctype_digit((string)$_POST["id"])) {
    $id = (int)$_POST["id"];

    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = "Product deleted successfully.";
    } else {
        $_SESSION['flash'] = "Database error: could not delete product.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header('Location: manage_products.php');
exit;
?>
