<?php
include("connect.php");
include("encryption_utils.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // 1. Verify OTP first
    $encrypted_email = encryptData($email);

    $otp_sql = "SELECT email, otp_code, expires_at FROM password_resets";
    $otp_result = $conn->query($otp_sql);
    $otp_valid = false;

    while ($row = $otp_result->fetch_assoc()) {
        if (decryptData($row['email']) === $email && $row['otp_code'] === $otp) {
            // Check expiry
            if (strtotime($row['expires_at']) > time()) {
                $otp_valid = true;
            }
            break;
        }
    }

    if (!$otp_valid) {
        echo "<script>alert('Invalid or expired OTP. Please try again.'); window.history.back();</script>";
        exit;
    }

    // 2. Since OTP is valid, proceed to find user and update password
    $sql = "SELECT id, email FROM userrs";
    $result = $conn->query($sql);
    $user_id = null;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (decryptData($row['email']) === $email) {
                $user_id = $row['id'];
                break;
            }
        }
    }

    if ($user_id) {
        // Update the password
        $update_sql = "UPDATE userrs SET password = '$new_password' WHERE id = '$user_id'";

        if ($conn->query($update_sql) === TRUE) {
            // Clean up OTP
            $conn->query("DELETE FROM password_resets WHERE email = '$encrypted_email'");

            echo "<script>alert('Password updated successfully! You can now log in.'); window.location.href = 'login.html';</script>";
        }
        else {
            echo "<script>alert('Error updating password: " . $conn->error . "'); window.history.back();</script>";
        }
    }
    else {
        echo "<script>alert('Email not found. Please check your email or registration status.'); window.history.back();</script>";
    }
}

$conn->close();
?>
