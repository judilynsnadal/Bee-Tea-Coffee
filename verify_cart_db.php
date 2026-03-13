<?php
include 'connect.php';

echo "Database: $database\n";

// 1. Check Table
$check = $conn->query("SHOW TABLES LIKE 'shopping_cart'");
if ($check->num_rows == 0) {
    die("Table shopping_cart does not exist.\n");
}
echo "Table shopping_cart exists.\n";

// 2. Insert dummy
$uid = 1;
$sql = "INSERT INTO shopping_cart (user_id, product_name, product_price, quantity) VALUES ($uid, 'Debug Item', 123.45, 1)";
if ($conn->query($sql)) {
    echo "Inserted debug item.\n";
    $id = $conn->insert_id;
}
else {
    die("Insert failed: " . $conn->error . "\n");
}

// 3. Select
$res = $conn->query("SELECT * FROM shopping_cart WHERE id = $id");
if ($row = $res->fetch_assoc()) {
    echo "Retrieved item: " . $row['product_name'] . "\n";
}
else {
    echo "Failed to retrieve item.\n";
}

// 4. Delete
$conn->query("DELETE FROM shopping_cart WHERE id = $id");
echo "Deleted debug item.\n";

echo "DB Verification Passed.\n";
?>
