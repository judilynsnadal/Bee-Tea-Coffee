<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check specific user
$u34 = $conn->query("SELECT * FROM userrs WHERE id=34");
echo "User 34 exists: " . ($u34->num_rows > 0 ? "YES" : "NO") . "<br>";

$sql = "SELECT * FROM cart";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}


echo "<h2>All Cart Items in Database</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse; width:100%;'>";
echo "<thead style='background:#f2f2f2;'><tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Image Path</th>
        <th>Created At</th>
      </tr></thead>";
echo "<tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>{$row['price']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['image']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
}
else {
    echo "<tr><td colspan='7'>No items found in cart table.</td></tr>";
}
echo "</tbody></table>";

// Also show recent logs if file exists
$logFile = 'cart_debug.log';
if (file_exists($logFile)) {
    echo "<h3>Recent Debug Log</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
}
?>
