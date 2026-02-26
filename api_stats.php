<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'sales_overview') {
    // Get daily sales for last 30 days
    $sql = "SELECT DATE(created_at) as sale_date, SUM(total_amount) as daily_total
            FROM pos_orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY sale_date
            ORDER BY sale_date ASC";
    $result = mysqli_query($link, $sql);
    $dates = [];
    $totals = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dates[] = $row['sale_date'];
        $totals[] = floatval($row['daily_total']);
    }
    echo json_encode(['success' => true, 'labels' => $dates, 'data' => $totals]);
    exit;
}

if ($action === 'top_items') {
    // Top items by quantity sold
    $sql = "SELECT m.name, SUM(oi.quantity) as total_qty
            FROM pos_order_items oi
            JOIN menu m ON oi.menu_item_id = m.id
            GROUP BY m.id
            ORDER BY total_qty DESC
            LIMIT 5";
    $result = mysqli_query($link, $sql);
    $items = [];
    $counts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row['name'];
        $counts[] = intval($row['total_qty']);
    }
    echo json_encode(['success' => true, 'labels' => $items, 'data' => $counts]);
    exit;
}

if ($action === 'stock_levels') {
    // Get items with stock < reorder level (Low Stock)
    // Or just all menu items stock for bar chart
    $sql = "SELECT name, stock_quantity FROM menu WHERE stock_quantity < 20 ORDER BY stock_quantity ASC LIMIT 10";
    $result = mysqli_query($link, $sql);
    $labels = [];
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['name'];
        $data[] = intval($row['stock_quantity']);
    }
    echo json_encode(['success' => true, 'labels' => $labels, 'data' => $data]);
    exit;
}
?>
