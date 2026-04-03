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
}
