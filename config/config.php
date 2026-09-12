<?php



define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vendor_system');


define('APP_NAME',   'VendorSys');
define('CURRENCY',   '$');
define('LOW_STOCK',  5);   
define('SESSION_TIMEOUT', 1800); 


if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die('Database connection failed. Did you import schema.sql? Details: '
        . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

