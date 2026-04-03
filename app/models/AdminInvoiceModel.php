<?php
// app/models/AdminInvoiceModel.php

class AdminInvoiceModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    public function all()
    {
        $sql = "
            SELECT
                up.id AS package_id,
                up.invoice_no,
                up.start_date,
                up.end_date,
                up.created_at,

                u.id AS id,
                u.first_name,
                u.last_name,
                u.email,

                p.name AS package_name,
                p.price AS amount,
                p.duration_days
            FROM user_packages up
            JOIN users u ON u.id = up.user_id
            JOIN packages p ON p.id = up.package_id
            ORDER BY up.created_at DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                up.*,
                u.first_name,
                u.last_name,
                u.email,
                p.name AS package_name,
                p.price AS amount,
                p.duration_days
            FROM user_packages up
            JOIN users u ON u.id = up.user_id
            JOIN packages p ON p.id = up.package_id
            WHERE up.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
