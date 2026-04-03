<?php

class StaffActivityModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS staff_activity (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                activity_at DATETIME NOT NULL,
                staff_name VARCHAR(150) NOT NULL,
                team_name VARCHAR(255) NOT NULL DEFAULT '-',
                main_topic VARCHAR(120) NOT NULL DEFAULT '',
                activity VARCHAR(120) NOT NULL DEFAULT '',
                department_name VARCHAR(120) NOT NULL DEFAULT '',
                matri_id VARCHAR(32) NOT NULL DEFAULT '',
                member_name VARCHAR(255) NOT NULL DEFAULT '',
                detail TEXT,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_activity_at (activity_at),
                INDEX idx_staff (staff_name(32))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->seedIfEmpty();
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM staff_activity')->fetchColumn();
        if ($n > 0) {
            return;
        }
        $rows = [
            ['2026-03-30 23:37:46', 'Tooba Ahsan', '-', 'Members', 'Member Edited', 'Match Making', 'NG21806', 'Syeda Abeer', 'Member Syeda Abeer NG21806 edited.'],
            ['2026-03-29 14:22:10', 'Tooba Ahsan', 'Tooba Ahsan N Hira', 'Members', 'Profile Viewed', 'Match Making', 'NG21806', 'Syeda Abeer', 'Syeda Abeer NG21806, profile viewed.'],
            ['2026-03-28 09:15:00', 'Ali Jawad', '-', 'Members', 'Confirm email', 'Match Making', 'NG19001', 'Zainab Malik', 'Email confirmation sent for Zainab Malik.'],
            ['2026-03-27 11:40:22', 'Samina Kashif', '-', 'Communication', 'Comments Added', 'Lead Generation', 'NG17200', 'Fatima Noor', 'Comments added on profile NG17200.'],
            ['2026-03-26 18:05:33', 'Tooba Ahsan', '-', 'Members', 'Member Edited', 'Match Making', 'NG15507', 'Syed Farhan Ahmad Bukhari', 'Member profile updated.'],
            ['2026-03-25 10:00:01', 'Ali Jawad', '-', 'Payments', 'Invoice Viewed', 'Payments', 'NG18002', 'Ayesha Khan', 'Invoice viewed for member.'],
            ['2026-03-24 16:30:00', 'Hira Khan', 'Team Alpha', 'Meetings', 'Meeting Scheduled', 'Meetings', 'NG16550', 'Sara Ahmed', 'Meeting scheduled with Sara Ahmed.'],
            ['2026-03-23 08:45:12', 'Tooba Ahsan', '-', 'Members', 'Profile Viewed', 'Match Making', 'NG21806', 'Syeda Abeer', 'Profile NG21806 viewed.'],
            ['2026-03-22 13:20:45', 'Samina Kashif', '-', 'Members', 'Member Edited', 'Match Making', 'NG16001', 'Muhammad Usman', 'Member Muhammad Usman edited.'],
            ['2026-03-21 19:55:00', 'Ali Jawad', '-', 'Members', 'Confirm email', 'Match Making', 'NG16222', 'Omar Sheikh', 'Confirm email for Omar Sheikh.'],
            ['2026-03-20 12:10:00', 'Tooba Ahsan', 'Tooba Ahsan N Hira', 'Members', 'Comments Added', 'Match Making', 'NG15888', 'Hassan Raza', 'Internal comments added.'],
            ['2026-03-19 07:30:00', 'Ali Jawad', '-', 'Members', 'Profile Viewed', 'Match Making', 'NG17001', 'Bilal Hussain', 'Bilal Hussain NG17001, profile viewed.'],
        ];
        $stmt = $this->db->prepare('
            INSERT INTO staff_activity (activity_at, staff_name, team_name, main_topic, activity, department_name, matri_id, member_name, detail)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($rows as $r) {
            $stmt->execute($r);
        }
    }

    public function allRows(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM staff_activity
            ORDER BY activity_at DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Aggregated rows for Staff Activity Summary report. */
    public function summaryRows(): array
    {
        $stmt = $this->db->query('
            SELECT
                staff_name,
                team_name,
                department_name,
                COUNT(*) AS total_activities,
                MAX(activity_at) AS last_activity_at
            FROM staff_activity
            GROUP BY staff_name, team_name, department_name
            ORDER BY last_activity_at DESC, staff_name ASC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
