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

if (!$data || !isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$cart_id = $conn->real_escape_string($data['cart_id']);

// Delete item ensuring it belongs to the user
$sql = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Item removed']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Error removing item: ' . $conn->error]);
}

$conn->close();
?>
