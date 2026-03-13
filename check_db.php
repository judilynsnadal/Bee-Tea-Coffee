<?php
include 'connect.php';

echo "Database: " . $database . "<br>";

$tables = $conn->query("SHOW TABLES");
echo "Tables in DB:<br>";
while ($r = $tables->fetch_row()) {
    echo $r[0] . "<br>";
}

echo "<br>Checking specific table 'shopping_cart':<br>";
$res = $conn->query("DESCRIBE shopping_cart");
if ($res) {
    echo "shopping_cart exists!<br>";
    while ($col = $res->fetch_assoc()) {
        echo $col['Field'] . " - " . $col['Type'] . "<br>";
    }
}
else {
    echo "shopping_cart NOT found: " . $conn->error . "<br>";
}
?>
