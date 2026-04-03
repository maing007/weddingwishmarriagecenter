<?php

require_once __DIR__ . '/../services/MailService.php';

class MailController {

    public function sendTest() {
        $mailService = new MailService();

        $result = $mailService->send(
            'us533gi@gmail.com',
            'Hello from Wedding Wish Marriage Centre',
            '<b>Welcome to Wedding Wish Marriage Centre!</b>'
        );

        if ($result === true) {
            echo "Email sent!";
        } else {
            echo "Error: " . $result;
        }
    }
}