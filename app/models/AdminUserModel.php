<?php

class AdminUserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
        $this->ensureAdminTables();
    }

    private function ensureAdminTables(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS admin_profile_comments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                admin_id INT NOT NULL,
                comment LONGTEXT NOT NULL,
                comment_type VARCHAR(50) DEFAULT 'general',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS admin_tasks (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                task_name VARCHAR(255) NOT NULL,
                assigned_admin_id INT NULL,
                status VARCHAR(50) DEFAULT 'open',
                activity VARCHAR(255) NULL,
                main_topic VARCHAR(255) NULL,
                task_meeting VARCHAR(255) NULL,
                date_from DATE NULL,
                date_to DATE NULL,
                priority VARCHAR(50) NULL,
                details LONGTEXT NULL,
                image_path VARCHAR(255) NULL,
                attachment_path VARCHAR(255) NULL,
                admin_comment LONGTEXT NULL,
                visible_to VARCHAR(255) NULL,
                created_by INT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_task_user (user_id),
                INDEX idx_task_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS admin_member_evaluations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                admin_id INT NOT NULL,
                answers LONGTEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_user_eval (user_id),
                INDEX idx_eval_admin (admin_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function allUsers(?string $dashboardFilter = null)
    {
        $sql = "
            SELECT
                id,
                NULL AS avatar,
                phone,
                dob,
                religion,
                gender,
                COALESCE(user_status, 'approved') AS status,
                NULL AS admin_comment,
                first_name,
                second_name AS last_name,
                email,
                city,
                country,
                matri_id,
                COALESCE(featured_status, 'non_featured') AS featured_status,
                (
                    SELECT up.id
                    FROM user_packages up
                    WHERE up.user_id = user_details.id
                    ORDER BY up.id DESC
                    LIMIT 1
                ) AS latest_package_id,
                (
                    SELECT COALESCE(SUM(ma.opened_count), 0)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id
                ) AS opened_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'pending'
                ) AS deferred_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'declined'
                ) AS declined_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'meeting'
                ) AS meeting_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'accepted'
                ) AS accepted_count,
                created_at
            FROM user_details
        ";

        $conditions = [];
        $params = [];

        switch ($dashboardFilter) {
            case 'today':
                $conditions[] = "DATE(created_at) = CURDATE()";
                break;
            case 'last_week':
                $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'last_month':
                $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'male':
                $conditions[] = "LOWER(COALESCE(gender, '')) = :gender_male";
                $params[':gender_male'] = 'male';
                break;
            case 'female':
                $conditions[] = "LOWER(COALESCE(gender, '')) = :gender_female";
                $params[':gender_female'] = 'female';
                break;
            case 'active':
                // users table has no status column in current schema; treat all as active
                break;
            case 'paid':
                $conditions[] = "EXISTS (SELECT 1 FROM user_packages up WHERE up.user_id = user_details.id AND (up.is_paid = 1 OR LOWER(COALESCE(up.status, '')) = 'paid'))";
                break;
            case 'total':
            default:
                break;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function spotlightUsers(?string $featuredFilter = null): array
    {
        $sql = "
            SELECT
                id,
                NULL AS avatar,
                phone,
                dob,
                religion,
                gender,
                COALESCE(user_status, 'approved') AS status,
                first_name,
                second_name AS last_name,
                email,
                city,
                country,
                matri_id,
                COALESCE(featured_status, 'non_featured') AS featured_status,
                (
                    SELECT up.id
                    FROM user_packages up
                    WHERE up.user_id = user_details.id
                    ORDER BY up.id DESC
                    LIMIT 1
                ) AS latest_package_id,
                (
                    SELECT COALESCE(SUM(ma.opened_count), 0)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id
                ) AS opened_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'pending'
                ) AS deferred_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'declined'
                ) AS declined_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'meeting'
                ) AS meeting_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'accepted'
                ) AS accepted_count,
                created_at
            FROM user_details
            WHERE EXISTS (
                SELECT 1 FROM user_packages up
                WHERE up.user_id = user_details.id
                  AND (up.is_paid = 1 OR LOWER(COALESCE(up.status, '')) = 'paid' OR LOWER(COALESCE(up.status, '')) = 'active')
            )
        ";
        $params = [];

        if ($featuredFilter === 'featured') {
            $sql .= " AND LOWER(COALESCE(featured_status, '')) = 'featured'";
        } elseif ($featuredFilter === 'non_featured') {
            $sql .= " AND (LOWER(COALESCE(featured_status, '')) IN ('', 'non_featured', 'nonfeatured', 'non featured', 'default'))";
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function expiredMembershipUsers(?string $statusFilter = null): array
    {
        $sql = "
            SELECT
                id,
                NULL AS avatar,
                phone,
                dob,
                religion,
                gender,
                COALESCE(user_status, 'approved') AS status,
                first_name,
                second_name AS last_name,
                email,
                city,
                country,
                matri_id,
                COALESCE(featured_status, 'non_featured') AS featured_status,
                (
                    SELECT up.id
                    FROM user_packages up
                    WHERE up.user_id = user_details.id
                    ORDER BY up.id DESC
                    LIMIT 1
                ) AS latest_package_id,
                (
                    SELECT COALESCE(SUM(ma.opened_count), 0)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id
                ) AS opened_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'pending'
                ) AS deferred_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'declined'
                ) AS declined_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'meeting'
                ) AS meeting_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = user_details.id AND LOWER(COALESCE(ma.status, '')) = 'accepted'
                ) AS accepted_count,
                created_at
            FROM user_details
            WHERE EXISTS (
                SELECT 1
                FROM user_packages up
                WHERE up.user_id = user_details.id
                  AND (
                    (up.expires_at IS NOT NULL AND up.expires_at < CURDATE())
                    OR (up.end_date IS NOT NULL AND up.end_date < CURDATE())
                  )
            )
        ";
        $params = [];
        if ($statusFilter === 'approved' || $statusFilter === 'unapproved') {
            $sql .= " AND LOWER(COALESCE(user_status, '')) = :status_filter";
            $params[':status_filter'] = $statusFilter;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function followupReportUsers(string $followupFilter = 'all'): array
    {
        $sql = "
            SELECT
                ud.id,
                ud.first_name,
                ud.second_name AS last_name,
                ud.email,
                ud.phone,
                ud.gender,
                ud.country,
                ud.city,
                ud.dob,
                ud.created_at,
                COALESCE(ud.user_status, 'approved') AS status,
                fc.comment AS followup_comment,
                fc.created_at AS followup_at
            FROM user_details ud
            LEFT JOIN (
                SELECT c1.user_id, c1.comment, c1.created_at
                FROM admin_profile_comments c1
                INNER JOIN (
                    SELECT user_id, MAX(created_at) AS max_created
                    FROM admin_profile_comments
                    WHERE LOWER(COALESCE(comment_type, '')) = 'follow_up'
                    GROUP BY user_id
                ) c2 ON c1.user_id = c2.user_id AND c1.created_at = c2.max_created
                WHERE LOWER(COALESCE(c1.comment_type, '')) = 'follow_up'
            ) fc ON fc.user_id = ud.id
            WHERE 1=1
        ";

        if ($followupFilter === 'today') {
            $sql .= " AND DATE(fc.created_at) = CURDATE()";
        } elseif ($followupFilter === 'previous') {
            $sql .= " AND DATE(fc.created_at) < CURDATE()";
        } elseif ($followupFilter === 'next') {
            $sql .= " AND DATE(fc.created_at) > CURDATE()";
        }

        $sql .= " ORDER BY ud.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function followupReportCounts(): array
    {
        $all = (int)$this->db->query("SELECT COUNT(*) FROM user_details")->fetchColumn();
        $today = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) FROM admin_profile_comments WHERE LOWER(COALESCE(comment_type,''))='follow_up' AND DATE(created_at)=CURDATE()")->fetchColumn();
        $previous = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) FROM admin_profile_comments WHERE LOWER(COALESCE(comment_type,''))='follow_up' AND DATE(created_at)<CURDATE()")->fetchColumn();
        $next = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) FROM admin_profile_comments WHERE LOWER(COALESCE(comment_type,''))='follow_up' AND DATE(created_at)>CURDATE()")->fetchColumn();
        return ['all' => $all, 'today' => $today, 'previous' => $previous, 'next' => $next];
    }
     public function more_details()
    {
        $more = $this->db->query("
            SELECT id, education, annual_income, height ,mother_tongue, smoking, cast, created_at
            FROM user_profile_details
            ORDER BY created_at DESC
        ");
        return $more->fetchAll(PDO::FETCH_ASSOC);
    }


    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user_details WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function bulkUpdateUserStatus(array $ids, string $status): bool
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE user_details SET user_status = ? WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);

        $params = array_merge([$status], array_map('intval', $ids));
        return $stmt->execute($params);
    }

    public function bulkUpdateFeaturedStatus(array $ids, string $status): bool
    {
        if (empty($ids)) {
            return false;
        }
        $status = $status === 'featured' ? 'featured' : 'non_featured';
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE user_details SET featured_status = ? WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $params = array_merge([$status], array_map('intval', $ids));
        return $stmt->execute($params);
    }

    public function getInteractionDetails(int $assignedTo, string $action): array
    {
        $action = strtolower(trim($action));

        if (in_array($action, ['opened', 'accepted', 'declined'], true)) {
            $sql = "
                SELECT
                    h.user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    COUNT(*) AS action_count,
                    MAX(h.created_at) AS last_action_at
                FROM member_assignment_history h
                INNER JOIN member_assignments ma ON ma.id = h.assignment_id
                LEFT JOIN users u ON u.id = h.user_id
                WHERE ma.assigned_to = :assigned_to
                  AND LOWER(COALESCE(h.action, '')) = :action
                GROUP BY h.user_id, u.first_name, u.last_name, u.email
                ORDER BY action_count DESC, last_action_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':assigned_to' => $assignedTo,
                ':action' => $action
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (in_array($action, ['deferred', 'meeting'], true)) {
            $status = $action === 'deferred' ? 'pending' : 'meeting';
            $sql = "
                SELECT
                    ma.assigned_member AS user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    COUNT(*) AS action_count,
                    MAX(ma.updated_at) AS last_action_at
                FROM member_assignments ma
                LEFT JOIN users u ON u.id = ma.assigned_member
                WHERE ma.assigned_to = :assigned_to
                  AND LOWER(COALESCE(ma.status, '')) = :status
                GROUP BY ma.assigned_member, u.first_name, u.last_name, u.email
                ORDER BY action_count DESC, last_action_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':assigned_to' => $assignedTo,
                ':status' => $status
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function addProfileComment(int $userId, int $adminId, string $comment, string $type = 'general'): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO admin_profile_comments (user_id, admin_id, comment, comment_type)
            VALUES (:user_id, :admin_id, :comment, :comment_type)
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':admin_id' => $adminId,
            ':comment' => $comment,
            ':comment_type' => $type,
        ]);
    }

    public function getProfileComments(int $userId, array $filters = []): array
    {
        $sql = "
            SELECT c.*, a.name AS admin_name
            FROM admin_profile_comments c
            LEFT JOIN admin_users a ON a.id = c.admin_id
            WHERE c.user_id = :user_id
        ";
        $params = [':user_id' => $userId];

        if (!empty($filters['type'])) {
            $sql .= " AND c.comment_type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(c.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(c.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allAdminUsers(): array
    {
        $stmt = $this->db->query("SELECT id, name, email FROM admin_users ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO admin_tasks
            (user_id, task_name, assigned_admin_id, status, activity, main_topic, task_meeting, date_from, date_to, priority, details, image_path, attachment_path, admin_comment, visible_to, created_by)
            VALUES
            (:user_id, :task_name, :assigned_admin_id, :status, :activity, :main_topic, :task_meeting, :date_from, :date_to, :priority, :details, :image_path, :attachment_path, :admin_comment, :visible_to, :created_by)
        ");
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':task_name' => $data['task_name'],
            ':assigned_admin_id' => $data['assigned_admin_id'] ?: null,
            ':status' => $data['status'] ?: 'open',
            ':activity' => $data['activity'] ?: null,
            ':main_topic' => $data['main_topic'] ?: null,
            ':task_meeting' => $data['task_meeting'] ?: null,
            ':date_from' => $data['date_from'] ?: null,
            ':date_to' => $data['date_to'] ?: null,
            ':priority' => $data['priority'] ?: null,
            ':details' => $data['details'] ?: null,
            ':image_path' => $data['image_path'] ?: null,
            ':attachment_path' => $data['attachment_path'] ?: null,
            ':admin_comment' => $data['admin_comment'] ?: null,
            ':visible_to' => $data['visible_to'] ?: null,
            ':created_by' => $data['created_by'],
        ]);
    }

    public function getUserDetailsById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_details WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getEditableColumns(): array
    {
        $rows = $this->db->query("SHOW COLUMNS FROM user_details")->fetchAll(PDO::FETCH_ASSOC);
        $all = array_map(static function ($r) {
            return $r['Field'];
        }, $rows);
        return array_values(array_filter($all, static function ($col) {
            return !in_array($col, ['id', 'created_at'], true);
        }));
    }

    public function updateAllUserDetails(int $id, array $input): bool
    {
        $columns = $this->getEditableColumns();
        $setParts = [];
        $params = [':id' => $id];

        foreach ($columns as $col) {
            if (!array_key_exists($col, $input)) {
                continue;
            }
            $setParts[] = "`$col` = :$col";
            $params[":$col"] = $input[$col] === '' ? null : $input[$col];
        }

        if (empty($setParts)) {
            return false;
        }

        $sql = "UPDATE user_details SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function distinctValues(string $table, string $column): array
    {
        $stmt = $this->db->query("SELECT DISTINCT `$column` AS val FROM `$table` WHERE `$column` IS NOT NULL AND TRIM(`$column`) <> '' ORDER BY `$column` ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_values(array_filter(array_map(static function ($r) {
            return trim((string)($r['val'] ?? ''));
        }, $rows), static function ($v) {
            return $v !== '';
        }));
    }

    public function advancedSearchOptions(): array
    {
        return [
            'department' => $this->distinctValues('admin_users', 'department'),
            'team_leader' => $this->distinctValues('admin_users', 'team_leader'),
            'added_by' => $this->allAdminUsers(),
            'mother_tongue' => $this->distinctValues('user_details', 'mother_tongue'),
            'marital_status' => $this->distinctValues('user_details', 'marital_status'),
            'religion' => $this->distinctValues('user_details', 'religion'),
            'maslak' => $this->distinctValues('user_details', 'maslak'),
            'caste' => $this->distinctValues('user_details', 'caste'),
            'country' => $this->distinctValues('user_details', 'country'),
            'state' => $this->distinctValues('user_details', 'state'),
            'city' => $this->distinctValues('user_details', 'city'),
            'house_type' => $this->distinctValues('user_details', 'house_type'),
            'education' => $this->distinctValues('user_details', 'education'),
            'employed_in' => $this->distinctValues('user_details', 'employed_in'),
            'occupation' => $this->distinctValues('user_details', 'occupation'),
            'designation' => $this->distinctValues('user_details', 'designation'),
            'residence' => $this->distinctValues('user_details', 'residence'),
            'eating_habits' => $this->distinctValues('user_details', 'eating_habits'),
            'smoking' => $this->distinctValues('user_details', 'smoking'),
            'drinking' => $this->distinctValues('user_details', 'drinking'),
            'body_type' => $this->distinctValues('user_details', 'body_type'),
            'skin_tone' => $this->distinctValues('user_details', 'skin_tone'),
            'blood_group' => $this->distinctValues('user_details', 'blood_group'),
            'profile_by' => $this->distinctValues('user_details', 'profile_by'),
            'reference' => $this->distinctValues('user_details', 'reference'),
            'family_type' => $this->distinctValues('user_details', 'family_type'),
            'family_status' => $this->distinctValues('user_details', 'family_status'),
            'plan_name' => $this->distinctValues('packages', 'name'),
        ];
    }

    public function advancedSearchUsers(array $filters): array
    {
        $sql = "
            SELECT
                ud.id,
                ud.first_name,
                ud.second_name AS last_name,
                ud.email,
                ud.phone,
                ud.gender,
                ud.country,
                ud.city,
                ud.created_at,
                COALESCE(ud.user_status, 'approved') AS user_status,
                COALESCE(ud.featured_status, 'non_featured') AS featured_status,
                au.name AS added_by_name
            FROM user_details ud
            LEFT JOIN admin_users au ON au.id = ud.lead
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['gender']) && strtolower($filters['gender']) !== 'all') {
            $sql .= " AND LOWER(COALESCE(ud.gender, '')) = :gender";
            $params[':gender'] = strtolower((string)$filters['gender']);
        }
        if (!empty($filters['fstatus']) && strtolower($filters['fstatus']) !== 'all') {
            $f = strtolower((string)$filters['fstatus']) === 'featured' ? 'featured' : 'non_featured';
            $sql .= " AND LOWER(COALESCE(ud.featured_status, 'non_featured')) = :fstatus";
            $params[':fstatus'] = $f;
        }
        if (!empty($filters['assignment']) && strtolower($filters['assignment']) !== 'all') {
            if (strtolower((string)$filters['assignment']) === 'assign') {
                $sql .= " AND ud.lead IS NOT NULL AND ud.lead > 0";
            } else {
                $sql .= " AND (ud.lead IS NULL OR ud.lead = 0)";
            }
        }
        if (!empty($filters['department_filter'])) {
            $sql .= " AND au.department = :department_filter";
            $params[':department_filter'] = $filters['department_filter'];
        }
        if (!empty($filters['team_leader_filter'])) {
            $sql .= " AND au.team_leader = :team_leader_filter";
            $params[':team_leader_filter'] = $filters['team_leader_filter'];
        }
        if (!empty($filters['added_by_filter'])) {
            $sql .= " AND ud.lead = :added_by_filter";
            $params[':added_by_filter'] = (int)$filters['added_by_filter'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= " AND (
                ud.first_name LIKE :kw
                OR ud.second_name LIKE :kw
                OR ud.email LIKE :kw
                OR ud.phone LIKE :kw
                OR ud.matri_id LIKE :kw
                OR ud.country LIKE :kw
                OR ud.state LIKE :kw
                OR ud.city LIKE :kw
            )";
            $params[':kw'] = '%' . trim((string)$filters['keyword']) . '%';
        }
        if (!empty($filters['from_reg_date'])) {
            $sql .= " AND DATE(ud.created_at) >= :from_reg_date";
            $params[':from_reg_date'] = $filters['from_reg_date'];
        }
        if (!empty($filters['to_reg_date'])) {
            $sql .= " AND DATE(ud.created_at) <= :to_reg_date";
            $params[':to_reg_date'] = $filters['to_reg_date'];
        }
        if (!empty($filters['from_age'])) {
            $sql .= " AND TIMESTAMPDIFF(YEAR, ud.dob, CURDATE()) >= :from_age";
            $params[':from_age'] = (int)$filters['from_age'];
        }
        if (!empty($filters['to_age'])) {
            $sql .= " AND TIMESTAMPDIFF(YEAR, ud.dob, CURDATE()) <= :to_age";
            $params[':to_age'] = (int)$filters['to_age'];
        }

        $singleMap = [
            'mother_tongue' => 'ud.mother_tongue',
            'marital_status' => 'ud.marital_status',
            'religion' => 'ud.religion',
            'maslak' => 'ud.maslak',
            'caste' => 'ud.caste',
            'country' => 'ud.country',
            'state' => 'ud.state',
            'city' => 'ud.city',
            'house_type' => 'ud.house_type',
            'education' => 'ud.education',
            'employed_in' => 'ud.employed_in',
            'occupation' => 'ud.occupation',
            'designation' => 'ud.designation',
            'residence' => 'ud.residence',
            'eating_habits' => 'ud.eating_habits',
            'smoking' => 'ud.smoking',
            'drinking' => 'ud.drinking',
            'body_type' => 'ud.body_type',
            'skin_tone' => 'ud.skin_tone',
            'blood_group' => 'ud.blood_group',
            'profile_by' => 'ud.profile_by',
            'reference' => 'ud.reference',
            'family_type' => 'ud.family_type',
            'family_status' => 'ud.family_status',
            'user_status' => 'ud.user_status',
        ];
        foreach ($singleMap as $key => $column) {
            if (!empty($filters[$key])) {
                $sql .= " AND $column = :$key";
                $params[":$key"] = $filters[$key];
            }
        }

        if (!empty($filters['plan_name'])) {
            $sql .= " AND EXISTS (
                SELECT 1
                FROM user_packages up
                INNER JOIN packages p ON p.id = up.package_id
                WHERE up.user_id = ud.id AND p.name = :plan_name
            )";
            $params[':plan_name'] = $filters['plan_name'];
        }
        if (!empty($filters['plan_status']) && strtolower($filters['plan_status']) !== 'all') {
            $status = strtolower((string)$filters['plan_status']);
            if ($status === 'paid') {
                $sql .= " AND EXISTS (SELECT 1 FROM user_packages up WHERE up.user_id = ud.id AND (up.is_paid = 1 OR LOWER(COALESCE(up.status, '')) = 'paid'))";
            } elseif ($status === 'not_paid') {
                $sql .= " AND NOT EXISTS (SELECT 1 FROM user_packages up WHERE up.user_id = ud.id AND (up.is_paid = 1 OR LOWER(COALESCE(up.status, '')) = 'paid'))";
            } elseif ($status === 'expired') {
                $sql .= " AND EXISTS (SELECT 1 FROM user_packages up WHERE up.user_id = ud.id AND ((up.expires_at IS NOT NULL AND up.expires_at < CURDATE()) OR (up.end_date IS NOT NULL AND up.end_date < CURDATE())))";
            }
        }

        $sql .= " ORDER BY ud.created_at DESC LIMIT 300";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMemberEvaluation(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT answers FROM admin_member_evaluations WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || empty($row['answers'])) {
            return [];
        }
        $decoded = json_decode((string)$row['answers'], true);
        return is_array($decoded) ? $decoded : [];
    }

    public function saveMemberEvaluation(int $userId, int $adminId, array $answers): bool
    {
        $json = json_encode($answers, JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare("
            INSERT INTO admin_member_evaluations (user_id, admin_id, answers)
            VALUES (:user_id, :admin_id, :answers)
            ON DUPLICATE KEY UPDATE
                admin_id = VALUES(admin_id),
                answers = VALUES(answers),
                updated_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':admin_id' => $adminId,
            ':answers' => $json,
        ]);
    }

    public function acceptedMatches(): array
    {
        $sql = "
            SELECT
                ma.id,
                ma.assigned_to,
                ma.assigned_member,
                ma.accepted_at,
                ma.updated_at,
                m1.first_name AS my_first_name,
                m1.second_name AS my_last_name,
                m2.first_name AS other_first_name,
                m2.second_name AS other_last_name,
                a.name AS staff_name,
                COALESCE(a.team_leader, a.department, 'admin') AS team_name
            FROM member_assignments ma
            LEFT JOIN user_details m1 ON m1.id = ma.assigned_to
            LEFT JOIN user_details m2 ON m2.id = ma.assigned_member
            LEFT JOIN admin_users a ON a.id = ma.assigned_by
            WHERE LOWER(COALESCE(ma.status, '')) = 'accepted'
            ORDER BY COALESCE(ma.accepted_at, ma.updated_at, ma.created_at) DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
