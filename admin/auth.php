<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Checks the SAME session that the one login page (root index.php) sets,
// rather than a separate admin-only login system.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>