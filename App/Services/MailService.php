<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    protected object $mail;
    protected array $config;
    function __construct($debug = false)
    {
        $this->mail = new PHPMailer(true);
        try {
            $this->config = get_config('mail');
            $this->mail->SMTPDebug = !$debug ? 0 : SMTP::DEBUG_SERVER;
            $this->mail->isSMTP();
            $this->mail->Host       = $this->config['host'];                     //Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $this->mail->Username   = $this->config['username'];                     //SMTP username
            $this->mail->Password   = $this->config['password'];                               //SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $this->mail->Port       = $this->config['port'] ?? 587;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $this->mail->ErrorInfo
            ];
        }
    }

    function sendMail($recipient_email, $subject, $body, $is_html = false, $altBody = "", $recipient_name = null, $from = null)
    {
        try {
            //Recipients
            $this->mail->setFrom($this->config['from'], $from ?? get_config('app_name'));
            $this->mail->addAddress($recipient_email, $recipient_name);

            //Content
            $this->mail->isHTML($is_html);
            $this->mail->Subject = $subject . " - from " . get_config("app_name");
            $this->mail->Body    = $body;
            $this->mail->AltBody = $altBody;

            $this->mail->send();
            return [
                'success' => true
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $this->mail->ErrorInfo
            ];
        }
    }
}
