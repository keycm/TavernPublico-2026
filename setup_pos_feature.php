<?php
// Run this script once to set up the POS database tables
require_once 'db_connect.php';

echo "<h2>Setting up POS Database...</h2>";

$schema_sql = file_get_contents('pos_schema.sql');
if (!$schema_sql) die("Error: pos_schema.sql not found.");

// Run schema
if (mysqli_multi_query($link, $schema_sql)) {
    do { if ($result = mysqli_store_result($link)) mysqli_free_result($result); } while (mysqli_next_result($link));
    echo "Tables created.<br>";
} else {
    echo "Schema Error: " . mysqli_error($link) . "<br>";
}

// Add columns to menu
$cols = ['stock_quantity' => 'INT(11) NOT NULL DEFAULT 0', 'cost_price' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00'];
foreach ($cols as $col => $def) {
    $check = mysqli_query($link, "SHOW COLUMNS FROM menu LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($link, "ALTER TABLE menu ADD COLUMN $col $def");
        echo "Added column $col.<br>";
    }
}

// Default Batch
$check_batch = mysqli_query($link, "SELECT * FROM financial_batches WHERE batch_name = '1st Batch'");
if (mysqli_num_rows($check_batch) == 0) {
    mysqli_query($link, "INSERT INTO financial_batches (batch_name, target_amount, status) VALUES ('1st Batch', 10000.00, 'Active')");
    echo "Created default 1st Batch.<br>";
}

echo "<h3>Setup Complete!</h3>";
echo "<a href='pos_dashboard.php'>Go to POS Dashboard</a>";
?>
