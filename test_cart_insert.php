<?php
include "connect.php";
$log = "";

$schema = [];
$r = $conn->query("DESCRIBE cart");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $schema[] = $row['Field'] . "(" . $row['Type'] . ")";
    }
}
$log .= "Schema: " . implode(", ", $schema) . "\n";

$r = $conn->query("SELECT * FROM cart ORDER BY id DESC LIMIT 5");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $log .= print_r($row, true) . "\n";
    }
}
else {
    $log .= "Error: " . $conn->error;
}
file_put_contents("test_cart_out.txt", $log);
echo "Logged";
?>
