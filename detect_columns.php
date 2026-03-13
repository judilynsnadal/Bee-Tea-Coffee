<?php
// detect_columns.php
include 'connect.php';

$output_file = 'detected_columns.txt';
$handle = fopen($output_file, 'w');

if ($conn->connect_error) {
    fwrite($handle, "Connection failed: " . $conn->connect_error);
    exit();
}

$table = 'adminn';
$sql = "SHOW COLUMNS FROM $table";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    fwrite($handle, "Columns in table '$table':\n");
    while ($row = $result->fetch_assoc()) {
        fwrite($handle, $row['Field'] . " (" . $row['Type'] . ")\n");
    }
}
else {
    fwrite($handle, "Error or no columns: " . $conn->error);
}

fclose($handle);
$conn->close();
?>
