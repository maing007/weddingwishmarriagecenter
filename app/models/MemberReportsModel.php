<?php

class MemberReportsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
        $this->ensureTables();
    }

    private function ensureTables(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS members_email_verification (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                matri_id VARCHAR(32) NOT NULL,
                username VARCHAR(255) NOT NULL DEFAULT '',
                email VARCHAR(255) NOT NULL DEFAULT '',
                mobile VARCHAR(64) NOT NULL DEFAULT '',
                last_login DATETIME NULL,
                team_assign_id VARCHAR(64) NOT NULL DEFAULT '',
                is_verified TINYINT(1) NOT NULL DEFAULT 0,
                is_paid TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_matri (matri_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS members_summary_report (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                matri_id VARCHAR(32) NOT NULL,
                username VARCHAR(255) NOT NULL DEFAULT '',
                email VARCHAR(255) NOT NULL DEFAULT '',
                package_name VARCHAR(120) NOT NULL DEFAULT '',
                membership_status VARCHAR(80) NOT NULL DEFAULT '',
                expiry_date DATE NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_matri (matri_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS unsubscribe_members_report (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                matri_id VARCHAR(32) NOT NULL DEFAULT '',
                email VARCHAR(255) NOT NULL DEFAULT '',
                unsubscribed_at DATETIME NOT NULL,
                channel VARCHAR(120) NOT NULL DEFAULT '',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email(64))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS members_all_activity_report (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                activity_at DATETIME NOT NULL,
                matri_id VARCHAR(32) NOT NULL DEFAULT '',
                member_name VARCHAR(255) NOT NULL DEFAULT '',
                activity VARCHAR(120) NOT NULL DEFAULT '',
                detail TEXT,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_activity_at (activity_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function allEmailVerification(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM members_email_verification
            ORDER BY id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function allMembersSummary(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM members_summary_report
            ORDER BY expiry_date DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function allUnsubscribeMembers(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM unsubscribe_members_report
            ORDER BY unsubscribed_at DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function allMembersActivity(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM members_all_activity_report
            ORDER BY activity_at DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
