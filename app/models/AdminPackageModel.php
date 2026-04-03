<?php

class AdminPackageModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    public function users()
    {
        return $this->db
            ->query("SELECT id, first_name, last_name FROM users ORDER BY first_name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function packages()
    {
        return $this->db
            ->query("SELECT * FROM packages ORDER BY price")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignPackage($userId, $packageId)
    {
        $pkg = $this->db
            ->prepare("SELECT duration_days FROM packages WHERE id = ?");
        $pkg->execute([$packageId]);
        $package = $pkg->fetch();

        if (!$package) return false;

        $start  = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime("+{$package['duration_days']} days"));

        $invoice = 'INV-' . time();

        $stmt = $this->db->prepare("
            INSERT INTO user_packages
            (user_id, package_id, started_at, expires_at, invoice_no)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $packageId,
            $start,
            $expiry,
            $invoice
        ]);
    }
    public function deleteUserPackage($id)
{
    $stmt = $this->db->prepare(
        "DELETE FROM user_packages WHERE id = ?"
    );
    return $stmt->execute([$id]);
}
public function updateUserPackage($id, $data)
{
    $stmt = $this->db->prepare("
        UPDATE user_packages SET
            package_name = :package,
            price = :price,
            status = :status,
            expires_at = :expires
        WHERE id = :id
    ");

    return $stmt->execute([
        ':package' => $data['package_name'],
        ':price'   => $data['price'],
        ':status'  => $data['status'],
        ':expires' => $data['expires_at'],
        ':id'      => $id
    ]);
}
public function findUserPackageById($id)
{
    $stmt = $this->db->prepare(
        "SELECT * FROM user_packages WHERE id = ?"
    );
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
