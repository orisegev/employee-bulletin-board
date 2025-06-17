<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private PHPMailer $mail;

    public function __construct(PHPMailer $mailer) {
        $this->mail = $mailer;
    }

    public function send(string $toEmail, string $toName, string $subject, string $body): bool {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->setFrom($_ENV['EMAIL_USER'], 'פורטל העובדים עיריית רמת הוד');
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}