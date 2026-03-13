<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Use Composer autoloader
require_once __DIR__ . '/../EMAIL/vendor/autoload.php';

/**
 * Sends an email using PHPMailer with the provided SMTP settings.
 *
 * @param string $to Email address of the recipient
 * @param string $subject Subject of the email
 * @param string $message Body of the email
 * @return array ['success' => boolean, 'message' => string]
 */
function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // Verified Credentials
        $mail->Username   = 'offbtcoffee@gmail.com';
        $mail->Password   = 'gyud tzle xouk ezrf'; // Verified App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Disabling SSL verification for local testing compatibility
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // Recipients
        $mail->setFrom('offbtcoffee@gmail.com', 'Bea Tea Coffee');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return ['success' => true, 'message' => 'Email has been sent.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}
?>
