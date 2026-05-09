<?php

class AdminPaidProfileModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    // LIST
   public function all()
{
    return $this->db->query("
        SELECT up.id,
               u.first_name,
               u.last_name,
               p.name AS package_name,
               up.is_paid,
               up.started_at,
               up.expires_at,
               up.status
        FROM user_packages up
        JOIN users u ON u.id = up.user_id
        JOIN packages p ON p.id = up.package_id
        ORDER BY up.id DESC
    ")->fetchAll();
}

    /** Latest package row for a member (user_packages.user_id). */
    public function findLatestPackageForUser(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT up.*, p.name AS package_name
            FROM user_packages up
            LEFT JOIN packages p ON p.id = up.package_id
            WHERE up.user_id = ?
            ORDER BY up.id DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }


    // SINGLE
    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_packages WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE user_packages SET
                start_date = ?,
                end_date   = ?,
                is_paid    = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['start_date'],
            $data['end_date'],
            $data['is_paid'],
            $id
        ]);
    }

    // DELETE
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user_packages WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
