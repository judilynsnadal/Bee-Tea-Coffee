<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connect.php';

echo "<h3>Database: " . $database . "</h3>";

// Check Table Existence
$check = $conn->query("SHOW TABLES LIKE 'cart'");
if ($check->num_rows > 0) {
    echo "Table 'cart' EXISTS.<br>";
}
else {
    echo "Table 'cart' DOES NOT EXIST.<br>";
}

// Show Content
echo "<h4>Contents:</h4>";
$sql = "SELECT * FROM cart";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1' cellspacing='0' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Product</th><th>Price</th><th>Qty</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['product_name'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    else {
        echo "Table is empty (0 rows).<br>";
    }
}
else {
    echo "Error querying table: " . $conn->error . "<br>";
}

echo "<h4>Session Info:</h4>";
session_start();
print_r($_SESSION);
?>
