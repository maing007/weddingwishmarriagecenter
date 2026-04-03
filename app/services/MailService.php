<?php

require_once __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {

    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/mail.php';
    }

    public function send($to, $subject, $body) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['port'];

            $mail->setFrom(
                $this->config['from_email'],
                $this->config['from_name']
            );

            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();

        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }
}