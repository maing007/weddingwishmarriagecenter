<?php
class AdminUserManager {
    protected $db;
    public function __construct() {
        $this->db = Database::getInstance()->pdo();
    }

    // public function allUsers() {
    //     return $this->db->query("SELECT * FROM users ORDER BY created_at DESC")
    //                     ->fetchAll(PDO::FETCH_ASSOC);
    // }
public function allUsers() {
   $sql = $this->db->prepare("
      SELECT u.*, ma.status, ma.opened_count, ma.admin_comment
FROM users u
LEFT JOIN (
    SELECT * FROM member_assignments ma1
    WHERE ma1.id = (
        SELECT MAX(ma2.id) 
        FROM member_assignments ma2 
        WHERE ma2.assigned_to = ma1.assigned_to
    )
) ma ON u.id = ma.assigned_to
ORDER BY u.created_at DESC

    "); // <- semicolon was missing
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function markPaid($userId,$packageId) {
        $stmt = $this->db->prepare("
            INSERT INTO user_subscriptions 
            (user_id,package_id,is_paid,start_date,end_date)
            VALUES (?,?,1,CURDATE(),DATE_ADD(CURDATE(),INTERVAL 30 DAY))
        ");
        return $stmt->execute([$userId,$packageId]);
    }
}
