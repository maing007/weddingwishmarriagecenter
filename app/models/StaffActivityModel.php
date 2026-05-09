<?php

class StaffActivityModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
        $this->ensureTable();
        $this->ensureAdminLinkColumns();
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

    private function ensureAdminLinkColumns(): void
    {
        $check = $this->db->query("SHOW COLUMNS FROM staff_activity LIKE 'admin_id'")->fetch(PDO::FETCH_ASSOC);
        if ($check) {
            return;
        }
        $this->db->exec("
            ALTER TABLE staff_activity
                ADD COLUMN admin_id INT UNSIGNED NULL DEFAULT NULL AFTER id,
                ADD COLUMN admin_role VARCHAR(120) NOT NULL DEFAULT '' AFTER admin_id,
                ADD INDEX idx_staff_activity_admin (admin_id)
        ");
        $this->backfillAdminLinksFromStaffNames();
    }

    private function backfillAdminLinksFromStaffNames(): void
    {
        try {
            $this->db->exec("
                UPDATE staff_activity sa
                INNER JOIN admin_users au
                    ON LOWER(TRIM(sa.staff_name)) = LOWER(TRIM(au.name))
                SET
                    sa.admin_id = au.id,
                    sa.admin_role = TRIM(COALESCE(
                        NULLIF(TRIM(au.role), ''),
                        NULLIF(TRIM(au.department), ''),
                        ''
                    ))
                WHERE sa.admin_id IS NULL
            ");
        } catch (Throwable $e) {
            error_log('StaffActivityModel::backfillAdminLinksFromStaffNames: ' . $e->getMessage());
        }
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM staff_activity')->fetchColumn();
        if ($n > 0) {
            return;
        }
        $rows = [
            ['2026-03-30 23:37:46', 'Tooba Ahsan', '-', 'Members', 'Member Edited', 'Match Making', 'WW21806', 'Syeda Abeer', 'Member Syeda Abeer WW21806 edited.'],
            ['2026-03-29 14:22:10', 'Tooba Ahsan', 'Tooba Ahsan N Hira', 'Members', 'Profile Viewed', 'Match Making', 'WW21806', 'Syeda Abeer', 'Syeda Abeer WW21806, profile viewed.'],
            ['2026-03-28 09:15:00', 'Ali Jawad', '-', 'Members', 'Confirm email', 'Match Making', 'WW19001', 'Zainab Malik', 'Email confirmation sent for Zainab Malik.'],
            ['2026-03-27 11:40:22', 'Samina Kashif', '-', 'Communication', 'Comments Added', 'Lead Generation', 'WW17200', 'Fatima Noor', 'Comments added on profile WW17200.'],
            ['2026-03-26 18:05:33', 'Tooba Ahsan', '-', 'Members', 'Member Edited', 'Match Making', 'WW15507', 'Syed Farhan Ahmad Bukhari', 'Member profile updated.'],
            ['2026-03-25 10:00:01', 'Ali Jawad', '-', 'Payments', 'Invoice Viewed', 'Payments', 'WW18002', 'Ayesha Khan', 'Invoice viewed for member.'],
            ['2026-03-24 16:30:00', 'Hira Khan', 'Team Alpha', 'Meetings', 'Meeting Scheduled', 'Meetings', 'WW16550', 'Sara Ahmed', 'Meeting scheduled with Sara Ahmed.'],
            ['2026-03-23 08:45:12', 'Tooba Ahsan', '-', 'Members', 'Profile Viewed', 'Match Making', 'WW21806', 'Syeda Abeer', 'Profile WW21806 viewed.'],
            ['2026-03-22 13:20:45', 'Samina Kashif', '-', 'Members', 'Member Edited', 'Match Making', 'WW16001', 'Muhammad Usman', 'Member Muhammad Usman edited.'],
            ['2026-03-21 19:55:00', 'Ali Jawad', '-', 'Members', 'Confirm email', 'Match Making', 'WW16222', 'Omar Sheikh', 'Confirm email for Omar Sheikh.'],
            ['2026-03-20 12:10:00', 'Tooba Ahsan', 'Tooba Ahsan N Hira', 'Members', 'Comments Added', 'Match Making', 'WW15888', 'Hassan Raza', 'Internal comments added.'],
            ['2026-03-19 07:30:00', 'Ali Jawad', '-', 'Members', 'Profile Viewed', 'Match Making', 'WW17001', 'Bilal Hussain', 'Bilal Hussain WW17001, profile viewed.'],
        ];
        $stmt = $this->db->prepare('
            INSERT INTO staff_activity (activity_at, staff_name, team_name, main_topic, activity, department_name, matri_id, member_name, detail)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($rows as $r) {
            $stmt->execute($r);
        }
    }

    /**
     * Activity rows with resolved admin role (from stored admin_role or admin_users.role / department).
     */
    public function allRows(): array
    {
        $hasAdminId = $this->db->query("SHOW COLUMNS FROM staff_activity LIKE 'admin_id'")->fetch(PDO::FETCH_ASSOC);
        if (!$hasAdminId) {
            $stmt = $this->db->query('
                SELECT * FROM staff_activity
                ORDER BY activity_at DESC, id DESC
            ');

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        $sql = "
            SELECT
                sa.id,
                sa.admin_id,
                sa.admin_role,
                sa.activity_at,
                sa.staff_name,
                sa.team_name,
                sa.main_topic,
                sa.activity,
                sa.department_name,
                sa.matri_id,
                sa.member_name,
                sa.detail,
                sa.created_at,
                COALESCE(
                    NULLIF(TRIM(sa.admin_role), ''),
                    NULLIF(TRIM(au1.role), ''),
                    NULLIF(TRIM(au2.role), ''),
                    NULLIF(TRIM(au1.department), ''),
                    NULLIF(TRIM(au2.department), ''),
                    '—'
                ) AS staff_role
            FROM staff_activity sa
            LEFT JOIN admin_users au1 ON sa.admin_id IS NOT NULL AND au1.id = sa.admin_id
            LEFT JOIN admin_users au2 ON sa.admin_id IS NULL AND au2.id = (
                SELECT MIN(aux.id) FROM admin_users aux
                WHERE LOWER(TRIM(aux.name)) = LOWER(TRIM(sa.staff_name))
            )
            ORDER BY sa.activity_at DESC, sa.id DESC
        ";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Aggregated rows for Staff Activity Summary report. */
    public function summaryRows(): array
    {
        $hasAdminId = $this->db->query("SHOW COLUMNS FROM staff_activity LIKE 'admin_id'")->fetch(PDO::FETCH_ASSOC);
        if (!$hasAdminId) {
            $stmt = $this->db->query('
                SELECT
                    staff_name,
                    team_name,
                    department_name,
                    \'—\' AS staff_role,
                    COUNT(*) AS total_activities,
                    MAX(activity_at) AS last_activity_at
                FROM staff_activity
                GROUP BY staff_name, team_name, department_name
                ORDER BY last_activity_at DESC, staff_name ASC
            ');

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        $stmt = $this->db->query('
            SELECT
                sa.staff_name,
                sa.team_name,
                sa.department_name,
                MAX(
                    COALESCE(
                        NULLIF(TRIM(sa.admin_role), \'\'),
                        NULLIF(TRIM(au1.role), \'\'),
                        NULLIF(TRIM(au2.role), \'\'),
                        NULLIF(TRIM(au1.department), \'\'),
                        NULLIF(TRIM(au2.department), \'\'),
                        \'—\'
                    )
                ) AS staff_role,
                COUNT(*) AS total_activities,
                MAX(sa.activity_at) AS last_activity_at
            FROM staff_activity sa
            LEFT JOIN admin_users au1 ON sa.admin_id IS NOT NULL AND au1.id = sa.admin_id
            LEFT JOIN admin_users au2 ON sa.admin_id IS NULL AND au2.id = (
                SELECT MIN(aux.id) FROM admin_users aux
                WHERE LOWER(TRIM(aux.name)) = LOWER(TRIM(sa.staff_name))
            )
            GROUP BY sa.staff_name, sa.team_name, sa.department_name
            ORDER BY last_activity_at DESC, sa.staff_name ASC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Append an activity row for the current admin (optional hook for future logging).
     */
    public function log(
        int $adminId,
        string $staffName,
        string $staffRole,
        array $fields
    ): bool {
        $hasAdminId = $this->db->query("SHOW COLUMNS FROM staff_activity LIKE 'admin_id'")->fetch(PDO::FETCH_ASSOC);
        if (!$hasAdminId) {
            return false;
        }
        $stmt = $this->db->prepare('
            INSERT INTO staff_activity (
                admin_id, admin_role, activity_at, staff_name, team_name, main_topic,
                activity, department_name, matri_id, member_name, detail
            ) VALUES (
                :admin_id, :admin_role, :activity_at, :staff_name, :team_name, :main_topic,
                :activity, :department_name, :matri_id, :member_name, :detail
            )
        ');

        return $stmt->execute([
            ':admin_id' => $adminId,
            ':admin_role' => $staffRole,
            ':activity_at' => $fields['activity_at'] ?? date('Y-m-d H:i:s'),
            ':staff_name' => $staffName,
            ':team_name' => $fields['team_name'] ?? '-',
            ':main_topic' => $fields['main_topic'] ?? '',
            ':activity' => $fields['activity'] ?? '',
            ':department_name' => $fields['department_name'] ?? '',
            ':matri_id' => $fields['matri_id'] ?? '',
            ':member_name' => $fields['member_name'] ?? '',
            ':detail' => $fields['detail'] ?? '',
        ]);
    }
}
