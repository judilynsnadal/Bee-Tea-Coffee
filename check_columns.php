<?php
include 'connect.php';
$sql = "DESCRIBE cart";
$result = $conn->query($sql);
if ($result) {
    $output = "";
    while($row = $result->fetch_assoc()) {
        $output .= $row['Field'] . "\n";
    }
    file_put_contents("columns.txt", $output);
} else {
    echo "Error: " . $conn->error;
}
?>
