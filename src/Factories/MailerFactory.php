<?php
namespace App\Factories;

use PHPMailer\PHPMailer\PHPMailer;

class MailerFactory {
    public static function create(): PHPMailer {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->CharSet = 'UTF-8';
        return $mail;
    }
}