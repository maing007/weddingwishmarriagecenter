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
            ['WW17739', 'Miss rajpoot', 'WW15507', 'Syed Farhan Ahmad Bukhari', 'Ali Jawad', '2024-11-11 00:00:00', null],
            ['WW18002', 'Ayesha Khan', 'WW16001', 'Muhammad Usman', 'Ali Jawad', '2024-10-28 14:30:00', null],
            ['WW17200', 'Fatima Noor', 'WW15888', 'Hassan Raza', 'Samina Kashif', '2024-09-15 09:00:00', null],
            ['WW19001', 'Zainab Malik', 'WW16222', 'Omar Sheikh', 'Ali Jawad', '2024-12-01 18:45:00', null],
            ['WW16550', 'Sara Ahmed', 'WW17001', 'Bilal Hussain', 'Samina Kashif', '2024-08-20 00:00:00', null],
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

    /**
     * Log a defer action from the member dashboard discovery feed (admin Deferred Matches report).
     */
    public function insertFromDashboardFeed(int $viewerId, int $targetId): void
    {
        if (!function_exists('matri_id_display')) {
            require_once dirname(__DIR__) . '/helpers/matri.php';
        }

        $stmt = $this->db->prepare(
            'SELECT id, first_name, second_name, matri_id FROM user_details WHERE id IN (?, ?)'
        );
        $stmt->execute([$viewerId, $targetId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $byId = [];
        foreach ($rows as $r) {
            $byId[(int) $r['id']] = $r;
        }
        $v = $byId[$viewerId] ?? null;
        $t = $byId[$targetId] ?? null;
        if (!$v || !$t) {
            return;
        }

        $myName = trim(($v['first_name'] ?? '') . ' ' . ($v['second_name'] ?? ''));
        $otherName = trim(($t['first_name'] ?? '') . ' ' . ($t['second_name'] ?? ''));
        $myMid = matri_id_display($v['matri_id'] ?? '', $viewerId);
        $otherMid = matri_id_display($t['matri_id'] ?? '', $targetId);

        $ins = $this->db->prepare('
            INSERT INTO deferred_matches (my_matri_id, my_name, other_matri_id, other_name, staff_name, deferred_at, auto_deferred)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)
        ');
        $ins->execute([
            $myMid,
            $myName !== '' ? $myName : '-',
            $otherMid,
            $otherName !== '' ? $otherName : '-',
            'Member',
            'user_dashboard_feed',
        ]);
    }
}
