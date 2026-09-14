<?php


require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../models/vendor_model.php";

header('Content-Type: application/json');

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
