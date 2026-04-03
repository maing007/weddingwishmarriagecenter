<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/StaffActivityModel.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminStaffActivityController
{
    protected StaffActivityModel $model;
    protected Admin $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new StaffActivityModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function allActivity(): void
    {
        $activityRows = $this->model->allRows();
        require __DIR__ . '/../views/admin/staff_all_activity.php';
    }

    public function summary(): void
    {
        $summaryRows = $this->model->summaryRows();
        require __DIR__ . '/../views/admin/staff_activity_summary.php';
    }

    public function exportAllActivity(): void
    {
        $rows = $this->model->allRows();
        $this->sendCsv(
            'staff-all-activity.csv',
            ['Date Time', 'Staff Name', 'Team Name', 'Main Topic', 'Activity', 'Department Name', 'Matri Id', 'Member Name', 'Detail'],
            $rows,
            static function (array $r): array {
                $ts = strtotime((string) ($r['activity_at'] ?? ''));

                return [
                    $ts ? date('Y-m-d H:i:s', $ts) : '',
                    (string) ($r['staff_name'] ?? ''),
                    (string) ($r['team_name'] ?? ''),
                    (string) ($r['main_topic'] ?? ''),
                    (string) ($r['activity'] ?? ''),
                    (string) ($r['department_name'] ?? ''),
                    (string) ($r['matri_id'] ?? ''),
                    (string) ($r['member_name'] ?? ''),
                    (string) ($r['detail'] ?? ''),
                ];
            }
        );
    }

    public function exportSummary(): void
    {
        $rows = $this->model->summaryRows();
        $this->sendCsv(
            'staff-activity-summary.csv',
            ['Staff Name', 'Team Name', 'Department Name', 'Total Activities', 'Last Activity'],
            $rows,
            static function (array $r): array {
                $ts = strtotime((string) ($r['last_activity_at'] ?? ''));

                return [
                    (string) ($r['staff_name'] ?? ''),
                    (string) ($r['team_name'] ?? ''),
                    (string) ($r['department_name'] ?? ''),
                    (string) ($r['total_activities'] ?? '0'),
                    $ts ? date('Y-m-d H:i:s', $ts) : '',
                ];
            }
        );
    }

    private function sendCsv(string $filename, array $header, array $rows, callable $mapRow): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if ($out === false) {
            exit;
        }
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, $header);
        foreach ($rows as $r) {
            fputcsv($out, $mapRow($r));
        }
        fclose($out);
        exit;
    }
}
