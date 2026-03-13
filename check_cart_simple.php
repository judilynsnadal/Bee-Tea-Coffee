<?php
include 'connect.php';

echo "User 34 check:<br>";
$res = $conn->query("SELECT * FROM shopping_cart WHERE user_id = 34");
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
        echo "<br>";
    }
}
else {
    echo "User 34 cart empty.<br>";
}

echo "User 1 check:<br>";
$res = $conn->query("SELECT * FROM shopping_cart WHERE user_id = 1");
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
        echo "<br>";
    }
}
else {
    echo "User 1 cart empty.<br>";
}
?>
