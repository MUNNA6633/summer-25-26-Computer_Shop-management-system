<?php
// ================================================================
// CONTROLLER: AJAX / JSON endpoints
// The dashboard pages call these with fetch() and redraw a table
// without reloading the page.
//
// All SQL lives in models/vendor_model.php - this file only
// routes requests to those functions and returns JSON. No raw SQL
// should ever be added here.
//
// NOTE: schema.sql has no `users` table yet, so there is no login
// system in this project. Because of that, this controller has NO
// role checks (there is no admin/vendor/etc. to check against). Once
// a `users` table + login exist, add an is_logged_in() / role check
// at the top of this function the same way the reference example
// does, before opening this up on a real server.
// ================================================================

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../models/vendor_model.php";

header('Content-Type: application/json');

/* ---------- small helper so every branch below can stay one line ---------- */
function json_out($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$action = $_GET['action'] ?? '';
$term   = trim($_GET['q'] ?? '');

switch ($action) {

    /* ----------- Products ----------- */
    case 'search_products':
        json_out($term === ''
            ? get_products($conn)
            : search_products($conn, $term));

    case 'low_stock':
        json_out(get_low_stock_products($conn, LOW_STOCK));

    /* ----------- Damage reports ----------- */
    case 'search_damage':
        json_out($term === ''
            ? get_damage_reports($conn)
            : search_damage_reports($conn, $term));

    /* ----------- Deliveries ----------- */
    case 'search_deliveries':
        json_out($term === ''
            ? get_deliveries($conn)
            : search_deliveries($conn, $term));

    /* ----------- Dashboard stats ----------- */
    case 'stats':
        json_out(get_system_stats($conn));
}

// Action missing, or an action that does not exist.
json_out(['error' => 'Unknown action.'], 400);
