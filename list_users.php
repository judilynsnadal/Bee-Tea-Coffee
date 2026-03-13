<?php
include 'connect.php';
$res = $conn->query("SELECT id, fullname, email, username FROM userrs");
echo "Users:<br>";
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['fullname'] . " | Email: " . $row['email'] . "<br>";
}
?>
