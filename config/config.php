<?php
// ================================================================
// CONFIG - database connection, session setup and app settings
// Everything in the project starts from here.
// ================================================================

/* ---------- 1. Database settings (change if your XAMPP differs) ---------- */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vendor_system');

/* ---------- 2. App settings ---------- */
define('APP_NAME',   'VendorSys');
define('CURRENCY',   '$');
define('LOW_STOCK',  5);    // a product at or below this quantity is "low stock"
define('SESSION_TIMEOUT', 1800); // auto logout after 30 minutes of no activity

/* ---------- 3. Start a hardened session ---------- */
// SECURITY: the cookie cannot be read by JavaScript (httponly) and is not
// sent on cross-site requests (samesite), which blocks most XSS/CSRF tricks.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ---------- 4. Connect to MySQL (procedural mysqli) ---------- */
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die('Database connection failed. Did you import schema.sql? Details: '
        . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

/* ---------- 5. No user/admin table yet ---------- */
// NOTE: schema.sql currently only defines `products`, `damage_reports`,
// and `deliveries` — there is no `users` table, so this project has no
// login system yet. Once a `users` table exists (id, name, email, contact,
// username, password, role), a block can be added here to auto-create a
// default admin the first time the app runs, the same way this config
// bootstraps everything else.
