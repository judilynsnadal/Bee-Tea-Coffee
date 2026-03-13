<?php
include 'connect.php';
include 'encryption_utils.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $login_email = $_POST['email'];
  $password = $_POST['password'];

  // Since email is encrypted, we fetch all and check manually
  $sql = "SELECT * FROM userrs";
  $result = $conn->query($sql);
  $user_found = false;

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      if (decryptData($row['email']) === $login_email) {
        if (password_verify($password, $row['password'])) {
          $_SESSION['user_id'] = $row['id'];
          $_SESSION['user_name'] = decryptData($row['fullname']);
          header("Location: profile.php");
          exit();
        }
        else {
          echo "<script>alert('Invalid password'); window.location='login.html';</script>";
          exit();
        }
      }
    }
  }

  // If we reach here, no email matched or no users found
  echo "<script>alert('No account found with that email'); window.location='login.html';</script>";
}
$conn->close();
?>
