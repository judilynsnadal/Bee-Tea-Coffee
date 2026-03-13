<?php
include 'connect.php';
$result = $conn->query("SELECT * FROM cart LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    print_r($row);
} else {
    echo "No data or query failed: " . $conn->error;
}
?>
