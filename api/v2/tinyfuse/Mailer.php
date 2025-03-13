<?php

namespace tinyfuse;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

trait Mailer
{
    private function mail_html(string $to, string $to_name, string $body, string $subject = 'Notification from SPC Media Unit'): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->state->get_env('MAIL_HOST') ?? 'localhost';
            $mail->SMTPAuth = true;
            $mail->Username = $this->state->get_env('MAIL_USERNAME') ?? 'root@localhost';
            $mail->Password = $this->state->get_env('MAIL_PASSWORD') ?? 'spcmediaunit2023';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->state->get_env('MAIL_PORT') ?? 1025;

            $mail->setFrom('info@spcjaffna.org', 'SPC Media Unit');
            $mail->addAddress($to, $to_name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = htmlspecialchars($body);

            $mail->send();
        } catch (Exception $e) {
            if ($this->state->DEBUG) {
                Utils::logDebug($e->getMessage());
            } else {
                Utils::logInfo('failed to send mail!');
            }

            return false;
        }
        return true;
    }

}