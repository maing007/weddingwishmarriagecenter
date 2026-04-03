<?php

class DeferredMatchModel
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
            CREATE TABLE IF NOT EXISTS deferred_matches (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                my_matri_id VARCHAR(32) NOT NULL,
                my_name VARCHAR(255) NOT NULL,
                other_matri_id VARCHAR(32) NOT NULL,
                other_name VARCHAR(255) NOT NULL,
                staff_name VARCHAR(150) DEFAULT NULL,
                deferred_at DATETIME NOT NULL,
                auto_deferred VARCHAR(80) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_deferred (deferred_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->seedIfEmpty();
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM deferred_matches')->fetchColumn();
        if ($n > 0) {
            return;
        }
        $rows = [
            ['NG17739', 'Miss rajpoot', 'NG15507', 'Syed Farhan Ahmad Bukhari', 'Ali Jawad', '2024-11-11 00:00:00', null],
            ['NG18002', 'Ayesha Khan', 'NG16001', 'Muhammad Usman', 'Ali Jawad', '2024-10-28 14:30:00', null],
            ['NG17200', 'Fatima Noor', 'NG15888', 'Hassan Raza', 'Samina Kashif', '2024-09-15 09:00:00', null],
            ['NG19001', 'Zainab Malik', 'NG16222', 'Omar Sheikh', 'Ali Jawad', '2024-12-01 18:45:00', null],
            ['NG16550', 'Sara Ahmed', 'NG17001', 'Bilal Hussain', 'Samina Kashif', '2024-08-20 00:00:00', null],
        ];
        $stmt = $this->db->prepare('
            INSERT INTO deferred_matches (my_matri_id, my_name, other_matri_id, other_name, staff_name, deferred_at, auto_deferred)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($rows as $r) {
            $stmt->execute($r);
        }
    }

    public function allRows(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM deferred_matches
            ORDER BY id ASC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
