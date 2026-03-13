<?php
session_start();
include 'connect.php';
// No encryption check here, just raw data
if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM userrs WHERE id = '$user_id'");
$user = $result->fetch_assoc();
foreach ($user as $key => $value) {
    echo "$key: " . ($value === null ? "NULL" : "'$value'") . "\n";
}
?>
