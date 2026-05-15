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
    }

    /**
     * Same COALESCE as allUsers() card thumbnail (photo1 … photo6).
     *
     * @param string $q Table name or alias (e.g. "user_details", "ud")
     */
    private static function sqlSelectAvatarFromUserDetails(string $q): string
    {
        return "COALESCE(
                NULLIF(TRIM(COALESCE({$q}.photo1_status, '')), ''),
                NULLIF(TRIM(COALESCE({$q}.photo2_url, '')), ''),
                NULLIF(TRIM(COALESCE({$q}.photo3_url, '')), ''),
                NULLIF(TRIM(COALESCE({$q}.photo4_url, '')), ''),
                NULLIF(TRIM(COALESCE({$q}.photo5_url, '')), ''),
                NULLIF(TRIM(COALESCE({$q}.photo6_url, '')), '')
            ) AS avatar";
    }

    public function allUsers(?string $dashboardFilter = null)
    {
        $pfx = MATRI_ID_PREFIX;
        $leg = MATRI_ID_PREFIX_LEGACY;
        $sql = "
            SELECT
                ud.id,
                COALESCE(
                    NULLIF(TRIM(COALESCE(ud.photo1_status, '')), ''),
                    NULLIF(TRIM(COALESCE(ud.photo2_url, '')), ''),
                    NULLIF(TRIM(COALESCE(ud.photo3_url, '')), ''),
                    NULLIF(TRIM(COALESCE(ud.photo4_url, '')), ''),
                    NULLIF(TRIM(COALESCE(ud.photo5_url, '')), ''),
                    NULLIF(TRIM(COALESCE(ud.photo6_url, '')), '')
                ) AS avatar,
                ud.phone,
                ud.mobile_number,
                ud.dob,
                ud.religion,
                ud.gender,
                ud.marital_status,
                COALESCE(ud.user_status, 'approved') AS status,
                NULL AS admin_comment,
                ud.first_name,
                ud.second_name AS last_name,
                ud.email,
                ud.city,
                ud.country,
                ud.state,
                ud.caste,
                ud.mother_tongue,
                ud.final_fee,
                ud.cv_file,
                ud.matri_id,
                COALESCE(ud.featured_status, 'non_featured') AS featured_status,
                COALESCE(NULLIF(TRIM(au.name), ''), NULLIF(TRIM(ud.lead), '')) AS added_by_name,
                (
                    SELECT p.name
                    FROM user_packages up
                    INNER JOIN packages p ON p.id = up.package_id
                    WHERE up.user_id = ud.id
                    ORDER BY up.expires_at DESC
                    LIMIT 1
                ) AS active_plan_name,
                (
                    SELECT MAX(up.expires_at)
                    FROM user_packages up
                    WHERE up.user_id = ud.id
                ) AS plan_expires_at,
                (
                    SELECT MAX(mev.last_login)
                    FROM members_email_verification mev
                    WHERE NULLIF(TRIM(ud.email), '') IS NOT NULL
                      AND LOWER(TRIM(mev.email)) = LOWER(TRIM(ud.email))
                ) AS last_login,
                (
                    SELECT up.id
                    FROM user_packages up
                    WHERE up.user_id = ud.id
                    ORDER BY up.id DESC
                    LIMIT 1
                ) AS latest_package_id,
                (
                    SELECT COALESCE(SUM(ma.opened_count), 0)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = ud.id
                ) AS opened_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'pending'
                ) AS deferred_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'declined'
                ) AS declined_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'meeting'
                ) AS meeting_count,
                (
                    SELECT COUNT(*)
                    FROM member_assignments ma
                    WHERE ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'accepted'
                ) AS accepted_count,
                ud.created_at,
                COALESCE(ud.registration_fee_queued, 0) AS registration_fee_queued,
                (
                    SELECT MAX(
                        CASE
                            WHEN LOWER(TRIM(COALESCE(msf.staff_payment_status, ''))) = 'paid' THEN 1
                            WHEN EXISTS (
                                SELECT 1 FROM member_fee_payment_proofs p WHERE p.fee_id = msf.id
                            ) THEN 1
                            ELSE 0
                        END
                    )
                    FROM member_sale_fees msf
                    WHERE msf.fee_type = 'registration'
                      AND (
                          (msf.linked_user_id IS NOT NULL AND msf.linked_user_id > 0 AND msf.linked_user_id = ud.id)
                          OR (
                              (msf.linked_user_id IS NULL OR msf.linked_user_id = 0)
                              AND (
                                  (NULLIF(TRIM(ud.matri_id), '') IS NOT NULL AND TRIM(msf.matri_id) = TRIM(ud.matri_id))
                                  OR TRIM(msf.matri_id) = CONCAT('{$pfx}', ud.id)
                                  OR TRIM(msf.matri_id) = CONCAT('{$leg}', ud.id)
                              )
                          )
                      )
                ) AS registration_fee_paid
            FROM user_details ud
            LEFT JOIN admin_users au
                ON ud.lead REGEXP '^[0-9]+$' AND au.id = CAST(ud.lead AS UNSIGNED)
        ";

        $conditions = [];
        $params = [];

        switch ($dashboardFilter) {
            case 'today':
                $conditions[] = "DATE(ud.created_at) = CURDATE()";
                break;
            case 'last_week':
                $conditions[] = "ud.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'last_month':
                $conditions[] = "ud.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'male':
                $conditions[] = "LOWER(COALESCE(ud.gender, '')) = :gender_male";
                $params[':gender_male'] = 'male';
                break;
            case 'female':
                $conditions[] = "LOWER(COALESCE(ud.gender, '')) = :gender_female";
                $params[':gender_female'] = 'female';
                break;
            case 'active':
                // users table has no status column in current schema; treat all as active
                break;
            case 'paid':
                $conditions[] = "EXISTS (SELECT 1 FROM user_packages up WHERE up.user_id = ud.id AND (up.is_paid = 1 OR LOWER(COALESCE(up.status, '')) = 'paid'))";
                break;
            case 'total':
            default:
                break;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY ud.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Same status fields as allUsers() for live JSON polling (approved / paid / etc.).
     *
     * @param list<int> $ids
     *
     * @return list<array<string, mixed>>
     */
    public function profileStatusRowsForIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids), static fn (int $x) => $x > 0));
        if ($ids === []) {
            return [];
        }
        $regFeePaidSql = $this->sqlRegistrationFeePaidScalar('ud');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "
            SELECT
                ud.id,
                COALESCE(ud.user_status, 'approved') AS status,
                COALESCE(ud.registration_fee_queued, 0) AS registration_fee_queued,
                {$regFeePaidSql} AS registration_fee_paid
            FROM user_details ud
            WHERE ud.id IN ({$placeholders})
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Subquery: 1 if registration fee paid for this user_details row (same rules as allUsers). */
    private function sqlRegistrationFeePaidScalar(string $udAlias = 'ud'): string
    {
        $pfx = MATRI_ID_PREFIX;
        $leg = MATRI_ID_PREFIX_LEGACY;

        return "(
                    SELECT MAX(
                        CASE
                            WHEN LOWER(TRIM(COALESCE(msf.staff_payment_status, ''))) = 'paid' THEN 1
                            WHEN EXISTS (
                                SELECT 1 FROM member_fee_payment_proofs p WHERE p.fee_id = msf.id
                            ) THEN 1
                            ELSE 0
                        END
                    )
                    FROM member_sale_fees msf
                    WHERE msf.fee_type = 'registration'
                      AND (
                          (msf.linked_user_id IS NOT NULL AND msf.linked_user_id > 0 AND msf.linked_user_id = {$udAlias}.id)
                          OR (
                              (msf.linked_user_id IS NULL OR msf.linked_user_id = 0)
                              AND (
                                  (NULLIF(TRIM({$udAlias}.matri_id), '') IS NOT NULL AND TRIM(msf.matri_id) = TRIM({$udAlias}.matri_id))
                                  OR TRIM(msf.matri_id) = CONCAT('{$pfx}', {$udAlias}.id)
                                  OR TRIM(msf.matri_id) = CONCAT('{$leg}', {$udAlias}.id)
                              )
                          )
                      )
                )";
    }

    public function spotlightUsers(?string $featuredFilter = null): array
    {
        $avatarSel = self::sqlSelectAvatarFromUserDetails('user_details');
        $regFeePaidSql = $this->sqlRegistrationFeePaidScalar('user_details');
        $sql = "
            SELECT
                id,
                {$avatarSel},
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
                COALESCE(user_details.registration_fee_queued, 0) AS registration_fee_queued,
                {$regFeePaidSql} AS registration_fee_paid,
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
        $avatarSel = self::sqlSelectAvatarFromUserDetails('user_details');
        $regFeePaidSql = $this->sqlRegistrationFeePaidScalar('user_details');
        $sql = "
            SELECT
                id,
                {$avatarSel},
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
                COALESCE(user_details.registration_fee_queued, 0) AS registration_fee_queued,
                {$regFeePaidSql} AS registration_fee_paid,
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
        $avatarSel = self::sqlSelectAvatarFromUserDetails('ud');
        $regFeePaidSql = $this->sqlRegistrationFeePaidScalar('ud');
        $sql = "
            SELECT
                ud.id,
                {$avatarSel},
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
                COALESCE(ud.registration_fee_queued, 0) AS registration_fee_queued,
                {$regFeePaidSql} AS registration_fee_paid,
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


    /**
     * Remove member row and dependent data (fees, proofs, assignments, packages, legacy users row, etc.).
     */
    public function deleteUserCompletely(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }
        $st = $this->db->prepare('SELECT id, matri_id FROM user_details WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $matri = trim((string) ($row['matri_id'] ?? ''));

        try {
            $this->db->beginTransaction();

            $this->deleteFeesAndProofsForUserId($id, $matri);

            $this->db->prepare('DELETE FROM admin_profile_comments WHERE user_id = ?')->execute([$id]);

            $this->tryExec('DELETE FROM member_feed_interactions WHERE viewer_user_id = ? OR target_user_id = ?', [$id, $id]);

            $aidStmt = $this->db->prepare('SELECT id FROM member_assignments WHERE assigned_to = ? OR assigned_member = ?');
            $aidStmt->execute([$id, $id]);
            $assignmentIds = $aidStmt->fetchAll(PDO::FETCH_COLUMN);
            if (is_array($assignmentIds) && $assignmentIds !== []) {
                $placeholders = implode(',', array_fill(0, count($assignmentIds), '?'));
                $this->tryExec("DELETE FROM member_assignment_history WHERE assignment_id IN ({$placeholders})", $assignmentIds);
                $this->tryExec("DELETE FROM member_assignment_views WHERE assignment_id IN ({$placeholders})", $assignmentIds);
            }
            $this->db->prepare('DELETE FROM member_assignments WHERE assigned_to = ? OR assigned_member = ?')->execute([$id, $id]);

            $this->tryExec('DELETE FROM saved_profiles WHERE user_id = ? OR saved_user_id = ?', [$id, $id]);
            $this->tryExec('DELETE FROM auto_generated_matches WHERE male_user_id = ? OR female_user_id = ?', [$id, $id]);

            if ($matri !== '') {
                $this->tryExec('DELETE FROM deferred_matches WHERE my_matri_id = ? OR other_matri_id = ?', [$matri, $matri]);
            }

            $this->tryExec('DELETE FROM invoices WHERE user_id = ?', [$id]);
            $this->tryExec('DELETE FROM user_packages WHERE user_id = ?', [$id]);
            $this->tryExec('DELETE FROM user_profile_details WHERE user_id = ?', [$id]);

            $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM user_details WHERE id = ?')->execute([$id]);

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('AdminUserModel::deleteUserCompletely: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param list<mixed> $params
     */
    private function tryExec(string $sql, array $params): void
    {
        try {
            $st = $this->db->prepare($sql);
            $st->execute($params);
        } catch (Throwable $e) {
            error_log('AdminUserModel::tryExec: ' . $e->getMessage());
        }
    }

    private function deleteFeesAndProofsForUserId(int $id, string $matri): void
    {
        $this->db->prepare('
            DELETE p FROM member_fee_payment_proofs p
            INNER JOIN member_sale_fees f ON f.id = p.fee_id
            WHERE f.linked_user_id = ?
        ')->execute([$id]);
        $this->db->prepare('DELETE FROM member_sale_fees WHERE linked_user_id = ?')->execute([$id]);

        if ($matri !== '') {
            $this->db->prepare('
                DELETE p FROM member_fee_payment_proofs p
                INNER JOIN member_sale_fees f ON f.id = p.fee_id
                WHERE (f.linked_user_id IS NULL OR f.linked_user_id = 0) AND TRIM(f.matri_id) = ?
            ')->execute([$matri]);
            $this->db->prepare('
                DELETE FROM member_sale_fees
                WHERE (linked_user_id IS NULL OR linked_user_id = 0) AND TRIM(matri_id) = ?
            ')->execute([$matri]);
        }
    }

    public function deleteUser($id)
    {
        return $this->deleteUserCompletely((int) $id);
    }

    public function bulkUpdateUserStatus(array $ids, string $status): bool
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $clearQ = '';
        if (in_array($status, ['unapproved', 'suspended'], true)) {
            $clearQ = ', registration_fee_queued = 0';
        }
        $sql = "UPDATE user_details SET user_status = ?{$clearQ} WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);

        $params = array_merge([$status], array_map('intval', $ids));
        return $stmt->execute($params);
    }

    /**
     * "Approve" on All Members: send to Registration Fee list (Pending Plan), stay unapproved for site.
     */
    public function queueSelectedForRegistrationFee(array $ids): bool
    {
        $ids = array_values(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0));
        if ($ids === []) {
            return false;
        }

        require_once __DIR__ . '/MemberSaleFeeModel.php';
        $feeModel = new MemberSaleFeeModel();

        foreach ($ids as $id) {
            $stmt = $this->db->prepare("UPDATE user_details SET user_status = 'unapproved', registration_fee_queued = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $feeModel->upsertPendingPlanRegistrationRow($id);
        }

        return true;
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

    public function getMemberInteractionCounts(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                first_name,
                second_name AS last_name,
                email,
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
                ) AS accepted_count
            FROM user_details
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
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

    /**
     * Plan, added-by, last login — same semantics as allUsers() card fields (for profile view summary).
     */
    public function getUserListSupplement(int $userId): array
    {
        $sql = "
            SELECT
                (
                    SELECT p.name
                    FROM user_packages up
                    INNER JOIN packages p ON p.id = up.package_id
                    WHERE up.user_id = ud.id
                    ORDER BY up.expires_at DESC
                    LIMIT 1
                ) AS active_plan_name,
                (
                    SELECT MAX(up.expires_at)
                    FROM user_packages up
                    WHERE up.user_id = ud.id
                ) AS plan_expires_at,
                COALESCE(NULLIF(TRIM(au.name), ''), NULLIF(TRIM(ud.lead), '')) AS added_by_name,
                (
                    SELECT MAX(mev.last_login)
                    FROM members_email_verification mev
                    WHERE NULLIF(TRIM(ud.email), '') IS NOT NULL
                      AND LOWER(TRIM(mev.email)) = LOWER(TRIM(ud.email))
                ) AS last_login
            FROM user_details ud
            LEFT JOIN admin_users au
                ON ud.lead REGEXP '^[0-9]+$' AND au.id = CAST(ud.lead AS UNSIGNED)
            WHERE ud.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
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
            $val = $input[$col];
            if (is_array($val)) {
                $val = json_encode(array_values($val));
            } elseif ($val === '' || $val === null) {
                $val = null;
            }
            $setParts[] = "`$col` = :$col";
            $params[":$col"] = $val;
        }

        if (empty($setParts)) {
            return false;
        }

        $sql = "UPDATE user_details SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute($params);
        if ($ok) {
            require_once __DIR__ . '/MemberSaleFeeModel.php';
            $feeModel = new MemberSaleFeeModel();
            $feeModel->syncRegistrationSaleRowFromUserDetails($id);
            $feeModel->syncRishtaSaleRowFromUserDetails($id);
        }

        return $ok;
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

    private function dbColumnExists(string $table, string $column): bool
    {
        static $cache = [];
        $k = $table . "\0" . $column;
        if (isset($cache[$k])) {
            return $cache[$k];
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c'
        );
        $stmt->execute([':t' => $table, ':c' => $column]);
        $cache[$k] = (int) $stmt->fetchColumn() > 0;

        return $cache[$k];
    }

    private function distinctPlanExpiryDates(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT DISTINCT DATE(up.expires_at) AS d
                FROM user_packages up
                INNER JOIN user_details ud ON ud.id = up.user_id
                WHERE up.expires_at IS NOT NULL
                  AND up.expires_at > '1970-01-01'
                ORDER BY d DESC
                LIMIT 500
            ");
            $out = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $d = (string) ($r['d'] ?? '');
                if ($d !== '' && strpos($d, '0000-00-00') === false) {
                    $out[] = $d;
                }
            }

            return array_values(array_unique($out));
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function advancedSearchOptions(): array
    {
        return [
            'department' => $this->distinctValues('admin_users', 'department'),
            'team_leader' => $this->distinctValues('admin_users', 'team_leader'),
            'team_role' => $this->distinctValues('admin_users', 'role'),
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
            'annual_income' => $this->distinctValues('user_details', 'annual_income'),
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
            'no_of_brothers' => $this->distinctValues('user_details', 'no_of_brothers'),
            'no_of_married_brother' => $this->distinctValues('user_details', 'no_of_married_brother'),
            'no_of_sisters' => $this->distinctValues('user_details', 'no_of_sisters'),
            'no_of_married_sister' => $this->distinctValues('user_details', 'no_of_married_sister'),
            'plan_name' => $this->distinctValues('packages', 'name'),
            'plan_expire_dates' => $this->distinctPlanExpiryDates(),
        ];
    }

    public function advancedSearchUsers(array $filters): array
    {
        $regFeePaidSql = $this->sqlRegistrationFeePaidScalar('ud');
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
                COALESCE(ud.user_status, 'approved') AS status,
                COALESCE(ud.registration_fee_queued, 0) AS registration_fee_queued,
                {$regFeePaidSql} AS registration_fee_paid,
                COALESCE(ud.featured_status, 'non_featured') AS featured_status,
                au.name AS added_by_name
            FROM user_details ud
            LEFT JOIN admin_users au ON (
                (ud.lead REGEXP '^[0-9]+$' AND au.id = CAST(ud.lead AS UNSIGNED))
                OR (ud.lead NOT REGEXP '^[0-9]+$' AND au.name = ud.lead)
            )
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['_own_lead_only'])) {
            $sql .= ' AND (
                (ud.lead REGEXP \'^[0-9]+$\' AND CAST(ud.lead AS UNSIGNED) = :own_aid)
                OR ud.lead = :own_aname
            )';
            $params[':own_aid'] = (int) $filters['_own_lead_only'];
            $params[':own_aname'] = (string) ($filters['_own_lead_name'] ?? '');
        }

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
            $abf = (int) $filters['added_by_filter'];
            $sql .= ' AND (CAST(ud.lead AS UNSIGNED) = :added_by_filter OR ud.lead = :added_by_filter_str)';
            $params[':added_by_filter'] = $abf;
            $params[':added_by_filter_str'] = (string) $abf;
        }
        if (!empty($filters['team_filter'])) {
            $sql .= ' AND au.role = :team_filter';
            $params[':team_filter'] = (string) $filters['team_filter'];
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

        $maslakVal = trim((string) ($filters['maslak'] ?? ''));
        $sectVal = trim((string) ($filters['sect'] ?? ''));
        if ($maslakVal !== '') {
            $sql .= ' AND ud.maslak = :adv_maslak';
            $params[':adv_maslak'] = $maslakVal;
        } elseif ($sectVal !== '') {
            $sql .= ' AND ud.maslak = :adv_sect';
            $params[':adv_sect'] = $sectVal;
        }

        if (!empty($filters['height_in']) && is_array($filters['height_in'])) {
            $hin = array_values(array_filter($filters['height_in'], static function ($v) {
                return $v !== null && $v !== '';
            }));
            if ($hin !== []) {
                $ph = [];
                foreach ($hin as $ix => $h) {
                    $k = ':hi' . $ix;
                    $ph[] = $k;
                    $params[$k] = (string) $h;
                }
                $sql .= ' AND ud.height IN (' . implode(',', $ph) . ')';
            }
        }
        if (!empty($filters['weight_in']) && is_array($filters['weight_in'])) {
            $win = array_values(array_filter($filters['weight_in'], static function ($v) {
                return $v !== null && $v !== '';
            }));
            if ($win !== []) {
                $ph = [];
                foreach ($win as $ix => $w) {
                    $k = ':wi' . $ix;
                    $ph[] = $k;
                    $params[$k] = (string) $w;
                }
                $sql .= ' AND ud.weight IN (' . implode(',', $ph) . ')';
            }
        }

        $hsmFrom = isset($filters['house_marla_from']) && $filters['house_marla_from'] !== ''
            ? (float) str_replace(',', '.', preg_replace('/[^0-9.,-]/', '', (string) $filters['house_marla_from'])) : null;
        $hsmTo = isset($filters['house_marla_to']) && $filters['house_marla_to'] !== ''
            ? (float) str_replace(',', '.', preg_replace('/[^0-9.,-]/', '', (string) $filters['house_marla_to'])) : null;
        if ($hsmFrom !== null && $hsmFrom > 0) {
            $sql .= ' AND CAST(NULLIF(TRIM(ud.house_size_marla), \'\') AS DECIMAL(12,2)) >= :hsm_from';
            $params[':hsm_from'] = $hsmFrom;
        }
        if ($hsmTo !== null && $hsmTo > 0) {
            $sql .= ' AND CAST(NULLIF(TRIM(ud.house_size_marla), \'\') AS DECIMAL(12,2)) <= :hsm_to';
            $params[':hsm_to'] = $hsmTo;
        }

        $singleMap = [
            'mother_tongue' => 'ud.mother_tongue',
            'marital_status' => 'ud.marital_status',
            'religion' => 'ud.religion',
            'caste' => 'ud.caste',
            'country' => 'ud.country',
            'state' => 'ud.state',
            'city' => 'ud.city',
            'area' => 'ud.area',
            'house_type' => 'ud.house_type',
            'education' => 'ud.education',
            'employed_in' => 'ud.employed_in',
            'annual_income' => 'ud.annual_income',
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
            'no_of_brothers' => 'ud.no_of_brothers',
            'no_of_married_brother' => 'ud.no_of_married_brother',
            'no_of_sisters' => 'ud.no_of_sisters',
            'no_of_married_sister' => 'ud.no_of_married_sister',
            'user_status' => 'ud.user_status',
        ];
        foreach ($singleMap as $key => $column) {
            if (!array_key_exists($key, $filters)) {
                continue;
            }
            $fv = $filters[$key];
            if ($fv === '' || $fv === null) {
                continue;
            }
            $sql .= " AND $column = :$key";
            $params[":$key"] = $fv;
        }

        $ped = trim((string) ($filters['plan_expires_on'] ?? ''));
        if ($ped !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $ped)) {
            $sql .= ' AND EXISTS (
                SELECT 1 FROM user_packages up_pe
                WHERE up_pe.user_id = ud.id AND DATE(up_pe.expires_at) = :plan_expires_on
            )';
            $params[':plan_expires_on'] = $ped;
        }

        $ps = strtolower(trim((string) ($filters['photo_setting'] ?? 'all')));
        if ($ps === 'withphoto') {
            $sql .= " AND (
                NULLIF(TRIM(COALESCE(ud.photo2_url,'')), '') IS NOT NULL
                OR NULLIF(TRIM(COALESCE(ud.photo3_url,'')), '') IS NOT NULL
                OR NULLIF(TRIM(COALESCE(ud.photo4_url,'')), '') IS NOT NULL
                OR NULLIF(TRIM(COALESCE(ud.photo5_url,'')), '') IS NOT NULL
                OR NULLIF(TRIM(COALESCE(ud.photo6_url,'')), '') IS NOT NULL
                OR NULLIF(TRIM(COALESCE(ud.photo1_status,'')), '') IS NOT NULL
            )";
        } elseif ($ps === 'withoutphoto') {
            $sql .= " AND NULLIF(TRIM(COALESCE(ud.photo2_url,'')), '') IS NULL
                AND NULLIF(TRIM(COALESCE(ud.photo3_url,'')), '') IS NULL
                AND NULLIF(TRIM(COALESCE(ud.photo4_url,'')), '') IS NULL
                AND NULLIF(TRIM(COALESCE(ud.photo5_url,'')), '') IS NULL
                AND NULLIF(TRIM(COALESCE(ud.photo6_url,'')), '') IS NULL
                AND NULLIF(TRIM(COALESCE(ud.photo1_status,'')), '') IS NULL";
        }

        $mv = strtolower(trim((string) ($filters['mobile_verify_status'] ?? 'all')));
        if ($mv === 'verified' || $mv === 'notverified') {
            if ($this->dbColumnExists('user_details', 'mobile_verified')) {
                if ($mv === 'verified') {
                    $sql .= ' AND ud.mobile_verified = 1';
                } else {
                    $sql .= ' AND (ud.mobile_verified = 0 OR ud.mobile_verified IS NULL)';
                }
            } elseif ($mv === 'verified') {
                $sql .= " AND (
                    NULLIF(TRIM(COALESCE(ud.mobile_number,'')), '') IS NOT NULL
                    OR NULLIF(TRIM(COALESCE(ud.phone,'')), '') IS NOT NULL
                )";
            } else {
                $sql .= " AND NULLIF(TRIM(COALESCE(ud.mobile_number,'')), '') IS NULL
                    AND NULLIF(TRIM(COALESCE(ud.phone,'')), '') IS NULL";
            }
        }

        $ev = strtolower(trim((string) ($filters['email_verify_status'] ?? 'all')));
        if ($ev === 'verified' || $ev === 'notverified') {
            if ($this->dbColumnExists('user_details', 'email_verified')) {
                if ($ev === 'verified') {
                    $sql .= ' AND ud.email_verified = 1';
                } else {
                    $sql .= ' AND (ud.email_verified = 0 OR ud.email_verified IS NULL)';
                }
            } elseif ($ev === 'verified') {
                $sql .= " AND NULLIF(TRIM(COALESCE(ud.email,'')), '') IS NOT NULL
                    AND ud.email LIKE '%@%'";
            } else {
                $sql .= " AND (
                    NULLIF(TRIM(COALESCE(ud.email,'')), '') IS NULL
                    OR ud.email NOT LIKE '%@%'
                )";
            }
        }

        $rs = strtolower(trim((string) ($filters['registration_source'] ?? '')));
        if ($rs !== '' && $this->dbColumnExists('user_details', 'registration_source')) {
            if ($rs === 'website') {
                $sql .= " AND LOWER(TRIM(ud.registration_source)) IN ('website','web','site')";
            } elseif ($rs === 'mobile_app') {
                $sql .= " AND LOWER(TRIM(ud.registration_source)) IN ('mobile_app','mobile','app','android','ios')";
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

    /**
     * Staff tied to a member: assigned lead (user_details.lead), their department/team_leader peers,
     * admins assigned on open tasks, and admins who created member_assignments for this member.
     */
    public function getMemberDynamicAssignTeam(int $userId): array
    {
        $ud = $this->getUserDetailsById($userId);
        if (!$ud) {
            return ['team_name' => '', 'primary_admin_id' => 0, 'primary_lead_name' => '', 'rows' => []];
        }

        $leadRaw = $ud['lead'] ?? null;
        $primaryId = 0;
        if ($leadRaw !== null && $leadRaw !== '') {
            $leadStr = trim((string) $leadRaw);
            if ($leadStr !== '' && ctype_digit($leadStr)) {
                $primaryId = (int) $leadStr;
            } elseif ($leadStr !== '') {
                $stmt = $this->db->prepare('SELECT id FROM admin_users WHERE name = ? OR email = ? LIMIT 1');
                $stmt->execute([$leadStr, $leadStr]);
                $r = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($r) {
                    $primaryId = (int) $r['id'];
                }
            }
        }

        $byId = [];
        $addAdminRow = function (array $au) use (&$byId, $primaryId): void {
            $id = (int) ($au['id'] ?? 0);
            if ($id <= 0) {
                return;
            }
            $designation = trim((string) ($au['role'] ?? ''));
            if ($designation === '') {
                $designation = 'Staff';
            }
            $byId[$id] = [
                'admin_id' => $id,
                'department' => (string) ($au['department'] ?? ''),
                'designation' => $designation,
                'name' => (string) ($au['name'] ?? ''),
                'contact' => trim((string) ($au['email'] ?? '')),
                'official' => strtolower((string) ($au['status'] ?? '')) === 'approved',
                'is_primary' => $primaryId > 0 && $id === $primaryId,
            ];
        };

        $primary = null;
        if ($primaryId > 0) {
            $stmt = $this->db->prepare('SELECT * FROM admin_users WHERE id = ? LIMIT 1');
            $stmt->execute([$primaryId]);
            $primary = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        if ($primary) {
            $dept = trim((string) ($primary['department'] ?? ''));
            $tl = trim((string) ($primary['team_leader'] ?? ''));
            $sql = "SELECT * FROM admin_users au
                WHERE LOWER(COALESCE(au.status, '')) = 'approved'
                AND (
                    au.id = :pid
                    OR (:dept <> '' AND au.department = :dept2)
                    OR (:tl <> '' AND au.team_leader = :tl2)
                )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':pid' => $primaryId,
                ':dept' => $dept,
                ':dept2' => $dept,
                ':tl' => $tl,
                ':tl2' => $tl,
            ]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $addAdminRow($row);
            }
        }

        $stmt = $this->db->prepare(
            'SELECT DISTINCT au.* FROM admin_tasks t
            INNER JOIN admin_users au ON au.id = t.assigned_admin_id
            WHERE t.user_id = :uid AND t.assigned_admin_id IS NOT NULL'
        );
        $stmt->execute([':uid' => $userId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $addAdminRow($row);
        }

        $stmt = $this->db->prepare(
            'SELECT DISTINCT au.* FROM member_assignments ma
            INNER JOIN admin_users au ON au.id = ma.assigned_by
            WHERE ma.assigned_to = :uid AND ma.assigned_by IS NOT NULL'
        );
        $stmt->execute([':uid' => $userId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $addAdminRow($row);
        }

        $teamName = '';
        if ($primary) {
            $teamName = trim((string) ($primary['team_leader'] ?? ''));
            if ($teamName === '') {
                $teamName = trim((string) ($primary['name'] ?? ''));
            }
            if ($teamName === '') {
                $teamName = trim((string) ($primary['department'] ?? ''));
            }
        }

        $rows = array_values($byId);
        usort($rows, static function (array $a, array $b): int {
            if (($a['is_primary'] ?? false) !== ($b['is_primary'] ?? false)) {
                return ($a['is_primary'] ?? false) ? -1 : 1;
            }
            return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
        });

        if ($teamName === '' && $rows !== []) {
            $deps = array_values(array_unique(array_filter(array_map(static function ($r) {
                return trim((string) ($r['department'] ?? ''));
            }, $rows))));
            $teamName = count($deps) === 1 ? $deps[0] : 'Assigned staff';
        }

        return [
            'team_name' => $teamName,
            'primary_admin_id' => $primaryId,
            'primary_lead_name' => $primary ? (string) ($primary['name'] ?? '') : '',
            'rows' => $rows,
        ];
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
