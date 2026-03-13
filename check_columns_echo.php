<?php
include 'connect.php';
$result = $conn->query("SHOW COLUMNS FROM cart");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
