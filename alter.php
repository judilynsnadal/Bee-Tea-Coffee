<?php
include "connect.php";
$conn->query("ALTER TABLE cart ADD COLUMN addons VARCHAR(255) DEFAULT ''");
$conn->query("ALTER TABLE orders ADD COLUMN addons VARCHAR(255) DEFAULT ''");
if ($conn->error)
    echo $conn->error;
else
    echo "Success";
?>
