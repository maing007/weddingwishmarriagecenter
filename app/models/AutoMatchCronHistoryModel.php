<?php

class AutoMatchCronHistoryModel
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
            CREATE TABLE IF NOT EXISTS auto_match_cron_history (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(80) NOT NULL DEFAULT 'Completed',
                started_at DATETIME NOT NULL,
                ended_at DATETIME NULL,
                sent_emails_count INT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ended_at (ended_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->seedIfEmpty();
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM auto_match_cron_history')->fetchColumn();
        if ($n > 0) {
            return;
        }
        $now = time();
        $statuses = ['Completed', 'Completed', 'Running', 'Completed', 'Failed'];
        $stmt = $this->db->prepare('
            INSERT INTO auto_match_cron_history (status, started_at, ended_at, sent_emails_count)
            VALUES (?, ?, ?, ?)
        ');
        for ($i = 0; $i < 15; $i++) {
            $start = $now - ($i + 1) * 3600 - random_int(0, 900);
            $end = $start + random_int(120, 1800);
            $st = $statuses[$i % count($statuses)];
            $emails = random_int(0, 500);
            if ($st === 'Running') {
                $stmt->execute([$st, date('Y-m-d H:i:s', $start), null, $emails]);
            } else {
                $stmt->execute([$st, date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end), $emails]);
            }
        }
    }

    public function allRows(): array
    {
        $stmt = $this->db->query('
            SELECT * FROM auto_match_cron_history
            ORDER BY COALESCE(ended_at, started_at) DESC, id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM auto_match_cron_history WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM auto_match_cron_history WHERE id = ?');

        return $stmt->execute([$id]);
    }
}
