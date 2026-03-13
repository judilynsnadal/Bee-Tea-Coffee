<?php
include 'connect.php';

$user_id = 34;
// Set a valid image that definitely exists
$valid_image = "images/MOCHA.png";


echo "Fixing images for User $user_id...<br>";

$sql = "UPDATE shopping_cart SET product_image = '$valid_image' WHERE user_id = '$user_id'";

if ($conn->query($sql) === TRUE) {
    echo "Updated images successfully. Rows affected: " . $conn->affected_rows . "<br>";
}
else {
    echo "Error updating record: " . $conn->error . "<br>";
}

// Verify
$result = $conn->query("SELECT product_name, product_image FROM shopping_cart WHERE user_id = '$user_id'");
while ($row = $result->fetch_assoc()) {
    echo "Item: " . $row['product_name'] . " | Image: " . $row['product_image'] . "<br>";
}

$conn->close();
?>
