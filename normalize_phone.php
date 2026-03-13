<?php
include 'connect.php';

echo "Normalizing existing contact numbers...\n";

$result = $conn->query("SELECT id, contact_number FROM userrs");
$count = 0;

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $original = $row['contact_number'];

    // Remove all non-numeric characters
    $cleaned = preg_replace('/[^0-9]/', '', $original);

    // If it's 11 digits starting with 0, strip the 0
    if (strlen($cleaned) == 11 && strpos($cleaned, '0') === 0) {
        $cleaned = substr($cleaned, 1);
    }

    // Only update if it fits our 10-digit '9' pattern and is different
    if (preg_match('/^9[0-9]{9}$/', $cleaned) && $cleaned !== $original) {
        $stmt = $conn->prepare("UPDATE userrs SET contact_number = ? WHERE id = ?");
        $stmt->bind_param("si", $cleaned, $id);
        $stmt->execute();
        $stmt->close();
        $count++;
        echo "Updated ID $id: $original -> $cleaned\n";
    }
}

echo "Finished. Updated $count records.\n";
$conn->close();
?>
