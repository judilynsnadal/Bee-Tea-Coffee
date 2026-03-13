<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'debug_user_id' => 'Guest']);
    exit();
}

$user_id = $_SESSION['user_id'];

// --- TEMPORARY DEBUG LOGGING ---
$logFile = __DIR__ . '/cart_temp_debug.log';
function logCartDb($msg)
{
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND);
}

logCartDb("=== NEW REQUEST === User ID: " . $user_id);
logCartDb("Raw Input: " . file_get_contents("php://input"));
// --------------------------------

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    logCartDb("Failed: Invalid JSON data received");
    echo json_encode(['success' => false, 'message' => 'Invalid data', 'debug_user_id' => $user_id]);
    exit();
}

$product_name = $conn->real_escape_string($data['name']);
$product_price = $conn->real_escape_string($data['price']);
$product_image = $conn->real_escape_string($data['image']);
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

// Check if item already exists in cart for this user
$sql_check = "SELECT id, quantity FROM cart WHERE user_id = '$user_id' AND product_name = '$product_name'";
$result = $conn->query($sql_check);

if (!$result) {
    logCartDb("Check Query Failed! SQL: " . $sql_check . " Error: " . $conn->error);
}
else {
    logCartDb("Check Query Success: " . $result->num_rows . " rows found.");
}

if ($result->num_rows > 0) {
    // Update quantity
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $cart_id = $row['id'];
    $sql_update = "UPDATE cart SET quantity = '$new_quantity' WHERE id = '$cart_id'";
    if ($conn->query($sql_update) === TRUE) {
        logCartDb("Update Success: Quantity set to $new_quantity for Cart ID $cart_id");
        echo json_encode(['success' => true, 'message' => 'Cart updated', 'debug_user_id' => $user_id]);
    }
    else {
        logCartDb("Update Failed! SQL: " . $sql_update . " Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error updating cart: ' . $conn->error, 'debug_user_id' => $user_id]);
    }
}
else {
    // Insert new item
    $sql_insert = "INSERT INTO cart (user_id, product_name, price, image, quantity) 
                   VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$quantity')";
    if ($conn->query($sql_insert) === TRUE) {
        $last_id = $conn->insert_id;
        logCartDb("Insert Success! New Cart ID: $last_id");
        echo json_encode(['success' => true, 'message' => 'Item added to cart', 'debug_user_id' => $user_id, 'cart_id' => $last_id]);
    }
    else {
        logCartDb("Insert Failed! SQL: " . $sql_insert . " Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error adding item: ' . $conn->error, 'debug_user_id' => $user_id]);
    }
}

$conn->close();
?>
