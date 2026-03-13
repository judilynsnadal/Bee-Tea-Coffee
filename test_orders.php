<?php
require 'connect.php';
$r = $conn->query("DESCRIBE orders");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo $row['Field'] . "<br>";
    }
}
else {
    echo $conn->error;
}
?>
