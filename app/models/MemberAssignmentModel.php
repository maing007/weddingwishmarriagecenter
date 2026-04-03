<?php

class MemberAssignmentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    // 🔹 Assign Member
    public function assignMember($data)
    {
        $stmt = $this->db->prepare("INSERT INTO member_assignments 
            (assigned_to, assigned_member, assigned_by, admin_comment) 
            VALUES (:assigned_to, :assigned_member, :assigned_by, :admin_comment)");

        return $stmt->execute([
            ':assigned_to'     => $data['assigned_to'],
            ':assigned_member' => $data['assigned_member'],
            ':assigned_by'     => $data['assigned_by'],
            ':admin_comment'   => $data['admin_comment']
        ]);
    }

    // 🔹 Get Assignments for User Feed (user_details-first; legacy users if no UD row)
    public function getUserAssignments($userId)
    {
        $stmt = $this->db->prepare("
            SELECT ma.*,
                   COALESCE(ud.first_name, u.first_name) AS first_name,
                   COALESCE(NULLIF(ud.second_name, ''), u.last_name) AS last_name,
                   COALESCE(ud.gender, u.gender) AS gender,
                   COALESCE(ud.religion, u.religion) AS religion,
                   COALESCE(ud.dob, u.dob) AS dob,
                   COALESCE(
                       NULLIF(ud.photo2_url, ''),
                       NULLIF(ud.photo1_status, ''),
                       u.avatar,
                       ''
                   ) AS avatar
            FROM member_assignments ma
            LEFT JOIN user_details ud ON ma.assigned_member = ud.id
            LEFT JOIN users u ON ma.assigned_member = u.id AND ud.id IS NULL
            WHERE ma.assigned_to = :user_id
            ORDER BY ma.created_at DESC
        ");

        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /** Assignment row only if it belongs to the logged-in member (assigned_to). */
    public function findOwnedBy(int $assignmentId, int $assignedToUserId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM member_assignments WHERE id = ? AND assigned_to = ? LIMIT 1"
        );
        $stmt->execute([$assignmentId, $assignedToUserId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // 🔹 Update Status
    public function updateStatus($assignmentId, $status)
    {
        $sql = "UPDATE member_assignments 
                SET status = :status";

        if ($status == 'accepted') {
            $sql .= ", accepted_at = NOW()";
        }

        if ($status == 'declined') {
            $sql .= ", declined_at = NOW()";
        }

        $sql .= ", updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $assignmentId
        ]);
    }

    // 🔹 Mark as Opened
    public function markOpened($assignmentId)
    {
        $stmt = $this->db->prepare("UPDATE member_assignments 
            SET status = IF(status = 'pending', 'opened', status),
                opened_count = opened_count + 1,
                opened_at = IFNULL(opened_at, NOW())
            WHERE id = :id");

        return $stmt->execute([':id' => $assignmentId]);
    }

    // 🔹 Add History
    public function addHistory($assignmentId, $userId, $action)
    {
        $stmt = $this->db->prepare("INSERT INTO member_assignment_history
            (assignment_id, user_id, action)
            VALUES (:assignment_id, :user_id, :action)");

        return $stmt->execute([
            ':assignment_id' => $assignmentId,
            ':user_id'       => $userId,
            ':action'        => $action
        ]);
    }
}