<?php
/*
 * Malachi: In House Messenger system
 */

namespace tinyfuse\lib;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Malachi
{
    private PHPMailer $mail;

    public function __construct()
    {
        try {
            $this->mail = new PHPMailer(true);

            $this->mail->isSMTP();
            $this->mail->SMTPAuth = true;

            $this->mail->Host = $_ENV['MAIL_HOST'];
            $this->mail->Port = $_ENV['MAIL_PORT'];
            $this->mail->Username = $_ENV['MAIL_USER'];
            $this->mail->Password = $_ENV['MAIL_PASS'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $this->mail->setFrom($_ENV['MAIL_USER'], 'SPC Media Unit');
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            exit(1);
        }

    }

    public function send_msg(string $sub, string $msg, string $to, bool $isHTML = false): bool
    {
        $this->mail->Subject = $sub;
        $this->mail->Body = $msg;
        $this->mail->AltBody = 'Error Occured during Render.';
        $this->mail->isHTML($isHTML);

        try {
            $this->mail->addAddress($to);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            debug("Marachi failed.", __FILE__);
            return false;
        }
    }
}