<?php
include 'connect.php';

$sql = "SELECT id, product_name, product_image FROM shopping_cart WHERE user_id = 34";
$result = $conn->query($sql);

echo "<h3>Cart Items for User 34</h3>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $img_path = $row['product_image'];
        $full_path = __DIR__ . '/' . $img_path; // Absolute path check

        echo "Item: <b>" . $row['product_name'] . "</b><br>";
        echo "DB Path: " . $img_path . "<br>";

        if (file_exists($full_path)) {
            echo "Status: <span style='color:green'>Found</span><br>";
        }
        else {
            echo "Status: <span style='color:red'>MISSING</span> ( Checked: $full_path )<br>";
        }
        echo "<hr>";
    }
}
else {
    echo "Cart is empty.";
}
?>
