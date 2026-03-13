<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart_id']) || (!isset($data['change']) && !isset($data['new_value']))) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$cart_id = $conn->real_escape_string($data['cart_id']);

// Get current quantity
$sql = "SELECT quantity FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Check if absolute value was passed
    if (isset($data['absolute']) && $data['absolute'] === true && isset($data['new_value'])) {
        $new_quantity = (int)$data['new_value'];
    }
    else if (isset($data['change'])) {
        $change = (int)$data['change'];
        $new_quantity = $row['quantity'] + $change;
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid input format']);
        exit();
    }

    if ($new_quantity < 1) {
        $new_quantity = 1;
    }

    $update_sql = "UPDATE cart SET quantity = '$new_quantity' WHERE id = '$cart_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo json_encode(['success' => true, 'new_quantity' => $new_quantity]);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
}

$conn->close();
?>
