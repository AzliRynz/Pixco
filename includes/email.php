<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to send email
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = getConfig('smtp_host', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = getConfig('smtp_username');
        $mail->Password = getConfig('smtp_password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getConfig('smtp_port', 587);

        // Recipients
        $mail->setFrom(getConfig('smtp_from_email', 'noreply@pixco.com'), getConfig('smtp_from_name', 'Pixco'));
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $subject = t('email_verification_subject');
    $body = sprintf(t('email_verification_body'), getSiteName(), "https://" . $_SERVER['HTTP_HOST'] . "/verify-email?token=$token");
    return sendEmail($email, $subject, $body);
}