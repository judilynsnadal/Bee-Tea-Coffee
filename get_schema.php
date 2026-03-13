<?php
include 'connect.php';

$columns = [];
$result_columns = $conn->query("SHOW COLUMNS FROM cart");
if ($result_columns) {
    while ($row = $result_columns->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

$tables = [];
$result_tables = $conn->query("SHOW TABLES");
if ($result_tables) {
    while ($row = $result_tables->fetch_array()) {
        $tables[] = $row[0];
    }
}

echo json_encode(['tables' => $tables, 'columns' => $columns]);
?>
