<?php
require 'connect.php';

$sql = "SELECT id, product_name FROM orders";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $id = $row['id'];
        $name = strtoupper($row['product_name']);

        $imgPath = 'images/' . $name . '.png';

        // Some special cases for missing specific image names
        if (strpos(strtolower($name), 'milk tea') !== false) {
            $imgPath = 'images/BTC_1.jpg';
        }

        $conn->query("UPDATE orders SET image = '$imgPath' WHERE id = '$id' AND (image IS NULL OR image = '')");
    }
    echo "Orders backfilled successfully.";
}
else {
    echo "Error updating orders: " . $conn->error;
}
?>
