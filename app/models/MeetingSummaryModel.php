<?php

class MeetingSummaryModel
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
            CREATE TABLE IF NOT EXISTS meeting_summaries (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                meeting_date DATE NOT NULL,
                meeting_number VARCHAR(64) NOT NULL DEFAULT '',
                arranged_by VARCHAR(150) NOT NULL DEFAULT '',
                ti_name VARCHAR(150) NOT NULL DEFAULT '',
                customer_support VARCHAR(150) NOT NULL DEFAULT '',
                client_id VARCHAR(64) NOT NULL DEFAULT '',
                client_name VARCHAR(255) NOT NULL DEFAULT '',
                match_id VARCHAR(64) NOT NULL DEFAULT '',
                match_name VARCHAR(255) NOT NULL DEFAULT '',
                meeting_status VARCHAR(80) NOT NULL DEFAULT '',
                meeting_outcome VARCHAR(255) NOT NULL DEFAULT '',
                staff_approval VARCHAR(80) NOT NULL DEFAULT '',
                payment VARCHAR(120) NOT NULL DEFAULT '',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_meeting_date (meeting_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function allRows(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM meeting_summaries
            ORDER BY meeting_date DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
