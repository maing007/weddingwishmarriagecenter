<?php

class MemberSaleFeeModel
{
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_RISHTA = 'rishta';

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS member_sale_fees (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                fee_type ENUM('registration','rishta') NOT NULL DEFAULT 'registration',
                activation_date DATE NOT NULL,
                staff_name VARCHAR(150) NOT NULL DEFAULT '',
                ti_name VARCHAR(150) NOT NULL DEFAULT '',
                matri_id VARCHAR(32) NOT NULL DEFAULT '',
                client_name VARCHAR(255) NOT NULL DEFAULT '',
                fee_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                package VARCHAR(120) NOT NULL DEFAULT '',
                payment_mode VARCHAR(120) NOT NULL DEFAULT '',
                staff_payment_status VARCHAR(40) NOT NULL DEFAULT 'Unpaid',
                staff_payment_mode VARCHAR(120) DEFAULT NULL,
                staff_paid_on DATE DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_type_date (fee_type, activation_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->seedIfEmpty();
        $this->ensureExtendedSchema();
    }

    public const STATUS_PENDING_PLAN = 'Pending Plan';

    private function ensureExtendedSchema(): void
    {
        $this->ensureColumn('user_details', 'registration_fee_queued', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->ensureColumn('member_sale_fees', 'linked_user_id', 'INT UNSIGNED NULL DEFAULT NULL');
        $this->ensureColumn('member_sale_fees', 'payment_note', 'TEXT NULL');
        $this->ensureColumn('member_sale_fees', 'bonus_days', 'INT NOT NULL DEFAULT 0');
        $this->ensureColumn('member_sale_fees', 'discount_amount', 'DECIMAL(12,2) NOT NULL DEFAULT 0');
        $this->ensureColumn('member_sale_fees', 'team_label', 'VARCHAR(200) NOT NULL DEFAULT \'\'');
        $this->ensureColumn('member_sale_fees', 'invoice_ref', 'VARCHAR(64) NOT NULL DEFAULT \'\'');

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS member_fee_payment_proofs (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                fee_id INT UNSIGNED NOT NULL,
                bank_name VARCHAR(200) NOT NULL DEFAULT '',
                account_title VARCHAR(200) NOT NULL DEFAULT '',
                transaction_id VARCHAR(120) NOT NULL DEFAULT '',
                paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                paid_date DATE NULL,
                receipt_path VARCHAR(500) NOT NULL DEFAULT '',
                proof_type VARCHAR(40) NOT NULL DEFAULT 'register',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_fee (fee_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function ensureColumn(string $table, string $column, string $definition): void
    {
        try {
            $chk = $this->db->prepare('
                SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            ');
            $chk->execute([$table, $column]);
            if ((int) $chk->fetchColumn() > 0) {
                return;
            }
            $this->db->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        } catch (Throwable $e) {
            error_log('MemberSaleFeeModel::ensureColumn ' . $table . '.' . $column . ': ' . $e->getMessage());
        }
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM member_sale_fees')->fetchColumn();
        if ($n > 0) {
            return;
        }

        $reg = [
            ['2026-03-28', 'Ali Jawad', 'Ali Jawad', 'WW21768', 'Syeda Zahra', 5000.00, 'Rhodium', 'Cash account', 'Unpaid', null, null],
            ['2026-03-27', 'Ali Jawad', 'Sana Malik', 'WW21806', 'Syeda Abeer', 7500.00, 'Platinum', 'Jazz Cash', 'Paid', 'Meezan Bank', '2026-01-25'],
            ['2026-03-26', 'Samina Kashif', 'Samina Kashif', 'WW19001', 'Zainab Malik', 5000.00, 'Rhodium', 'Meezan Bank', 'Unpaid', null, null],
            ['2026-03-25', 'Ali Jawad', 'Ali Jawad', 'WW17200', 'Fatima Noor', 10000.00, 'Gold', 'Cash account', 'Paid', '-', null],
            ['2026-03-24', 'Hira Khan', 'Ali Jawad', 'WW15507', 'Syed Farhan Ahmad Bukhari', 5000.00, 'Rhodium', 'Jazz Cash', 'Unpaid', null, null],
            ['2026-03-22', 'Tooba Ahsan', 'Tooba Ahsan', 'WW18002', 'Ayesha Khan', 6500.00, 'Silver', 'Meezan Bank', 'Paid', 'Meezan Bank', '2026-02-10'],
        ];

        $rishta = [
            ['2026-03-29', 'Ali Jawad', 'Ali Jawad', 'WW21768', 'Syeda Zahra', 3000.00, 'Rhodium', 'Cash account', 'Unpaid', null, null],
            ['2026-03-28', 'Samina Kashif', 'Samina Kashif', 'WW16001', 'Muhammad Usman', 4500.00, 'Platinum', 'Jazz Cash', 'Paid', '-', '2026-02-01'],
            ['2026-03-27', 'Ali Jawad', 'Ali Jawad', 'WW16550', 'Sara Ahmed', 3000.00, 'Gold', 'Meezan Bank', 'Unpaid', null, null],
            ['2026-03-20', 'Tooba Ahsan', 'Hira Khan', 'WW15888', 'Hassan Raza', 3500.00, 'Rhodium', 'Cash account', 'Paid', 'Meezan Bank', '2026-01-15'],
        ];

        $sql = '
            INSERT INTO member_sale_fees (
                fee_type, activation_date, staff_name, ti_name, matri_id, client_name,
                fee_amount, package, payment_mode, staff_payment_status, staff_payment_mode, staff_paid_on
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ';
        $stmt = $this->db->prepare($sql);
        foreach ($reg as $r) {
            $stmt->execute(array_merge([self::TYPE_REGISTRATION], $r));
        }
        foreach ($rishta as $r) {
            $stmt->execute(array_merge([self::TYPE_RISHTA], $r));
        }
    }

    public function allByType(string $feeType): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM member_sale_fees
            WHERE fee_type = ?
            ORDER BY activation_date DESC, id DESC
        ');
        $stmt->execute([$feeType]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Fee rows with linked member profile + interaction counts (Manage Member–style cards).
     */
    public function allByTypeForIncomeUi(string $feeType): array
    {
        $sql = "
            SELECT
                msf.*,
                ud.id AS linked_user_id,
                ud.first_name AS ud_first_name,
                ud.second_name AS ud_second_name,
                ud.gender,
                ud.phone,
                ud.mobile_number,
                ud.religion,
                ud.caste,
                ud.mother_tongue,
                ud.marital_status,
                ud.email AS email,
                ud.country,
                ud.state,
                ud.city,
                ud.dob,
                ud.created_at AS ud_created_at,
                ud.user_status,
                ud.photo1_status,
                ud.photo2_url,
                ud.photo3_url,
                ud.photo4_url,
                ud.photo5_url,
                ud.photo6_url,
                ud.registration_fee AS ud_registration_fee,
                ud.final_fee AS ud_final_fee,
                (SELECT MAX(up.expires_at) FROM user_packages up WHERE up.user_id = ud.id) AS plan_expires_at,
                (SELECT p.name FROM user_packages up
                    INNER JOIN packages p ON p.id = up.package_id
                    WHERE up.user_id = ud.id
                    ORDER BY up.expires_at DESC LIMIT 1) AS active_plan_name,
                (SELECT COALESCE(SUM(ma.opened_count), 0) FROM member_assignments ma
                    WHERE ud.id IS NOT NULL AND ma.assigned_to = ud.id) AS opened_count,
                (SELECT COUNT(*) FROM member_assignments ma
                    WHERE ud.id IS NOT NULL AND ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'pending') AS deferred_count,
                (SELECT COUNT(*) FROM member_assignments ma
                    WHERE ud.id IS NOT NULL AND ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'declined') AS declined_count,
                (SELECT COUNT(*) FROM member_assignments ma
                    WHERE ud.id IS NOT NULL AND ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'meeting') AS meeting_count,
                (SELECT COUNT(*) FROM member_assignments ma
                    WHERE ud.id IS NOT NULL AND ma.assigned_to = ud.id AND LOWER(COALESCE(ma.status, '')) = 'accepted') AS accepted_count
            FROM member_sale_fees msf
            LEFT JOIN user_details ud ON (
                (NULLIF(TRIM(ud.matri_id), '') IS NOT NULL AND TRIM(ud.matri_id) = TRIM(msf.matri_id))
                OR TRIM(msf.matri_id) = CONCAT('" . MATRI_ID_PREFIX . "', ud.id)
                OR TRIM(msf.matri_id) = CONCAT('" . MATRI_ID_PREFIX_LEGACY . "', ud.id)
            )
            WHERE msf.fee_type = ?
            ORDER BY msf.activation_date DESC, msf.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$feeType]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM member_sale_fees WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getLinkedUserIdForFee(int $feeId): ?int
    {
        $p = MATRI_ID_PREFIX;
        $leg = MATRI_ID_PREFIX_LEGACY;
        $sql = "
            SELECT ud.id AS linked_user_id
            FROM member_sale_fees msf
            LEFT JOIN user_details ud ON (
                (NULLIF(TRIM(ud.matri_id), '') IS NOT NULL AND TRIM(ud.matri_id) = TRIM(msf.matri_id))
                OR TRIM(msf.matri_id) = CONCAT('{$p}', ud.id)
                OR TRIM(msf.matri_id) = CONCAT('{$leg}', ud.id)
            )
            WHERE msf.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $feeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $uid = (int) ($row['linked_user_id'] ?? 0);

        return $uid > 0 ? $uid : null;
    }

    /**
     * Mark fee staff-side as Paid; approve linked member when matri matches a profile.
     */
    public function markFeePaidAndApproveMember(int $feeId): bool
    {
        $feeRow = $this->findById($feeId);
        if (!$feeRow) {
            return false;
        }
        $linkedId = $this->getLinkedUserIdForFee($feeId);
        $isRishta = ($feeRow['fee_type'] ?? '') === self::TYPE_RISHTA;
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('
                UPDATE member_sale_fees
                SET staff_payment_status = \'Paid\', staff_paid_on = CURDATE()
                WHERE id = :id
            ');
            $stmt->execute([':id' => $feeId]);
            if ($linkedId !== null) {
                if ($isRishta) {
                    $u = $this->db->prepare('UPDATE user_details SET featured_status = \'approved\' WHERE id = :id');
                } else {
                    $u = $this->db->prepare('UPDATE user_details SET user_status = \'approved\' WHERE id = :id');
                }
                $u->execute([':id' => $linkedId]);
            }
            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();

            return false;
        }
    }

    /**
     * Keep member_sale_fees (Income → Registration) aligned with user_details so new members appear on the registration fee list.
     * Members Sales Report already reads user_details; marking Paid here updates user_status and moves rows to "received" tabs.
     */
    public function syncRegistrationSaleRowFromUserDetails(int $userDetailsId): bool
    {
        if ($userDetailsId <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM user_details WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $userDetailsId]);
            $ud = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ud) {
                return false;
            }

            $feeAmt = (float) ($ud['registration_fee'] ?? 0);
            if ($feeAmt <= 0) {
                return true;
            }

            $mid = trim((string) ($ud['matri_id'] ?? ''));
            if ($mid === '') {
                $mid = MATRI_ID_PREFIX . (int) $ud['id'];
                $fix = $this->db->prepare("UPDATE user_details SET matri_id = :m WHERE id = :id AND (matri_id IS NULL OR TRIM(matri_id) = '')");
                $fix->execute([':m' => $mid, ':id' => $userDetailsId]);
            }

            $legacyMatriKey = MATRI_ID_PREFIX_LEGACY . (int) $ud['id'];
            $prefixedMatriKey = MATRI_ID_PREFIX . (int) $ud['id'];
            $clientName = trim(($ud['first_name'] ?? '') . ' ' . ($ud['second_name'] ?? ''));
            if ($clientName === '') {
                $clientName = 'Member';
            }
            $actTs = strtotime((string) ($ud['created_at'] ?? '')) ?: time();
            $actDate = date('Y-m-d', $actTs);
            $tiName = trim((string) ($ud['lead'] ?? ''));

            $find = $this->db->prepare("
                SELECT id FROM member_sale_fees
                WHERE fee_type = 'registration'
                  AND (
                      TRIM(matri_id) = TRIM(:m1)
                      OR TRIM(matri_id) = :m2
                      OR TRIM(matri_id) = :m3
                  )
                ORDER BY id DESC
                LIMIT 1
            ");
            $find->execute([':m1' => $mid, ':m2' => $legacyMatriKey, ':m3' => $prefixedMatriKey]);
            $existingId = (int) ($find->fetchColumn() ?: 0);

            if ($existingId > 0) {
                $up = $this->db->prepare('
                    UPDATE member_sale_fees
                    SET matri_id = :matri,
                        client_name = :client,
                        fee_amount = :fee,
                        activation_date = :ad,
                        ti_name = CASE WHEN :ti <> \'\' THEN :ti ELSE ti_name END
                    WHERE id = :id
                ');
                $up->execute([
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':id' => $existingId,
                ]);
            } else {
                $ins = $this->db->prepare('
                    INSERT INTO member_sale_fees (
                        fee_type, activation_date, staff_name, ti_name, matri_id, client_name,
                        fee_amount, package, payment_mode, staff_payment_status
                    ) VALUES (
                        \'registration\', :ad, \'\', :ti, :matri, :client,
                        :fee, \'\', \'\', \'Unpaid\'
                    )
                ');
                $ins->execute([
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                ]);
            }

            return true;
        } catch (Throwable $e) {
            error_log('MemberSaleFeeModel::syncRegistrationSaleRowFromUserDetails: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Same for Rishta / final fee (Income → Rishta Fee list).
     */
    public function syncRishtaSaleRowFromUserDetails(int $userDetailsId): bool
    {
        if ($userDetailsId <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM user_details WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $userDetailsId]);
            $ud = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ud) {
                return false;
            }

            $feeAmt = (float) ($ud['final_fee'] ?? 0);
            if ($feeAmt <= 0) {
                return true;
            }

            $mid = trim((string) ($ud['matri_id'] ?? ''));
            if ($mid === '') {
                $mid = MATRI_ID_PREFIX . (int) $ud['id'];
                $fix = $this->db->prepare("UPDATE user_details SET matri_id = :m WHERE id = :id AND (matri_id IS NULL OR TRIM(matri_id) = '')");
                $fix->execute([':m' => $mid, ':id' => $userDetailsId]);
            }

            $legacyMatriKey = MATRI_ID_PREFIX_LEGACY . (int) $ud['id'];
            $prefixedMatriKey = MATRI_ID_PREFIX . (int) $ud['id'];
            $clientName = trim(($ud['first_name'] ?? '') . ' ' . ($ud['second_name'] ?? ''));
            if ($clientName === '') {
                $clientName = 'Member';
            }
            $actTs = strtotime((string) ($ud['created_at'] ?? '')) ?: time();
            $actDate = date('Y-m-d', $actTs);
            $tiName = trim((string) ($ud['lead'] ?? ''));

            $find = $this->db->prepare("
                SELECT id FROM member_sale_fees
                WHERE fee_type = 'rishta'
                  AND (
                      TRIM(matri_id) = TRIM(:m1)
                      OR TRIM(matri_id) = :m2
                      OR TRIM(matri_id) = :m3
                  )
                ORDER BY id DESC
                LIMIT 1
            ");
            $find->execute([':m1' => $mid, ':m2' => $legacyMatriKey, ':m3' => $prefixedMatriKey]);
            $existingId = (int) ($find->fetchColumn() ?: 0);

            if ($existingId > 0) {
                $up = $this->db->prepare('
                    UPDATE member_sale_fees
                    SET matri_id = :matri,
                        client_name = :client,
                        fee_amount = :fee,
                        activation_date = :ad,
                        ti_name = CASE WHEN :ti <> \'\' THEN :ti ELSE ti_name END
                    WHERE id = :id
                ');
                $up->execute([
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':id' => $existingId,
                ]);
            } else {
                $ins = $this->db->prepare('
                    INSERT INTO member_sale_fees (
                        fee_type, activation_date, staff_name, ti_name, matri_id, client_name,
                        fee_amount, package, payment_mode, staff_payment_status
                    ) VALUES (
                        \'rishta\', :ad, \'\', :ti, :matri, :client,
                        :fee, \'\', \'\', \'Unpaid\'
                    )
                ');
                $ins->execute([
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                ]);
            }

            return true;
        } catch (Throwable $e) {
            error_log('MemberSaleFeeModel::syncRishtaSaleRowFromUserDetails: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function salesReportWhere(string $search, string $scope, string $payFilter, string $staffEq = ''): array
    {
        $parts = ['1=1'];
        $params = [];

        if ($scope === self::TYPE_REGISTRATION) {
            $parts[] = "fee_type = 'registration'";
        } elseif ($scope === self::TYPE_RISHTA) {
            $parts[] = "fee_type = 'rishta'";
        }

        if ($payFilter === 'paid') {
            $parts[] = "LOWER(TRIM(COALESCE(staff_payment_status, ''))) = 'paid'";
        } elseif ($payFilter === 'unpaid') {
            $parts[] = "LOWER(TRIM(COALESCE(staff_payment_status, ''))) <> 'paid'";
        }

        $staffEq = trim($staffEq);
        if ($staffEq !== '') {
            $parts[] = 'TRIM(staff_name) = :stf';
            $params[':stf'] = $staffEq;
        }

        if (trim($search) !== '') {
            $parts[] = '(matri_id LIKE :s OR client_name LIKE :s OR staff_name LIKE :s OR ti_name LIKE :s OR package LIKE :s OR payment_mode LIKE :s OR COALESCE(staff_payment_mode, \'\') LIKE :s)';
            $params[':s'] = '%' . $search . '%';
        }

        return [implode(' AND ', $parts), $params];
    }

    /** Row counts per scope for tabs (search only; ignores pay filter). */
    public function salesReportScopeCounts(string $search): array
    {
        return [
            'all' => $this->salesReportTotal($search, 'all', 'all'),
            'registration' => $this->salesReportTotal($search, self::TYPE_REGISTRATION, 'all'),
            'rishta' => $this->salesReportTotal($search, self::TYPE_RISHTA, 'all'),
        ];
    }

    public function salesReportTotal(string $search, string $scope, string $payFilter, string $staffEq = ''): int
    {
        [$w, $p] = $this->salesReportWhere($search, $scope, $payFilter, $staffEq);
        $sql = 'SELECT COUNT(*) FROM member_sale_fees WHERE ' . $w;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($p);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function salesReportRows(string $search, string $scope, string $payFilter, string $staffEq, int $limit, int $offset): array
    {
        [$w, $p] = $this->salesReportWhere($search, $scope, $payFilter, $staffEq);
        $sql = '
            SELECT
                id, fee_type, activation_date, staff_name, ti_name, matri_id, client_name,
                fee_amount, package, payment_mode, staff_payment_status, staff_payment_mode,
                staff_paid_on, created_at
            FROM member_sale_fees
            WHERE ' . $w . '
            ORDER BY activation_date DESC, id DESC
            LIMIT :lim OFFSET :off
        ';
        $stmt = $this->db->prepare($sql);
        foreach ($p as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Distinct staff names for filter dropdown. */
    public function salesReportDistinctStaffNames(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT TRIM(staff_name) AS n
            FROM member_sale_fees
            WHERE staff_name IS NOT NULL AND TRIM(staff_name) <> ''
            ORDER BY n ASC
        ");
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = (string) $row['n'];
        }

        return $out;
    }

    /**
     * After "Approve" on All Members: queue member for Registration Fee + Pending Plan row.
     */
    public function upsertPendingPlanRegistrationRow(int $userDetailsId): bool
    {
        if ($userDetailsId <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM user_details WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $userDetailsId]);
            $ud = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ud) {
                return false;
            }

            $mid = trim((string) ($ud['matri_id'] ?? ''));
            if ($mid === '') {
                $mid = MATRI_ID_PREFIX . (int) $ud['id'];
                $this->db->prepare("UPDATE user_details SET matri_id = :m WHERE id = :id AND (matri_id IS NULL OR TRIM(matri_id) = '')")
                    ->execute([':m' => $mid, ':id' => $userDetailsId]);
            }

            $legacyMatriKey = MATRI_ID_PREFIX_LEGACY . (int) $ud['id'];
            $prefixedMatriKey = MATRI_ID_PREFIX . (int) $ud['id'];
            $clientName = trim(($ud['first_name'] ?? '') . ' ' . ($ud['second_name'] ?? ''));
            if ($clientName === '') {
                $clientName = 'Member';
            }
            $actTs = strtotime((string) ($ud['created_at'] ?? '')) ?: time();
            $actDate = date('Y-m-d', $actTs);
            $tiName = trim((string) ($ud['lead'] ?? ''));
            $feeAmt = (float) ($ud['registration_fee'] ?? 0);

            $find = $this->db->prepare("
                SELECT id FROM member_sale_fees
                WHERE fee_type = 'registration'
                  AND (
                      TRIM(matri_id) = TRIM(:m1)
                      OR TRIM(matri_id) = :m2
                      OR TRIM(matri_id) = :m3
                  )
                ORDER BY id DESC
                LIMIT 1
            ");
            $find->execute([':m1' => $mid, ':m2' => $legacyMatriKey, ':m3' => $prefixedMatriKey]);
            $existingId = (int) ($find->fetchColumn() ?: 0);

            if ($existingId > 0) {
                $up = $this->db->prepare('
                    UPDATE member_sale_fees
                    SET matri_id = :matri,
                        client_name = :client,
                        fee_amount = :fee,
                        activation_date = :ad,
                        ti_name = CASE WHEN :ti <> \'\' THEN :ti ELSE ti_name END,
                        linked_user_id = :uid,
                        package = \'\',
                        staff_payment_status = :pst,
                        staff_name = \'\'
                    WHERE id = :id
                ');
                $up->execute([
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':uid' => $userDetailsId,
                    ':pst' => self::STATUS_PENDING_PLAN,
                    ':id' => $existingId,
                ]);
            } else {
                $ins = $this->db->prepare('
                    INSERT INTO member_sale_fees (
                        fee_type, activation_date, staff_name, ti_name, matri_id, client_name,
                        fee_amount, package, payment_mode, staff_payment_status, linked_user_id
                    ) VALUES (
                        \'registration\', :ad, \'\', :ti, :matri, :client,
                        :fee, \'\', \'\', :pst, :uid
                    )
                ');
                $ins->execute([
                    ':ad' => $actDate,
                    ':ti' => $tiName,
                    ':matri' => $mid,
                    ':client' => $clientName,
                    ':fee' => $feeAmt,
                    ':pst' => self::STATUS_PENDING_PLAN,
                    ':uid' => $userDetailsId,
                ]);
            }

            return true;
        } catch (Throwable $e) {
            error_log('MemberSaleFeeModel::upsertPendingPlanRegistrationRow: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Plan assignment modal submit: create package row, approve member, update fee row for sales report.
     *
     * @param array<string, mixed> $in
     * @return array{ok: bool, message: string}
     */
    public function assignRegistrationPlanAndApprove(array $in): array
    {
        $feeId = (int) ($in['fee_id'] ?? 0);
        $userId = (int) ($in['user_id'] ?? 0);
        $packageId = (int) ($in['package_id'] ?? 0);
        $staffId = (int) ($in['staff_id'] ?? 0);
        $teamLabel = trim((string) ($in['team_label'] ?? ''));
        $rishtaFee = (float) ($in['rishta_fee'] ?? 0);
        $bonusDays = max(0, (int) ($in['bonus_days'] ?? 0));
        $discount = max(0, (float) ($in['discount'] ?? 0));
        $paymentNote = trim((string) ($in['payment_note'] ?? ''));

        if ($feeId <= 0 || $userId <= 0 || $packageId <= 0 || $staffId <= 0 || $teamLabel === '') {
            return ['ok' => false, 'message' => 'Missing required fields.'];
        }

        $feeRow = $this->findById($feeId);
        if (!$feeRow || ($feeRow['fee_type'] ?? '') !== self::TYPE_REGISTRATION) {
            return ['ok' => false, 'message' => 'Invalid registration fee record.'];
        }

        $pst = trim((string) ($feeRow['staff_payment_status'] ?? ''));
        if (strcasecmp($pst, self::STATUS_PENDING_PLAN) !== 0) {
            return ['ok' => false, 'message' => 'This member is not awaiting plan assignment.'];
        }

        $pkgStmt = $this->db->prepare('SELECT * FROM packages WHERE id = :id LIMIT 1');
        $pkgStmt->execute([':id' => $packageId]);
        $pkg = $pkgStmt->fetch(PDO::FETCH_ASSOC);
        if (!$pkg) {
            return ['ok' => false, 'message' => 'Plan not found.'];
        }

        $admStmt = $this->db->prepare('SELECT name FROM admin_users WHERE id = :id LIMIT 1');
        $admStmt->execute([':id' => $staffId]);
        $staffName = trim((string) ($admStmt->fetchColumn() ?: ''));
        if ($staffName === '') {
            return ['ok' => false, 'message' => 'Staff not found.'];
        }

        $planPrice = (float) ($pkg['price'] ?? 0);
        if ($discount > $planPrice) {
            $discount = $planPrice;
        }
        $grandTotal = max(0, $planPrice - $discount);
        $duration = (int) ($pkg['duration_days'] ?? 0) + $bonusDays;
        if ($duration < 1) {
            $duration = 1;
        }

        $start = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime($start . ' +' . $duration . ' days'));
        $pkgName = trim((string) ($pkg['name'] ?? '')) ?: 'Plan';
        $invoiceRef = 'INV-01-' . str_pad((string) $feeId, 6, '0', STR_PAD_LEFT);

        $autoNote = 'Registration Fee Invoice Created For ' . trim((string) ($feeRow['client_name'] ?? 'Member'))
            . ' - ' . trim((string) ($feeRow['matri_id'] ?? ''))
            . ' By Staff: ' . $staffName . ' Team: ' . $teamLabel . '.';
        $desc = $paymentNote !== '' ? ($paymentNote . "\n\n" . $autoNote) : $autoNote;

        $this->db->beginTransaction();
        try {
            $insUp = $this->db->prepare('
                INSERT INTO user_packages (user_id, package_id, status, started_at, expires_at, invoice_no, is_paid)
                VALUES (:uid, :pid, \'active\', :st, :ex, :inv, 0)
            ');
            $insUp->execute([
                ':uid' => $userId,
                ':pid' => $packageId,
                ':st' => $start,
                ':ex' => $expiry,
                ':inv' => $invoiceRef,
            ]);

            $this->db->prepare('
                UPDATE user_details SET
                    user_status = \'approved\',
                    registration_fee_queued = 0,
                    final_fee = :rf
                WHERE id = :id
            ')->execute([':rf' => $rishtaFee, ':id' => $userId]);

            $this->db->prepare('
                UPDATE member_sale_fees SET
                    package = :pkg,
                    fee_amount = :amt,
                    staff_name = :sn,
                    ti_name = :team,
                    staff_payment_status = \'Unpaid\',
                    payment_mode = \'N/A\',
                    payment_note = :pn,
                    bonus_days = :bd,
                    discount_amount = :disc,
                    team_label = :tl,
                    invoice_ref = :inv,
                    linked_user_id = :uid,
                    activation_date = :ad
                WHERE id = :fid
            ')->execute([
                ':pkg' => $pkgName,
                ':amt' => $grandTotal,
                ':sn' => $staffName,
                ':team' => $teamLabel,
                ':pn' => $desc,
                ':bd' => $bonusDays,
                ':disc' => $discount,
                ':tl' => $teamLabel,
                ':inv' => $invoiceRef,
                ':uid' => $userId,
                ':ad' => $start,
                ':fid' => $feeId,
            ]);

            $this->db->commit();

            return ['ok' => true, 'message' => 'Plan assigned. Member approved.'];
        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log('assignRegistrationPlanAndApprove: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Could not save assignment.'];
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function salesReportRowsForCards(string $search, string $scope, string $payFilter, string $staffEq, int $limit, int $offset): array
    {
        [$w, $p] = $this->salesReportWhere($search, $scope, $payFilter, $staffEq);
        $pfx = MATRI_ID_PREFIX;
        $leg = MATRI_ID_PREFIX_LEGACY;
        $sql = "
            SELECT
                msf.*,
                ud.id AS card_user_id,
                ud.email AS card_email,
                (SELECT up.started_at FROM user_packages up
                    WHERE up.user_id = ud.id ORDER BY up.id DESC LIMIT 1) AS card_started_at,
                (SELECT up.expires_at FROM user_packages up
                    WHERE up.user_id = ud.id ORDER BY up.id DESC LIMIT 1) AS card_expires_at,
                (SELECT p.duration_days FROM user_packages up
                    INNER JOIN packages p ON p.id = up.package_id
                    WHERE up.user_id = ud.id ORDER BY up.id DESC LIMIT 1) AS card_plan_duration_days,
                COALESCE(ud.final_fee, 0) AS card_final_fee
            FROM member_sale_fees msf
            LEFT JOIN user_details ud ON (
                (msf.linked_user_id IS NOT NULL AND msf.linked_user_id > 0 AND ud.id = msf.linked_user_id)
                OR (
                    (msf.linked_user_id IS NULL OR msf.linked_user_id = 0)
                    AND (
                        (NULLIF(TRIM(ud.matri_id), '') IS NOT NULL AND TRIM(ud.matri_id) = TRIM(msf.matri_id))
                        OR TRIM(msf.matri_id) = CONCAT('{$pfx}', ud.id)
                        OR TRIM(msf.matri_id) = CONCAT('{$leg}', ud.id)
                    )
                )
            )
            WHERE {$w}
            ORDER BY msf.activation_date DESC, msf.id DESC
            LIMIT :lim OFFSET :off
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($p as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function savePaymentProof(int $feeId, array $post, ?array $file): array
    {
        if ($feeId <= 0) {
            return ['ok' => false, 'message' => 'Invalid fee.'];
        }
        $row = $this->findById($feeId);
        if (!$row) {
            return ['ok' => false, 'message' => 'Fee not found.'];
        }

        $bank = trim((string) ($post['bank_name'] ?? ''));
        $acct = trim((string) ($post['account_title'] ?? ''));
        $tx = trim((string) ($post['transaction_id'] ?? ''));
        $amt = trim((string) ($post['paid_amount'] ?? ''));
        $dt = trim((string) ($post['date'] ?? ''));
        if ($bank === '' || $acct === '' || $tx === '' || $amt === '' || $dt === '') {
            return ['ok' => false, 'message' => 'All fields except file are required.'];
        }

        $receiptPath = '';
        if (!empty($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf', 'webp'], true)) {
                return ['ok' => false, 'message' => 'Invalid file type.'];
            }
            $saved = app_save_upload($file, 'payment_proofs');
            if ($saved === null) {
                return ['ok' => false, 'message' => 'Could not upload file.'];
            }
            $receiptPath = $saved;
        }

        try {
            $stmt = $this->db->prepare('
                INSERT INTO member_fee_payment_proofs (
                    fee_id, bank_name, account_title, transaction_id, paid_amount, paid_date, receipt_path, proof_type
                ) VALUES (
                    :fid, :bn, :at, :tx, :amt, :pd, :rp, :typ
                )
            ');
            $stmt->execute([
                ':fid' => $feeId,
                ':bn' => $bank,
                ':at' => $acct,
                ':tx' => $tx,
                ':amt' => (float) $amt,
                ':pd' => $dt,
                ':rp' => $receiptPath,
                ':typ' => trim((string) ($post['type'] ?? 'register')) ?: 'register',
            ]);

            $this->db->prepare('
                UPDATE member_sale_fees SET payment_mode = :pm, staff_payment_mode = :pm2 WHERE id = :id
            ')->execute([
                ':pm' => 'Bank transfer',
                ':pm2' => $bank,
                ':id' => $feeId,
            ]);

            return ['ok' => true, 'message' => 'Payment proof saved.'];
        } catch (Throwable $e) {
            error_log('savePaymentProof: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Could not save proof.'];
        }
    }

    public function listPaymentProofs(int $feeId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM member_fee_payment_proofs WHERE fee_id = :id ORDER BY id DESC');
        $stmt->execute([':id' => $feeId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param list<int> $feeIds
     *
     * @return array<int, list<array<string, mixed>>>
     */
    public function listPaymentProofsForFeeIds(array $feeIds): array
    {
        $feeIds = array_values(array_filter(array_map('intval', $feeIds), static fn (int $id) => $id > 0));
        if ($feeIds === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($feeIds), '?'));
        $stmt = $this->db->prepare("
            SELECT * FROM member_fee_payment_proofs
            WHERE fee_id IN ({$placeholders})
            ORDER BY fee_id ASC, id DESC
        ");
        $stmt->execute($feeIds);
        $by = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $fid = (int) ($row['fee_id'] ?? 0);
            if ($fid <= 0) {
                continue;
            }
            $by[$fid][] = $row;
        }

        return $by;
    }

    /** @return list<array<string, mixed>> */
    public function allPackages(): array
    {
        try {
            return $this->db->query('SELECT * FROM packages ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /** @return list<array<string, mixed>> */
    public function allAdminStaffForPlan(): array
    {
        try {
            return $this->db->query('
                SELECT id, name, COALESCE(team_leader, \'\') AS team_leader, COALESCE(department, \'\') AS department
                FROM admin_users
                ORDER BY name ASC
            ')->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function findFeeWithUserContext(int $feeId): ?array
    {
        $row = $this->findById($feeId);
        if (!$row) {
            return null;
        }
        $uid = (int) ($row['linked_user_id'] ?? 0);
        if ($uid <= 0) {
            $uid = (int) ($this->getLinkedUserIdForFee($feeId) ?: 0);
        }
        if ($uid > 0) {
            $st = $this->db->prepare('SELECT * FROM user_details WHERE id = :id LIMIT 1');
            $st->execute([':id' => $uid]);
            $ud = $st->fetch(PDO::FETCH_ASSOC);
            if ($ud) {
                $row['_user'] = $ud;
            }
        }

        return $row;
    }
}
