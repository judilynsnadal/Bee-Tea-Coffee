<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['cart_id']) || !isset($data['size'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit();
}

$cart_id = (int)$data['cart_id'];
$size = $conn->real_escape_string($data['size']);
$user_id = $_SESSION['user_id'];

// Verify the cart item belongs to the user
$check_sql = "SELECT id FROM cart WHERE id = $cart_id AND user_id = $user_id";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows > 0) {
    $update_sql = "UPDATE cart SET size = '$size' WHERE id = $cart_id";
    if ($conn->query($update_sql) === TRUE) {
        echo json_encode(['success' => true]);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Item not found in your cart']);
}

$conn->close();
?>
