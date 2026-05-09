<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/MeetingSummaryModel.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminMeetingSummaryController
{
    protected MeetingSummaryModel $model;
    protected Admin $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new MeetingSummaryModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function index(): void
    {
        $meetingRows = $this->model->allRows();
        require __DIR__ . '/../views/admin/meeting_summary.php';
    }
}
