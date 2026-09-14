<?php
session_start();

// Initialize variables
$order_id = "";
$status = "";

$orderIdErr = "";
$statusErr = "";

$isValid = false;

$statusOptions = ["Pending", "Shipped", "Delivered", "Cancelled"];

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Process form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Order ID / reference
    if (empty($_POST["order_id"])) {
        $orderIdErr = "Order ID is required.";
    } else {
        $order_id = cleanInput($_POST["order_id"]);
        if (!preg_match("/^[a-zA-Z0-9 -]+$/", $order_id)) {
            $orderIdErr = "Only letters, numbers and spaces allowed.";
        }
    }

    // Status
    if (empty($_POST["status"])) {
        $statusErr = "Please select a delivery status.";
    } else {
        $status = cleanInput($_POST["status"]);
        if (!in_array($status, $statusOptions, true)) {
            $statusErr = "Select a valid status from the list.";
        }
    }

    $isValid = !$orderIdErr && !$statusErr;

    // Only update the database once every field passes validation
    if ($isValid) {
        require __DIR__ . "/../../config/config.php";
        require __DIR__ . "/../../models/vendor_model.php";

        // Check the order exists first, since affected_rows() would also
        // read as 0 if the status is set to the value it already has
        if (!delivery_exists($conn, $order_id)) {
            $isValid = false;
            $orderIdErr = "Order ID not found.";
            mysqli_close($conn);
        } else {
            $result = update_delivery_status($conn, $order_id, $status);
            mysqli_close($conn);

            if ($result === true) {
                $_SESSION['flash'] = "Order \"$order_id\" status updated to $status.";
                header('Location: delivery_status.php');
                exit;
            } else {
                $isValid = false;
                $orderIdErr = "Database error: " . $result;
            }
        }
    }
}
?>
