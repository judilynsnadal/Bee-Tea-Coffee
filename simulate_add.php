<?php
include 'connect.php';

// Simulate a user session
session_start();
// We need a valid user ID. Let's pick the first one from userrs table.
$user_res = $conn->query("SELECT id FROM userrs LIMIT 1");
if ($user_res->num_rows > 0) {
    $user = $user_res->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    echo "Simulating User ID: " . $user['id'] . "<br>";
}
else {
    die("No users found in database to simulate.<br>");
}

// Prepare data
$data = [
    'name' => 'Test Product',
    'price' => '150.00',
    'image' => 'images/test.png',
    'quantity' => 1
];

// Call the logic from add_to_cart.php (but we can't include it directly because it reads php://input)
// So we replicate the logic here for testing.

$user_id = $_SESSION['user_id'];
$product_name = $data['name'];
$product_price = $data['price'];
$product_image = $data['image'];
$quantity = $data['quantity'];

// Check if item already exists
$sql_check = "SELECT id, quantity FROM shopping_cart WHERE user_id = '$user_id' AND product_name = '$product_name'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    echo "Item exists, updating...<br>";
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $cart_id = $row['id'];
    $sql_update = "UPDATE shopping_cart SET quantity = '$new_quantity' WHERE id = '$cart_id'";
    if ($conn->query($sql_update) === TRUE) {
        echo "Success: Cart updated.<br>";
    }
    else {
        echo "Error updating: " . $conn->error . "<br>";
    }
}
else {
    echo "Item new, inserting...<br>";
    $sql_insert = "INSERT INTO shopping_cart (user_id, product_name, product_price, product_image, quantity) 
                   VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$quantity')";
    if ($conn->query($sql_insert) === TRUE) {
        echo "Success: Item added to cart.<br>";
    }
    else {
        echo "Error adding: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
