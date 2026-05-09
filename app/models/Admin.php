<?php
class Admin
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }


    public function getById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT id, email FROM admin_users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function get_admin_details()
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM admin_users"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            "UPDATE admin_users SET password_hash = ? WHERE id = ?"
        );

        return $stmt->execute([$hashedPassword, $id]);
    }





    public function markAttendance($admin_id)
    {
        // check if already marked today
        $stmt = $this->db->prepare("
        SELECT id FROM admin_attendance 
        WHERE admin_id = ? AND created_at = CURDATE()
    ");
        $stmt->execute([$admin_id]);

        $result = $stmt->fetch();

        if (!$result) {
            // insert attendance
            $stmt = $this->db->prepare("
            INSERT INTO admin_attendance (admin_id) VALUES (?)
        ");
            $stmt->execute([$admin_id]);

            // update counter
            $stmt = $this->db->prepare("
            UPDATE admin_users SET attendance = attendance + 1 WHERE id = ?
        ");
            $stmt->execute([$admin_id]);
        }
    }
    public function getWeeklyAttendance($admin_id)
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) as total 
        FROM admin_attendance 
        WHERE admin_id = ? 
        AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
    ");

        $stmt->execute([$admin_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] ?? 0;
    }
    public function create($name, $email, $password)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO admin_users (name,email,password_hash) VALUES (?,?,?)"
        );
        return $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE admin_users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteAdmin($id)
    {
        $stmt = $this->db->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
    //  public function get_admin() {
    //         $stmt = $this->db->prepare("SELECT * FROM admin_users");
    //         $stmt->execute();
    //         return $stmt->fetchAll();   
    //     }
    /**
     * Get admin users with optional search, status filter, limit, and sort
     */
    public function get_admin($search = '', $limit = 10, $sort = 'created_at-desc', $status = '')
    {
        $query = "SELECT * FROM admin_users WHERE 1";

        // Search
        if (!empty($search)) {
            $query .= " AND (name LIKE :search OR email LIKE :search)";
        }

        // Status filter
        if (!empty($status)) {
            $query .= " AND status = :status";
        }

        // Allowed sorting
        $allowedSort = [
            'status-desc' => 'status DESC',
            'status-asc' => 'status ASC',
            'name-desc'   => 'name DESC',
            'name-asc'    => 'name ASC',
            'created_at-desc' => 'created_at DESC',
            'created_at-asc'  => 'created_at ASC'
        ];

        $orderBy = $allowedSort[$sort] ?? 'created_at DESC';
        $query .= " ORDER BY $orderBy LIMIT :limit";

        $stmt = $this->db->prepare($query);

        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }

        if (!empty($status)) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
