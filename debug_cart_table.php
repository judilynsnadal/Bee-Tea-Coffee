<?php
// debug_cart_table.php

include 'connect.php';

echo "<h2>Inspecting table: cart</h2>";

// Check if table exists
$check = $conn->query("SHOW TABLES LIKE 'cart'");
if ($check->num_rows == 0) {
    die("Table 'cart' does not exist.");
}

// Show columns
$result = $conn->query("SHOW COLUMNS FROM cart");
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No columns found.";
}

// Show first 5 rows to see data sample
echo "<h3>Data Sample (first 5 rows)</h3>";
$data = $conn->query("SELECT * FROM cart LIMIT 5");
if ($data->num_rows > 0) {
    while($row = $data->fetch_assoc()) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
} else {
    echo "Table is empty.";
}

$conn->close();
?>
