<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "btc");
    $sql = "SELECT * FROM shopping_cart";
    $result = $conn->query($sql);

    echo "Row Count: " . $result->num_rows . "<br><br>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "<br>";
            echo "User ID: " . $row['user_id'] . "<br>";
            echo "Product: " . $row['product_name'] . "<br>";
            echo "Price: " . $row['product_price'] . "<br>";
            echo "Image: " . $row['product_image'] . "<br>";
            echo "Qty: " . $row['quantity'] . "<br>";
            echo "Created: " . $row['created_at'] . "<br>";
            echo "--------------------------------<br>";
        }
    }
    else {
        echo "No items found.";
    }
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
