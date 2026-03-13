<?php
include 'connect.php';

$sql1 = "ALTER TABLE cart ADD COLUMN size VARCHAR(10) DEFAULT '16oz' AFTER quantity;";
$sql2 = "ALTER TABLE orders ADD COLUMN size VARCHAR(10) DEFAULT '16oz' AFTER quantity;";

if ($conn->query($sql1) === TRUE) {
    echo "Added size to cart.\n";
}
else {
    echo "Error cart: " . $conn->error . "\n";
}

if ($conn->query($sql2) === TRUE) {
    echo "Added size to orders.\n";
}
else {
    echo "Error orders: " . $conn->error . "\n";
}
?>
