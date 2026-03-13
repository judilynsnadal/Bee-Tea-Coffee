<?php
// debug_db_connection.php

$servername = "localhost";
$username = "root";
$password = "";

// 1. Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected to MySQL server.<br>";

// 2. Check if 'btc' database exists
$db_selected = $conn->select_db("btc");
if (!$db_selected) {
    echo "Database 'btc' NOT found.<br>";
    // List available databases
    $result = $conn->query("SHOW DATABASES");
    echo "Available databases: ";
    while($row = $result->fetch_assoc()) echo $row['Database'] . ", ";
    echo "<br>";
} else {
    echo "Database 'btc' selected.<br>";
    
    // 3. List tables in 'btc'
    $result = $conn->query("SHOW TABLES");
    echo "Tables in 'btc':<br>";
    $tables = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
            $tables[] = $row[0];
        }
    } else {
        echo "No tables found in 'btc'.<br>";
    }

    // 4. Check 'shopping_cart' table specifically
    if (in_array("shopping_cart", $tables)) {
        echo "Table 'shopping_cart' exists.<br>";
        $cols = $conn->query("SHOW COLUMNS FROM shopping_cart");
        echo "Columns in 'shopping_cart':<br>";
        while($row = $cols->fetch_assoc()) {
            echo $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "CRITICAL: Table 'shopping_cart' DOES NOT EXIST.<br>";
        echo "Last SQL Error: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
