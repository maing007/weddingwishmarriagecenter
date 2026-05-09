<?php

class AdminLeadModel
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
            CREATE TABLE IF NOT EXISTS admin_leads (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                gender VARCHAR(50) DEFAULT NULL,
                lead_code VARCHAR(50) DEFAULT NULL,
                country VARCHAR(100) DEFAULT NULL,
                city VARCHAR(100) DEFAULT NULL,
                state VARCHAR(100) DEFAULT NULL,
                address VARCHAR(255) DEFAULT NULL,
                phone1 VARCHAR(50) DEFAULT NULL,
                phone2 VARCHAR(50) DEFAULT NULL,
                phone3 VARCHAR(50) DEFAULT NULL,
                phone4 VARCHAR(50) DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                country_id INT UNSIGNED NULL,
                interest_name VARCHAR(50) DEFAULT 'In-Process-M',
                team_assign VARCHAR(255) DEFAULT NULL,
                team_assign_id INT UNSIGNED NULL,
                importance VARCHAR(50) DEFAULT 'Important',
                reg_matri_id VARCHAR(50) DEFAULT NULL,
                reg_date DATETIME DEFAULT NULL,
                source_name VARCHAR(150) DEFAULT NULL,
                next_followup DATE DEFAULT NULL,
                created_by VARCHAR(100) DEFAULT 'Admin',
                staff_username VARCHAR(100) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_interest (interest_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->migrateAdminLeadColumns();

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS admin_lead_comments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                lead_id INT UNSIGNED NOT NULL,
                admin_id INT NOT NULL,
                comment LONGTEXT NOT NULL,
                comment_type VARCHAR(50) DEFAULT 'general',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_lead_id (lead_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->syncFromContactsIfEmpty();
    }

    private function migrateAdminLeadColumns(): void
    {
        $this->addColumnIfMissing('admin_leads', 'country_id', 'INT UNSIGNED NULL');
        $this->addColumnIfMissing('admin_leads', 'team_assign_id', 'INT UNSIGNED NULL');
        try {
            $this->db->exec('ALTER TABLE admin_leads MODIFY address TEXT NULL');
        } catch (Throwable $e) {
            // May already be TEXT or table missing
        }
    }

    private function addColumnIfMissing(string $table, string $column, string $definition): void
    {
        try {
            $dbName = $this->db->query('SELECT DATABASE()')->fetchColumn();
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
            );
            $stmt->execute([$dbName, $table, $column]);
            if ((int) $stmt->fetchColumn() === 0) {
                $this->db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
            }
        } catch (Throwable $e) {
            try {
                $this->db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
            } catch (Throwable $e2) {
                // ignore duplicate / permissions
            }
        }
    }

    private function syncFromContactsIfEmpty(): void
    {
        $count = (int) $this->db->query('SELECT COUNT(*) FROM admin_leads')->fetchColumn();
        if ($count > 0) {
            return;
        }
        try {
            $this->db->exec("
                INSERT INTO admin_leads (
                    full_name, gender, lead_code, country, city, state, address,
                    phone1, phone2, phone3, phone4, email, interest_name,
                    team_assign, importance, reg_matri_id, reg_date, source_name,
                    next_followup, created_by, staff_username
                )
                SELECT
                    c.name,
                    NULL,
                    CONCAT('OT-', c.id),
                    NULL, NULL, NULL, NULL,
                    c.phone, NULL, NULL, NULL,
                    NULLIF(TRIM(c.email), ''),
                    CASE WHEN (c.id % 5) IN (3, 4) THEN 'Registered' ELSE 'In-Process-M' END,
                    'Admin',
                    'Important',
                    NULL,
                    c.created_at,
                    LEFT(c.subject, 150),
                    NULL,
                    'Admin',
                    NULL
                FROM contacts c
                ORDER BY c.id DESC
                LIMIT 200
            ");
        } catch (Throwable $e) {
            // contacts table may differ; ignore
        }
    }

    public function allLeads(): array
    {
        $stmt = $this->db->query('SELECT * FROM admin_leads ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countByInterest(string $interest): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM admin_leads WHERE interest_name = ?');
        $stmt->execute([$interest]);
        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM admin_leads WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM admin_lead_comments WHERE lead_id = ?');
        $stmt->execute([$id]);
        $del = $this->db->prepare('DELETE FROM admin_leads WHERE id = ?');

        return $del->execute([$id]);
    }

    public function create(array $data): bool
    {
        $sql = <<<'SQL'
INSERT INTO admin_leads (
            full_name, gender, lead_code, country, country_id, city, state, address,
            phone1, phone2, phone3, phone4, email, interest_name,
            team_assign, team_assign_id, importance, reg_matri_id, reg_date, source_name,
            next_followup, created_by, staff_username
        ) VALUES (
            :full_name, :gender, :lead_code, :country, :country_id, :city, :state, :address,
            :phone1, :phone2, :phone3, :phone4, :email, :interest_name,
            :team_assign, :team_assign_id, :importance, :reg_matri_id, :reg_date, :source_name,
            :next_followup, :created_by, :staff_username
        )
SQL;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':gender' => $data['gender'] ?: null,
            ':lead_code' => $data['lead_code'] ?: null,
            ':country' => $data['country'] ?: null,
            ':country_id' => $data['country_id'] ?: null,
            ':city' => $data['city'] ?: null,
            ':state' => $data['state'] ?: null,
            ':address' => $data['address'] ?: null,
            ':phone1' => $data['phone1'] ?: null,
            ':phone2' => $data['phone2'] ?: null,
            ':phone3' => $data['phone3'] ?: null,
            ':phone4' => $data['phone4'] ?: null,
            ':email' => $data['email'] ?: null,
            ':interest_name' => $data['interest_name'] ?: 'In-Process-M',
            ':team_assign' => $data['team_assign'] ?: null,
            ':team_assign_id' => $data['team_assign_id'] ?: null,
            ':importance' => $data['importance'] ?: 'Important',
            ':reg_matri_id' => $data['reg_matri_id'] ?: null,
            ':reg_date' => $data['reg_date'] ?: date('Y-m-d H:i:s'),
            ':source_name' => $data['source_name'] ?: null,
            ':next_followup' => $data['next_followup'] ?: null,
            ':created_by' => $data['created_by'] ?: 'Admin',
            ':staff_username' => $data['staff_username'] ?: null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = <<<'SQL'
UPDATE admin_leads SET
            full_name = :full_name, gender = :gender, lead_code = :lead_code,
            country = :country, country_id = :country_id, city = :city, state = :state, address = :address,
            phone1 = :phone1, phone2 = :phone2, phone3 = :phone3, phone4 = :phone4,
            email = :email, interest_name = :interest_name, team_assign = :team_assign,
            team_assign_id = :team_assign_id, importance = :importance, reg_matri_id = :reg_matri_id, reg_date = :reg_date,
            source_name = :source_name, next_followup = :next_followup,
            staff_username = :staff_username
        WHERE id = :id
SQL;
        $stmt = $this->db->prepare($sql);
        $params = [
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':gender' => $data['gender'] ?: null,
            ':lead_code' => $data['lead_code'] ?: null,
            ':country' => $data['country'] ?: null,
            ':country_id' => $data['country_id'] ?: null,
            ':city' => $data['city'] ?: null,
            ':state' => $data['state'] ?: null,
            ':address' => $data['address'] ?: null,
            ':phone1' => $data['phone1'] ?: null,
            ':phone2' => $data['phone2'] ?: null,
            ':phone3' => $data['phone3'] ?: null,
            ':phone4' => $data['phone4'] ?: null,
            ':email' => $data['email'] ?: null,
            ':interest_name' => $data['interest_name'] ?: 'In-Process-M',
            ':team_assign' => $data['team_assign'] ?: null,
            ':team_assign_id' => $data['team_assign_id'] ?: null,
            ':importance' => $data['importance'] ?: 'Important',
            ':reg_matri_id' => $data['reg_matri_id'] ?: null,
            ':reg_date' => $data['reg_date'] ?: null,
            ':source_name' => $data['source_name'] ?: null,
            ':next_followup' => $data['next_followup'] ?: null,
            ':staff_username' => $data['staff_username'] ?: null,
        ];
        return $stmt->execute($params);
    }

    public function bulkUpdateInterest(array $ids, string $interest): bool
    {
        $allowed = ['In-Process-M', 'Registered', 'Closed-M'];
        if (!in_array($interest, $allowed, true) || empty($ids)) {
            return false;
        }
        $ids = array_values(array_filter(array_map('intval', $ids), static function ($id) {
            return $id > 0;
        }));
        if (empty($ids)) {
            return false;
        }
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([$interest], $ids);
        $stmt = $this->db->prepare("UPDATE admin_leads SET interest_name = ? WHERE id IN ($ph)");
        return $stmt->execute($params);
    }

    public function addComment(int $leadId, int $adminId, string $comment, string $type = 'general'): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO admin_lead_comments (lead_id, admin_id, comment, comment_type)
            VALUES (:lead_id, :admin_id, :comment, :comment_type)
        ');
        return $stmt->execute([
            ':lead_id' => $leadId,
            ':admin_id' => $adminId,
            ':comment' => $comment,
            ':comment_type' => $type,
        ]);
    }

    public function getComments(int $leadId, array $filters = []): array
    {
        $sql = '
            SELECT c.*, a.name AS admin_name
            FROM admin_lead_comments c
            LEFT JOIN admin_users a ON a.id = c.admin_id
            WHERE c.lead_id = :lead_id
        ';
        $params = [':lead_id' => $leadId];
        if (!empty($filters['type'])) {
            $sql .= ' AND c.comment_type = :type';
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(c.created_at) >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(c.created_at) <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }
        $sql .= ' ORDER BY c.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
