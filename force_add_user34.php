<?php
include 'connect.php';

$user_id = 34; // The user from the screenshot
$product_name = "Manual Added Item";
$product_price = 120.00;
$product_image = "images/latte.png"; // ensuring a valid-ish path
$quantity = 1;

echo "Simulating add for User $user_id...<br>";

// Check existing
$sql_check = "SELECT id, quantity FROM shopping_cart WHERE user_id = '$user_id' AND product_name = '$product_name'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_q = $row['quantity'] + 1;
    $id = $row['id'];
    $conn->query("UPDATE shopping_cart SET quantity = $new_q WHERE id = $id");
    echo "Updated quantity for item $id.<br>";
}
else {
    $sql_insert = "INSERT INTO shopping_cart (user_id, product_name, product_price, product_image, quantity) 
                   VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$quantity')";
    if ($conn->query($sql_insert)) {
        echo "Inserted new item.<br>";
    }
    else {
        echo "Error: " . $conn->error . "<br>";
    }
}

// Verify count
$res = $conn->query("SELECT COUNT(*) as c FROM shopping_cart WHERE user_id = '$user_id'");
$row = $res->fetch_assoc();
echo "Total items for User $user_id: " . $row['c'] . "<br>";
?>
