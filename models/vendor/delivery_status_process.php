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
        require "db.php";

        // Check the order exists first, since affected_rows() would also
        // read as 0 if the status is set to the value it already has
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM deliveries WHERE order_id = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $order_id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) === 0) {
            $isValid = false;
            $orderIdErr = "Order ID not found.";
            mysqli_stmt_close($checkStmt);
            mysqli_close($conn);
        } else {
            mysqli_stmt_close($checkStmt);

            $stmt = mysqli_prepare($conn, "UPDATE deliveries SET status = ? WHERE order_id = ?");
            mysqli_stmt_bind_param($stmt, "ss", $status, $order_id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                $_SESSION['flash'] = "Order \"$order_id\" status updated to $status.";
                header('Location: delivery_status.php');
                exit;
            } else {
                $isValid = false;
                $orderIdErr = "Database error: " . mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }
    }
}
?>
