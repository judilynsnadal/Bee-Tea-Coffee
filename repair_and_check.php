<?php
// repair_and_check.php

$servername = "localhost";
$username = "root";
$password = "";
$database = "btc";

// 1. Connect
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected to 'btc'.<br>";

// 2. Check/Create shopping_cart
$sql = "CREATE TABLE IF NOT EXISTS shopping_cart (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    product_image VARCHAR(255),
    quantity INT(11) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- Removed FOREIGN KEY constraint temporarily to avoid errors if userrs table engine mismatches or if userrs.id is not index
    -- We can add it later if needed, but for now getting the table to exist is priority.
)";

// Let's try to add FK if we can, but careful.
// Actually, let's just stick to the definition in fix_db_v2.php but maybe without FK if it fails?
// Let's try WITH FK first.
$sql_with_fk = "CREATE TABLE IF NOT EXISTS shopping_cart (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    product_image VARCHAR(255),
    quantity INT(11) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES userrs(id) ON DELETE CASCADE
)";

if ($conn->query($sql_with_fk) === TRUE) {
    echo "Table 'shopping_cart' checked/created successfully.<br>";
} else {
    echo "Error creating table with FK: " . $conn->error . "<br>";
    echo "Trying without FK...<br>";
    if ($conn->query($sql) === TRUE) {
        echo "Table 'shopping_cart' created successfully (without FK).<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// 3. List tables
$result = $conn->query("SHOW TABLES");
echo "Current tables:<br>";
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . "<br>";
}

$conn->close();
?>
