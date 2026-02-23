<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'get_status') {
    // Get Active Batch
    $result = mysqli_query($link, "SELECT * FROM financial_batches WHERE status = 'Active' ORDER BY created_at DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'batch' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No active batch']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'update_target') {
        $target = floatval($input['target']);
        // Update the *active* batch
        $sql = "UPDATE financial_batches SET target_amount = $target WHERE status = 'Active'";
        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Target updated']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($link)]);
        }
    }

    if ($action === 'create_batch') {
        $name = mysqli_real_escape_string($link, $input['name']);
        $target = floatval($input['target']);

        // Deactivate old active batches? Or allow multiple? Usually strict sequential.
        mysqli_query($link, "UPDATE financial_batches SET status = 'Completed' WHERE status = 'Active'");

        $sql = "INSERT INTO financial_batches (batch_name, target_amount, status) VALUES ('$name', $target, 'Active')";
        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'New Batch Created']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($link)]);
        }
    }
    exit;
}
?>
