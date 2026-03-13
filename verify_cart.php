<?php
include 'connect.php';

$sql = "SELECT * FROM cart ORDER BY id DESC";
$result = $conn->query($sql);

if ($result) {
    echo "Total rows: " . $result->num_rows . "\n\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
}
else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
