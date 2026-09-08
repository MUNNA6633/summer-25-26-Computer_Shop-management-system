<?php
session_start();

// variables
$name = "";
$category = "";
$wholesale_price = "";
$retail_price = "";
$quantity = "";

$nameErr = "";
$categoryErr = "";
$wholesaleErr = "";
$retailErr = "";
$quantityErr = "";

$isValid = false;

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Product Name
    if (empty($_POST["name"])) {
        $nameErr = "Product name is required.";
    } else {
        $name = cleanInput($_POST["name"]);
        if (!preg_match("/^[a-zA-Z0-9 \"'.-]+$/", $name)) {
            $nameErr = "Only letters, numbers and spaces allowed.";
        } elseif (strlen($name) < 2) {
            $nameErr = "Name must be at least 2 characters.";
        }
    }

    // Category
    if (empty($_POST["category"])) {
        $categoryErr = "Category is required.";
    } else {
        $category = cleanInput($_POST["category"]);
        if (!preg_match("/^[a-zA-Z ]+$/", $category)) {
            $categoryErr = "Only letters and spaces allowed.";
        }
    }

    // Wholesale Price
    if (empty($_POST["wholesale_price"]) && $_POST["wholesale_price"] !== "0") {
        $wholesaleErr = "Wholesale price is required.";
    } else {
        $wholesale_price = cleanInput($_POST["wholesale_price"]);
        if (!is_numeric($wholesale_price) || $wholesale_price <= 0) {
            $wholesaleErr = "Enter a valid price greater than 0.";
        }
    }

    // Retail Price
    if (empty($_POST["retail_price"]) && $_POST["retail_price"] !== "0") {
        $retailErr = "Retail price is required.";
    } else {
        $retail_price = cleanInput($_POST["retail_price"]);
        if (!is_numeric($retail_price) || $retail_price <= 0) {
            $retailErr = "Enter a valid price greater than 0.";
        } elseif (is_numeric($wholesale_price) && $retail_price < $wholesale_price) {
            $retailErr = "Retail price cannot be less than wholesale price.";
        }
    }

    // Quantity
    if (empty($_POST["quantity"]) && $_POST["quantity"] !== "0") {
        $quantityErr = "Quantity is required.";
    } else {
        $quantity = cleanInput($_POST["quantity"]);
        if (!ctype_digit($quantity)) {
            $quantityErr = "Quantity must be a whole number (0 or more).";
        }
    }

    $isValid = !$nameErr && !$categoryErr && !$wholesaleErr && !$retailErr && !$quantityErr;

    if ($isValid) {
        require __DIR__ . "/../../config/config.php";
        require __DIR__ . "/../../models/vendor_model.php";

        $result = insert_product($conn, $name, $category, $wholesale_price, $retail_price, $quantity);
        mysqli_close($conn);

        if ($result === true) {
            $_SESSION['flash'] = "Product \"$name\" added successfully.";
            header('Location: add_product.php');
            exit;
        } else {
            $isValid = false;
            $nameErr = "Database error: " . $result;
        }
    }
}
?>
