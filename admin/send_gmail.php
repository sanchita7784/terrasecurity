<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // --- Server Settings for Gmail ---
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.gmail.com';                     // Gmail SMTP server
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'contact.terraventures@gmail.com';                 // Your actual Gmail address
    $mail->Password   = 'ftjidrjugvuesdvx';                     // Your 16-digit App Password (no spaces)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Required encryption for port 587
    $mail->Port       = 587;                                    // TCP port to connect to

    // --- Recipients ---
    $mail->setFrom('contact.terraventures@gmail.com', 'Terra Ventures'); // Your Gmail address and name
    $mail->addAddress('hardeepvicky1@gmail.com');                 // Who is receiving the email

    // --- Content ---
    $mail->isHTML(true);                                  
    $mail->Subject = 'Sent via Gmail SMTP';
    $mail->Body    = 'Hello! This email was safely routed through Google SMTP using PHPMailer.';

    $mail->send();
    echo 'Message has been sent successfully via Gmail!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>