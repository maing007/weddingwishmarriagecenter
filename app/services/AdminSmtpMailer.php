<?php

class AdminSmtpMailer
{
    private $socket;
    private $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/mail.php';
    }

    private function readResponse($expect)
    {
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        if (is_array($expect)) {
            foreach ($expect as $code) {
                if (strpos($response, (string)$code) === 0) {
                    return $response;
                }
            }
        } else {
            if (strpos($response, (string)$expect) === 0) {
                return $response;
            }
        }

        throw new Exception("SMTP Error: " . $response);
    }

    private function sendCommand($command, $expect)
    {
        fwrite($this->socket, $command . "\r\n");
        return $this->readResponse($expect);
    }

    public function send($to, $subject, $message)
    {
        $this->socket = fsockopen(
            $this->config['host'], // usually mail.yourdomain.com
            $this->config['port'], // 587
            $errno,
            $errstr,
            30
        );

        if (!$this->socket) {
            throw new Exception("Connection failed: $errstr ($errno)");
        }

        // Read server greeting
        $this->readResponse(220);

        $this->sendCommand("EHLO localhost", 250);
        $this->sendCommand("STARTTLS", 220);

        stream_socket_enable_crypto(
            $this->socket,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
        );

        $this->sendCommand("EHLO localhost", 250);

        $this->sendCommand("AUTH LOGIN", 334);
        $this->sendCommand(base64_encode($this->config['username']), 334);
        $this->sendCommand(base64_encode($this->config['password']), 235);

        $this->sendCommand("MAIL FROM:<{$this->config['from_email']}>", 250);
        $this->sendCommand("RCPT TO:<$to>", [250, 251, 252]);
        $this->sendCommand("DATA", 354);

        // Headers
        $headers  = "From: {$this->config['from_name']} <{$this->config['from_email']}>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Dot-stuffing (important!)
        $body = preg_replace("/^\./m", "..", $message);

        $data = $headers . "\r\n" . nl2br($body) . "\r\n.\r\n";

        fwrite($this->socket, $data);
        $this->readResponse(250);

        $this->sendCommand("QUIT", 221);
        fclose($this->socket);

        return true;
    }
}
