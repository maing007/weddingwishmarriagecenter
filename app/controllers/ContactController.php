<?php

require_once __DIR__ . '/../models/Contact.php';

class ContactController
{
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['is_post'])) {
            header("Location: " . BASE_URL . "/contact");
            exit;
        }

        // Sanitize & collect input
        $data = [
            'name'         => trim($_POST['name'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'country_code' => trim($_POST['country_code'] ?? ''),
            'phone'        => trim($_POST['phone'] ?? ''),
            'subject'      => trim($_POST['subject'] ?? ''),
            'description'  => trim($_POST['description'] ?? ''),
            'ip_address'   => $_SERVER['REMOTE_ADDR']
        ];

        // Basic validation
        if (
            $data['name'] === '' ||
            $data['email'] === '' ||
            $data['phone'] === '' ||
            $data['subject'] === '' ||
            $data['description'] === ''
        ) {
            $_SESSION['error'] = "All required fields must be filled.";
            header("Location: " . BASE_URL . "/contact");
            exit;
        }

        // Save to DB
        $contact = new Contact();

        if ($contact->save($data)) {
            $_SESSION['success'] = "Your enquiry has been submitted successfully.";
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
        }

        header("Location: " . BASE_URL . "/contact");
        exit;
    }

}
