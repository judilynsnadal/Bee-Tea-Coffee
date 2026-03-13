<?php
include("connect.php");

echo "--- Database Migration: Fixing Encryption Schema ---\n";

$queries = [
    "ALTER TABLE userrs MODIFY COLUMN birthdate VARCHAR(255)",
    "ALTER TABLE userrs MODIFY COLUMN contact_number VARCHAR(255)",
    "ALTER TABLE userrs MODIFY COLUMN fullname VARCHAR(255)",
    "ALTER TABLE userrs MODIFY COLUMN email VARCHAR(255)",
    "ALTER TABLE userrs MODIFY COLUMN username VARCHAR(255)"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Successfully executed: " . $sql . "\n";
    }
    else {
        echo "Error executing query: " . $sql . " | Error: " . $conn->error . "\n";
    }
}

$conn->close();
echo "--- Migration Finished ---\n";
?>
