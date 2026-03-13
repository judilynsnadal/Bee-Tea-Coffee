<?php
include 'connect.php';
$res = $conn->query("SHOW COLUMNS FROM cart");
while ($row = $res->fetch_assoc())
    echo $row['Field'] . "\n";
?>
