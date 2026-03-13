<?php
include("connect.php");
include("encryption_utils.php");

$sql = "SELECT id, username, fullname, birthdate, contact_number FROM userrs";
$result = $conn->query($sql);

echo "--- User Data Debug ---\n";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dec_name = decryptData($row['fullname']);
        $dec_uname = decryptData($row['username']);
        $dec_birth = decryptData($row['birthdate']);
        $dec_phone = decryptData($row['contact_number']);

        echo "ID: " . $row['id'] . "\n";
        echo "Username (Raw): " . $row['username'] . " | (Dec): $dec_uname\n";
        echo "Fullname (Raw): " . $row['fullname'] . " | (Dec): $dec_name\n";
        echo "Birthdate (Raw): " . $row['birthdate'] . " | (Dec): $dec_birth\n";
        echo "Phone (Raw): " . $row['contact_number'] . " | (Dec): $dec_phone\n";
        echo "----------------------\n";
    }
}
else {
    echo "No users found.\n";
}
?>
