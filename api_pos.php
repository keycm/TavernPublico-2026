<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check admin/manager access (optional for POS if staff uses it, but strict for now)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'get_menu') {
    // Fetch active menu items
    $sql = "SELECT id, name, category, price, stock_quantity, image FROM menu WHERE deleted_at IS NULL ORDER BY category, name";
    $result = mysqli_query($link, $sql);
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout') {
    $input = json_decode(file_get_contents('php://input'), true);
    $cart = $input['cart'] ?? [];
    $payment_method = $input['payment_method'] ?? 'Cash';

    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }

    mysqli_begin_transaction($link);

    try {
        // 1. Calculate Total & Validate Stock
        $total_amount = 0;
        foreach ($cart as $item) {
            $id = intval($item['id']);
            $qty = intval($item['quantity']);

            $res = mysqli_query($link, "SELECT price, stock_quantity FROM menu WHERE id = $id FOR UPDATE");
            $product = mysqli_fetch_assoc($res);

            if (!$product) throw new Exception("Product ID $id not found");
            if ($product['stock_quantity'] < $qty) throw new Exception("Insufficient stock for product ID $id");

            $total_amount += $product['price'] * $qty;
        }

        // 2. Create Order
        $stmt = mysqli_prepare($link, "INSERT INTO pos_orders (total_amount, payment_method) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ds", $total_amount, $payment_method);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($link);

        // 3. Insert Items & Deduct Stock
        $stmt_item = mysqli_prepare($link, "INSERT INTO pos_order_items (order_id, menu_item_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
        $stmt_stock = mysqli_prepare($link, "UPDATE menu SET stock_quantity = stock_quantity - ? WHERE id = ?");

        foreach ($cart as $item) {
            $id = intval($item['id']);
            $qty = intval($item['quantity']);

            // Get price again (optimization: reuse from step 1)
            $res = mysqli_query($link, "SELECT price FROM menu WHERE id = $id");
            $product = mysqli_fetch_assoc($res);
            $price = $product['price'];

            mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $id, $qty, $price);
            mysqli_stmt_execute($stmt_item);

            mysqli_stmt_bind_param($stmt_stock, "ii", $qty, $id);
            mysqli_stmt_execute($stmt_stock);
        }

        // 4. Update Financial Batch (1st Batch Logic)
        // Find active batch
        $batch_res = mysqli_query($link, "SELECT batch_id FROM financial_batches WHERE status = 'Active' LIMIT 1");
        if ($batch = mysqli_fetch_assoc($batch_res)) {
            $batch_id = $batch['batch_id'];
            $stmt_batch = mysqli_prepare($link, "UPDATE financial_batches SET collected_amount = collected_amount + ? WHERE batch_id = ?");
            mysqli_stmt_bind_param($stmt_batch, "di", $total_amount, $batch_id);
            mysqli_stmt_execute($stmt_batch);
        }

        mysqli_commit($link);
        echo json_encode(['success' => true, 'message' => 'Order completed', 'order_id' => $order_id]);

    } catch (Exception $e) {
        mysqli_rollback($link);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
