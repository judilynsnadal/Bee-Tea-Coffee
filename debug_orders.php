<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';

$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - UserID: " . $row["user_id"]. " - Product: " . $row["product_name"]. " - Status: " . $row["status"]. "<br>";
    }
} else {
    echo "0 results in orders table";
}
?>
