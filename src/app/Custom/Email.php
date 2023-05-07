<?php

namespace App\Custom;

use App\Entities\Logger;
use App\Errors\EmailException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    static public function send(string $from, string $to, string $title, string $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_HOST_USERNAME'];
            $mail->Password   = $_ENV['MAIL_HOST_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['MAIL_HOST_PORT'];

            $mail->setFrom($_ENV['MAIL_HOST_USERNAME'], $from);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body    = $body;
            $mail->send();
        } catch (Exception $e) {
            throw new EmailException("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
