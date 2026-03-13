<?php
include 'connect.php';
// Enable all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking columns for 'userrs' table:\n";
$result = $conn->query("SHOW COLUMNS FROM userrs");
if (!$result) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}
?>
