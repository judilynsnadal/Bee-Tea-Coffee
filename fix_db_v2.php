<?php
include 'connect.php';

// Create the table 'shopping_cart'
$sql_create = "CREATE TABLE IF NOT EXISTS shopping_cart (
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
    echo "Table 'shopping_cart' created successfully.<br>";
}
else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
