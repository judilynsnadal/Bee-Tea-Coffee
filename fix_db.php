<?php
include 'connect.php';

// Drop the corrupt table if it exists
$sql_drop = "DROP TABLE IF EXISTS cart";
if ($conn->query($sql_drop) === TRUE) {
    echo "Table 'cart' dropped successfully.<br>";
}
else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create the table again
$sql_create = "CREATE TABLE cart (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    product_image VARCHAR(255),
    quantity INT(11) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES userrs(id) ON DELETE CASCADE
)";

if ($conn->query($sql_create) === TRUE) {
    echo "Table 'cart' created successfully.<br>";
}
else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
