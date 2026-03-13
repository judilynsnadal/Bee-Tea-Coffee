<?php
require "connect.php";
header('Content-Type: application/json');

$query = "SELECT id, product_id, name, price, stock, size, image FROM products ORDER BY id DESC";
$result = $conn->query($query);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode(["success" => true, "data" => $products]);
}
else {
    echo json_encode(["success" => false, "message" => "Failed to fetch products: " . $conn->error]);
}
?>
