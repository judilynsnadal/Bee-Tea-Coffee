<?php
session_start();
require "connect.php";
require "encryption_utils.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required.']);
        exit;
    }

    // Check if user exists (decryption needed due to AES-CBC)
    $stmt = $conn->prepare("SELECT id, email FROM userrs");
    $stmt->execute();
    $result = $stmt->get_result();
    $user_found = false;

    while ($row = $result->fetch_assoc()) {
        if (decryptData($row['email']) === $email) {
            $user_found = true;
            break;
        }
    }

    if (!$user_found) {
        echo json_encode(['success' => false, 'message' => 'Email not found in our records.']);
        exit;
    }

    // Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Store in password_resets table
    // Delete any existing codes for this email first
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $encrypted_email = encryptData($email); // We store the encrypted email in the reset table too for consistency
    $stmt->bind_param("s", $encrypted_email);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO password_resets (email, otp_code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $encrypted_email, $otp, $expires);

    if ($stmt->execute()) {
        // Log the OTP for debugging/local testing
        file_put_contents("otp_log.txt", date('[Y-m-d H:i:s] ') . "OTP for $email: $otp (Expires: $expires)\n", FILE_APPEND);

        // Send email via centralized mailer.php
        require_once "mailer.php";
        $subject = "Your Password Reset OTP Code";
        $message = "
            <h2>Password Reset Request</h2>
            <p>Your OTP code for password reset is: <strong>$otp</strong></p>
            <p>This code will expire in 10 minutes.</p>
            <p>If you did not request this, please ignore this email.</p>
        ";

        $mailStatus = sendEmail($email, $subject, $message);

        if ($mailStatus['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'An OTP code has been sent to your email. Please check your inbox (and spam).'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'OTP generated but email delivery failed. ' . $mailStatus['message'],
                'debug_note' => 'You can find the OTP in otp_log.txt for local testing.'
            ]);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate OTP. Please try again.']);
    }

    $stmt->close();
}
$conn->close();
?>
