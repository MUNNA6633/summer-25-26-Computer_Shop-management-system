<?php
session_start();

// variables
$product = "";
$damage_qty = "";
$note = "";

$productErr = "";
$damageQtyErr = "";
$noteErr = "";

$isValid = false;

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Product
    if (empty($_POST["product"])) {
        $productErr = "Product name or ID is required.";
    } else {
        $product = cleanInput($_POST["product"]);
        if (!preg_match("/^[a-zA-Z0-9 \"'.-]+$/", $product)) {
            $productErr = "Only letters, numbers and spaces allowed.";
        }
    }

    // Damaged Quantity
    if (empty($_POST["damage_qty"]) && $_POST["damage_qty"] !== "0") {
        $damageQtyErr = "Damaged quantity is required.";
    } else {
        $damage_qty = cleanInput($_POST["damage_qty"]);
        if (!ctype_digit($damage_qty)) {
            $damageQtyErr = "Enter a whole number (0 or more).";
        } elseif ((int)$damage_qty < 1) {
            $damageQtyErr = "Damaged quantity must be at least 1.";
        }
    }

    // Note (optional)
    if (!empty($_POST["note"])) {
        $note = cleanInput($_POST["note"]);
        if (strlen($note) > 200) {
            $noteErr = "Note must be under 200 characters.";
        }
    }

    $isValid = !$productErr && !$damageQtyErr && !$noteErr;

    if ($isValid) {
        require "db.php";

        $damage_qty_int = (int)$damage_qty;

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO damage_reports (product_name, damage_qty, note) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sis", $product, $damage_qty_int, $note);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            $_SESSION['flash'] = "Damage report for \"$product\" saved successfully.";
            header('Location: damage_product.php');
            exit;
        } else {
            $isValid = false;
            $productErr = "Database error: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        }
    }
}
?>
