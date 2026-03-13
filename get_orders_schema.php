<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';
$result = $conn->query("DESCRIBE orders");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
