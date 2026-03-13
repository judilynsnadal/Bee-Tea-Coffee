<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "btc");

    // Fetch all cart items
    $sql = "SELECT * FROM shopping_cart ORDER BY id DESC";
    $result = $conn->query($sql);

    echo "<html><head><style>
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
          </style></head><body>";

    echo "<h2>All Cart Items in Database</h2>";
    echo "<table>";
    echo "<thead><tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Image Path</th>
            <th>Created At</th>
          </tr></thead>";
    echo "<tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>" . $row['product_price'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['product_image'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
    }
    else {
        echo "<tr><td colspan='7'>No items found in cart table.</td></tr>";
    }
    echo "</tbody></table>";

    // Also show total users for context
    $users = $conn->query("SELECT count(*) as c FROM userrs")->fetch_assoc();
    echo "<p>Total Registered Users: " . $users['c'] . "</p>";

    echo "</body></html>";


}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
