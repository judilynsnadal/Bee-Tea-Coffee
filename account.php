<?php
session_start();
include 'connect.php';
include 'encryption_utils.php';

// check if logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM userrs WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($user) {
  // Decrypt user data
  $user['fullname'] = decryptData($user['fullname']);
  $user['email'] = decryptData($user['email']);
  $user['contact_number'] = decryptData($user['contact_number']);
  $user['address'] = decryptData($user['address']);
  $user['username'] = decryptData($user['username']);
  $user['birthdate'] = decryptData($user['birthdate']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Account</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

 <form class="profile-form" action="update_account.php" method="POST">
  <div class="form-grid">
    <!-- LEFT SIDE -->
    <div class="form-left">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required />
        <small>Username can only be changed once.</small>
      </div>

      <div class="form-group">
        <label>Name</label>
        <input type="text" name="fullname" placeholder="Enter your name" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required />
      </div>

      <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" placeholder="Enter your address" />
      </div>

      <div class="form-group">
        <label>Contact Number</label>
        <input type="text" name="contact_number" placeholder="Enter your number" />
      </div>

      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" />
      </div>

      <button type="submit" class="save-btn">Save</button>
    </div>
  </div>
</form>

  </div>

</body>
</html>
