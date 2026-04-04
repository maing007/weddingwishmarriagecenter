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
    }

    private function seedIfEmpty(): void
    {
        $n = (int) $this->db->query('SELECT COUNT(*) FROM member_sale_fees')->fetchColumn();
        if ($n > 0) {
            return;
        }

        $reg = [
            ['2026-03-28', 'Ali Jawad', 'Ali Jawad', 'NG21768', 'Syeda Zahra', 5000.00, 'Rhodium', 'Cash account', 'Unpaid', null, null],
            ['2026-03-27', 'Ali Jawad', 'Sana Malik', 'NG21806', 'Syeda Abeer', 7500.00, 'Platinum', 'Jazz Cash', 'Paid', 'Meezan Bank', '2026-01-25'],
            ['2026-03-26', 'Samina Kashif', 'Samina Kashif', 'NG19001', 'Zainab Malik', 5000.00, 'Rhodium', 'Meezan Bank', 'Unpaid', null, null],
            ['2026-03-25', 'Ali Jawad', 'Ali Jawad', 'NG17200', 'Fatima Noor', 10000.00, 'Gold', 'Cash account', 'Paid', '-', null],
            ['2026-03-24', 'Hira Khan', 'Ali Jawad', 'NG15507', 'Syed Farhan Ahmad Bukhari', 5000.00, 'Rhodium', 'Jazz Cash', 'Unpaid', null, null],
            ['2026-03-22', 'Tooba Ahsan', 'Tooba Ahsan', 'NG18002', 'Ayesha Khan', 6500.00, 'Silver', 'Meezan Bank', 'Paid', 'Meezan Bank', '2026-02-10'],
        ];

        $rishta = [
            ['2026-03-29', 'Ali Jawad', 'Ali Jawad', 'NG21768', 'Syeda Zahra', 3000.00, 'Rhodium', 'Cash account', 'Unpaid', null, null],
            ['2026-03-28', 'Samina Kashif', 'Samina Kashif', 'NG16001', 'Muhammad Usman', 4500.00, 'Platinum', 'Jazz Cash', 'Paid', '-', '2026-02-01'],
            ['2026-03-27', 'Ali Jawad', 'Ali Jawad', 'NG16550', 'Sara Ahmed', 3000.00, 'Gold', 'Meezan Bank', 'Unpaid', null, null],
            ['2026-03-20', 'Tooba Ahsan', 'Hira Khan', 'NG15888', 'Hassan Raza', 3500.00, 'Rhodium', 'Cash account', 'Paid', 'Meezan Bank', '2026-01-15'],
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
                ud.email,
                ud.country,
                ud.state,
                ud.city,
                ud.dob,
                ud.created_at AS ud_created_at,
                ud.user_status,
                ud.photo2_url,
                ud.photo1_status,
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
                OR TRIM(msf.matri_id) = CONCAT('NG', ud.id)
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
        $sql = '
            SELECT ud.id AS linked_user_id
            FROM member_sale_fees msf
            LEFT JOIN user_details ud ON (
                (NULLIF(TRIM(ud.matri_id), \'\') IS NOT NULL AND TRIM(ud.matri_id) = TRIM(msf.matri_id))
                OR TRIM(msf.matri_id) = CONCAT(\'NG\', ud.id)
            )
            WHERE msf.id = :id
            LIMIT 1
        ';
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
                $mid = 'NG' . (int) $ud['id'];
                $fix = $this->db->prepare("UPDATE user_details SET matri_id = :m WHERE id = :id AND (matri_id IS NULL OR TRIM(matri_id) = '')");
                $fix->execute([':m' => $mid, ':id' => $userDetailsId]);
            }

            $ngKey = 'NG' . (int) $ud['id'];
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
                  )
                ORDER BY id DESC
                LIMIT 1
            ");
            $find->execute([':m1' => $mid, ':m2' => $ngKey]);
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
                $mid = 'NG' . (int) $ud['id'];
                $fix = $this->db->prepare("UPDATE user_details SET matri_id = :m WHERE id = :id AND (matri_id IS NULL OR TRIM(matri_id) = '')");
                $fix->execute([':m' => $mid, ':id' => $userDetailsId]);
            }

            $ngKey = 'NG' . (int) $ud['id'];
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
                  )
                ORDER BY id DESC
                LIMIT 1
            ");
            $find->execute([':m1' => $mid, ':m2' => $ngKey]);
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
}
