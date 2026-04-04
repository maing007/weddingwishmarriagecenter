<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/MemberReportsModel.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminReportsMembersController
{
    protected MemberReportsModel $model;
    protected Admin $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new MemberReportsModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function emailVerification(): void
    {
        $rows = $this->model->allEmailVerification();
        require __DIR__ . '/../views/admin/members_email_verification.php';
    }

    public function membersSummary(): void
    {
        $rows = $this->model->allMembersSummary();
        require __DIR__ . '/../views/admin/members_summary_report.php';
    }

    public function unsubscribeMember(): void
    {
        $rows = $this->model->allUnsubscribeMembers();
        require __DIR__ . '/../views/admin/unsubscribe_members_report.php';
    }

    public function membersAllActivity(): void
    {
        $rows = $this->model->allMembersActivity();
        require __DIR__ . '/../views/admin/members_all_activity_report.php';
    }

    public function membersAllActivityExport(): void
    {
        $rows = $this->model->allMembersActivity();
        $this->sendCsv(
            'members-all-activity.csv',
            ['Date Time', 'Matri Id', 'Member Name', 'Activity', 'Detail'],
            $rows,
            static function (array $r): array {
                $ts = strtotime((string) ($r['activity_at'] ?? ''));

                return [
                    $ts ? date('Y-m-d H:i:s', $ts) : '',
                    matri_id_display((string) ($r['matri_id'] ?? '')),
                    (string) ($r['member_name'] ?? ''),
                    (string) ($r['activity'] ?? ''),
                    (string) ($r['detail'] ?? ''),
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
