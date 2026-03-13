<?php
require 'connect.php';

// Check if image column exists
$result = $conn->query("SHOW COLUMNS FROM orders LIKE 'image'");
if ($result->num_rows == 0) {
    // Add image column
    $add_col = $conn->query("ALTER TABLE orders ADD COLUMN image VARCHAR(255) AFTER price");
    if ($add_col) {
        echo "Column 'image' successfully added to orders table.\n";
    }
    else {
        echo "Error adding column: " . $conn->error . "\n";
    }
}
else {
    echo "Column 'image' already exists.\n";
}
?>
