<?php

namespace optiwariindia;

class mail extends \PHPMailer\PHPMailer\PHPMailer
{
    private static $sender;
    public static function sender($sender)
    {
        self::$sender = $sender;
    }
    public static function sendMail($rcpts, $content)
    {
        $sender = self::$sender;
        $mail = new mail(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $sender['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $sender['user'];
            $mail->Password   = $sender['password'];
            $mail->SMTPSecure = mail::ENCRYPTION_SMTPS;
            $mail->Port       = $sender['port'];
            $mail->setFrom($sender['user'], $sender['name']);
            foreach ($rcpts as $rcpt) {
                if (isset($rcpt['name'])) {
                    $mail->addAddress($rcpt['email'], $rcpt['name']);
                } else {
                    $mail->addAddress($rcpt['email']);
                }
            }
            if (isset($sender['reply']))
                $mail->addReplyTo($sender['reply']['email'], $sender['reply']['name']);
            if (isset($content['attach'])) {
                foreach ($content['attach'] as $attach) {
                    if (isset($attach['name']))
                        $mail->addAttachment($attach['file'], $attach['name']);
                    else
                        $mail->addAttachment($attach['file']);
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $content['subject'];
            $mail->Body    = $content['body'];
            $mail->AltBody = $content['bodytxt'];
            $mail->send();
        } catch (\PHPMailer\PHPmailer\Exception $e) {
        }
    }
}
