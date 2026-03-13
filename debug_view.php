<?php
include 'connect.php';
include 'encryption_utils.php';

echo "<h2>Users Table Check</h2>";
$result = $conn->query("SELECT * FROM userrs");
echo "<table border='1'><tr>";
while($field = $result->fetch_field()) echo "<th>{$field->name}</th>";
echo "<th>Decrypted Fullname</th><th>Decrypted Username</th><th>Decrypted Email</th></tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach($row as $val) echo "<td>" . htmlspecialchars($val ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars(decryptData($row['fullname'])) . "</td>";
    echo "<td>" . htmlspecialchars(decryptData($row['username'])) . "</td>";
    echo "<td>" . htmlspecialchars(decryptData($row['email'])) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
