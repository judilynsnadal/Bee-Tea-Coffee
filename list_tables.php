<?php
$conn = new mysqli('localhost', 'root', '', 'btc');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$res = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}
echo implode("\n", $tables);
$conn->close();
?>
