<?php
session_start();
include("connect.php");
include("encryption_utils.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("Error: Session expired. Please log in again.");
}

$user_id = $_SESSION['user_id'];

// Check if all needed POST data is present
$required_fields = ['fullname', 'email', 'address', 'contact_number', 'gender', 'birthdate'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        die("Error: Missing field: $field");
    }
}

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$address = $_POST['address'];
$contact = preg_replace('/[^0-9]/', '', $_POST['contact_number']);
$gender = $_POST['gender'];
$birthdate = $_POST['birthdate'];

// Validate phone number format (09XXXXXXXXX)
if (!preg_match('/^09[0-9]{9}$/', $contact)) {
    echo "<script>alert('Invalid phone number format! Please use 11 digits starting with 09 (e.g., 09123456789).'); window.history.back();</script>";
    exit();
}

// Handle profile photo upload
$photo_updated = false;
$photo_name = "";

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['photo']['tmp_name'];
    $file_name = $_FILES['photo']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_exts = ['jpg', 'jpeg', 'png'];
    if (in_array($file_ext, $allowed_exts)) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $new_file_name = "profile_" . $user_id . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $photo_name = $new_file_name;
            $photo_updated = true;
        }
    }
}

// Encrypt details before update
$enc_fullname = encryptData($fullname);
$enc_email = encryptData($email);
$enc_address = encryptData($address);
$enc_contact = encryptData($contact);
$enc_birthdate = encryptData($birthdate);

// Perform update
if ($photo_updated) {
    $enc_photo = encryptData($photo_name);
    $stmt = $conn->prepare("UPDATE userrs SET fullname=?, email=?, address=?, contact_number=?, gender=?, birthdate=?, photo=? WHERE id=?");
    $stmt->bind_param("sssssssi", $enc_fullname, $enc_email, $enc_address, $enc_contact, $gender, $enc_birthdate, $enc_photo, $user_id);
}
else {
    $stmt = $conn->prepare("UPDATE userrs SET fullname=?, email=?, address=?, contact_number=?, gender=?, birthdate=? WHERE id=?");
    $stmt->bind_param("ssssssi", $enc_fullname, $enc_email, $enc_address, $enc_contact, $gender, $enc_birthdate, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['user_name'] = $fullname;
    echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
}
else {
    echo "Error updating database: " . $stmt->error;
    echo "<br><a href='profile.php'>Back to Profile</a>";
}

$stmt->close();
$conn->close();
?>
