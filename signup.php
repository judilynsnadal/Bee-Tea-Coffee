<?php
include("connect.php");
include("encryption_utils.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Get form inputs
  $username = $_POST['username'];
  $fullname = $_POST['full_name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $contact_number = preg_replace('/[^0-9]/', '', $_POST['contact_number']);
  $address = $_POST['address'];
  $gender = $_POST['gender'];
  $birthdate = $_POST['birthdate'];

  // Validate phone number format (09XXXXXXXXX)
  if (!preg_match('/^09[0-9]{9}$/', $contact_number)) {
    echo "<script>alert('Invalid phone number format! Please use 11 digits starting with 09 (e.g., 09123456789).'); window.history.back();</script>";
    exit();
  }

  // Check if email already exists
  // Since email is encrypted, we fetch all and check manually
  $email_exists = false;
  $check_all = "SELECT email FROM userrs";
  $all_res = $conn->query($check_all);
  if ($all_res) {
    while ($row = $all_res->fetch_assoc()) {
      if (decryptData($row['email']) === $email) {
        $email_exists = true;
        break;
      }
    }
  }

  if ($email_exists) {
    echo "<script>alert('Email already registered! Please use another email.'); window.history.back();</script>";
    exit();
  }

  // Handle photo upload
  $photo_filename = "";
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $photo_filename = basename($_FILES['photo']['name']);
    $target = "uploads/" . $photo_filename;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
      echo "<script>alert('Error uploading photo'); window.history.back();</script>";
      exit();
    }
  }

  // Encrypt details before insertion
  $enc_fullname = encryptData($fullname);
  $enc_email = encryptData($email);
  $enc_contact = encryptData($contact_number);
  $enc_address = encryptData($address);
  $enc_username = encryptData($username);
  $enc_birthdate = encryptData($birthdate);
  $enc_photo = encryptData($photo_filename);

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO userrs (fullname, email, password, contact_number, address, gender, username, birthdate, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssssss", $enc_fullname, $enc_email, $password, $enc_contact, $enc_address, $gender, $enc_username, $enc_birthdate, $enc_photo);

  if ($stmt->execute()) {
    echo "<script>alert('Registration successful!'); window.location='login.html';</script>";
  }
  else {
    echo "Error: " . $stmt->error;
  }
  $stmt->close();
}
?>
