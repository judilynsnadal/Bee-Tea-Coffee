<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['cart_id']) || !isset($data['addon']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit();
}

$cart_id = (int)$data['cart_id'];
$addon = $conn->real_escape_string(trim($data['addon']));
$action = $data['action']; // 'add' or 'remove'
$user_id = $_SESSION['user_id'];

// Verify the cart item belongs to the user
$check_sql = "SELECT id, addons FROM cart WHERE id = $cart_id AND user_id = $user_id";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows > 0) {
    $row = $check_result->fetch_assoc();
    $current_addons = !empty($row['addons']) ? $row['addons'] : '';

    $addons_arr = array_filter(array_map('trim', explode(',', $current_addons)));

    if ($action === 'add') {
        if (!in_array($addon, $addons_arr)) {
            $addons_arr[] = $addon;
        }
    }
    else if ($action === 'remove') {
        $addons_arr = array_diff($addons_arr, [$addon]);
    }

    $new_addons_str = $conn->real_escape_string(implode(',', $addons_arr));

    $update_sql = "UPDATE cart SET addons = '$new_addons_str' WHERE id = $cart_id";
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
