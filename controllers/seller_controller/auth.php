<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Checks the SAME session that the one login page (root index.php) sets.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller') {
    header('Location: ../../index.php');
    exit;
}
