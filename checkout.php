<?php
session_start();
include 'connect.php';
include 'encryption_utils.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['payment_method'])) {
    echo json_encode(['success' => false, 'message' => 'Payment method not specified']);
    exit();
}

$payment_method = $conn->real_escape_string($data['payment_method']);
$transaction_ref = 'TRX-' . strtoupper(uniqid()) . '-' . $user_id;
$status = 'Pending'; // Default status

// 1. Get Cart Items
$sql_cart = "SELECT * FROM cart WHERE user_id = '$user_id'";
$result_cart = $conn->query($sql_cart);

if ($result_cart->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// 2. Insert into Orders
$success_count = 0;
$order_items = [];
$total_receipt_price = 0;

while ($row = $result_cart->fetch_assoc()) {
    $product_name = $conn->real_escape_string($row['product_name']);
    $price = $row['price'];
    $quantity = $row['quantity'];
    $image = $conn->real_escape_string($row['image']);
    $size = $conn->real_escape_string($row['size']);
    $addons = $conn->real_escape_string($row['addons']);

    // Base total per item type
    $item_unit_price = $price;
    if ($size === '20oz') {
        $item_unit_price += 10;
    }

    $active_addons = array_filter(array_map('trim', explode(',', $addons)));
    $addon_count = count($active_addons);
    $item_unit_price += ($addon_count * 10);

    $item_total = $item_unit_price * $quantity;
    $total_receipt_price += $item_total;

    $sql_insert = "INSERT INTO orders (user_id, product_name, price, image, quantity, size, addons, payment_method, transaction_ref, status)
                   VALUES ('$user_id', '$product_name', '$item_unit_price', '$image', '$quantity', '$size', '$addons', '$payment_method', '$transaction_ref', '$status')";

    if ($conn->query($sql_insert)) {
        $success_count++;
        $order_items[] = [
            'product_name' => $product_name,
            'price' => $item_unit_price,
            'quantity' => $quantity,
            'size' => $size,
            'addons' => $addons,
            'item_total' => $item_total
        ];
    }
}

// 3. Fetch User Details for Receipt
$sql_user = "SELECT fullname, contact_number, address FROM userrs WHERE id = '$user_id'";
$result_user = $conn->query($sql_user);
$user_info = $result_user->fetch_assoc();

if ($success_count > 0) {
    // 4. Clear Cart
    $conn->query("DELETE FROM cart WHERE user_id = '$user_id'");
    echo json_encode([
        'success' => true,
        'message' => 'Checkout successful',
        'transaction_ref' => $transaction_ref,
        'order_items' => $order_items,
        'total_price' => $total_receipt_price,
        'payment_method' => $payment_method,
        'customer_name' => decryptData($user_info['fullname']),
        'customer_phone' => decryptData($user_info['contact_number']),
        'customer_address' => decryptData($user_info['address'])
    ]);
}
else {
    echo json_encode(['success' => false, 'message' => 'Failed to process any items']);
}

$conn->close();
?>
