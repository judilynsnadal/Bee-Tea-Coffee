<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "btc");
    $result = $conn->query("SHOW TABLES");
    echo "Tables in DB:<br>";
    while ($row = $result->fetch_row()) {
        echo $row[0] . "<br>";
    }

    echo "<hr>";
    $cart = $conn->query("SELECT * FROM shopping_cart");
    echo "Shopping Cart Rows: " . $cart->num_rows . "<br>";


}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
