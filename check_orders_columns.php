<?php
require 'connect.php';
$r = $conn->query("DESCRIBE orders");
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
