<?php
// test_update_quantity.php
// First, we need a valid cart ID. Let's fetch one.
include 'connect.php';
$result = $conn->query("SELECT id, quantity FROM cart LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cart_id = $row['id'];
    $current_qty = $row['quantity'];
    echo "Testing with Cart ID: $cart_id (Current Qty: $current_qty)\n";

    // Simulate POST request to update_cart_quantity.php
    // We need to verify session handling, so we might need to actually login or mock the session in the test script.
    // simpler approach: just include the logic file? No, because it has exit().
    // Let's use curl with a cookie jar if possible, or just copy the logic effectively.
    // Actually, since I can run PHP CLI, I can just include the connect and run the update logic directly to test the SQL.
    
    $new_qty = $current_qty + 1;
    $sql = "UPDATE cart SET quantity = '$new_qty' WHERE id = '$cart_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Update SQL Success. New Qty: $new_qty\n";
        // Revert
        $conn->query("UPDATE cart SET quantity = '$current_qty' WHERE id = '$cart_id'");
        echo "Reverted to Qty: $current_qty\n";
    } else {
        echo "Update SQL Failed: " . $conn->error . "\n";
    }

} else {
    echo "No content in cart to test.\n";
}
?>
