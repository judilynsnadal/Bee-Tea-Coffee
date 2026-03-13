<?php
session_start();
include("connect.php");

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM userrs WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<script>alert('User not found!'); window.location='login.html';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>My Account</title>
</head>
<body>
  <h2>Welcome, <?php echo $user['fullname']; ?>!</h2>
</body>
</html>
