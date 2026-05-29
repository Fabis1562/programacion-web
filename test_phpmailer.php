<?php
require 'backend/libs/PHPMailer/Exception.php';
require 'backend/libs/PHPMailer/PHPMailer.php';
require 'backend/libs/PHPMailer/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'conectapro202605@gmail.com'; 
    $mail->Password   = 'tarzkbalghymtdxg'; // Contraseña de aplicación
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('conectapro202605@gmail.com', 'ConectaPro Soporte');
    $mail->addAddress('conectapro202605@gmail.com'); // self

    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = "Test email.";

    $mail->send();
    echo "Mail sent successfully!";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>
