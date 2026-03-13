<?php
include 'connect.php';
include 'encryption_utils.php';

$log_file = 'db_dump.txt';
$handle = fopen($log_file, 'w');

$result = $conn->query("SELECT * FROM userrs");
while($row = $result->fetch_assoc()) {
    $line = "ID: {$row['id']} | ";
    $line .= "Raw Fullname: '{$row['fullname']}' | ";
    $line .= "Dec Fullname: '" . decryptData($row['fullname']) . "' | ";
    $line .= "Raw Username: '{$row['username']}' | ";
    $line .= "Dec Username: '" . decryptData($row['username']) . "' | ";
    $line .= "Raw Email: '{$row['email']}' | ";
    $line .= "Dec Email: '" . decryptData($row['email']) . "'\n";
    fwrite($handle, $line);
}
fclose($handle);
echo "Dump finished to $log_file";
?>
