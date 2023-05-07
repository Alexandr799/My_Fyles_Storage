<?php

namespace App\Custom;

use App\Entities\Logger;
use App\Entities\Response;
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
            $mail->Host       = 'smtp.mail.ru';
            $mail->SMTPAuth   = true;
            $mail->Username   = '228.test@internet.ru';
            $mail->Password   = '5dw2z1cjTMaQXkeHrbyu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('228.test@internet.ru', $from);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body    = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            Logger::printLog("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", 'email');
            return false;
        }
    }
}
