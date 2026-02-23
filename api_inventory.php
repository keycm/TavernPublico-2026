<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'get_items') {
    $result = mysqli_query($link, "SELECT * FROM inventory_items ORDER BY item_name");
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'add_item') {
        $name = mysqli_real_escape_string($link, $input['name']);
        $unit = mysqli_real_escape_string($link, $input['unit']);
        $cost = floatval($input['cost']);
        $stock = intval($input['stock']);

        $sql = "INSERT INTO inventory_items (item_name, unit, cost_per_unit, current_stock) VALUES ('$name', '$unit', $cost, $stock)";
        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Item added']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($link)]);
        }
    }

    if ($action === 'update_stock') {
        $id = intval($input['id']);
        $qty = intval($input['quantity']); // Can be negative for usage

        $sql = "UPDATE inventory_items SET current_stock = current_stock + $qty WHERE inventory_id = $id";
        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Stock updated']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($link)]);
        }
    }
    exit;
}
?>
