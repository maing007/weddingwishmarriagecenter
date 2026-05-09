<?php

class UserProfileModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function searchProfiles($filters = [])
    {
        $sql = "SELECT * FROM user_profiles WHERE status = 'active'";
        $params = [];

        if (!empty($filters['gender'])) {
            $sql .= " AND gender = :gender";
            $params[':gender'] = $filters['gender'];
        }

        if (!empty($filters['city'])) {
            $sql .= " AND city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['age_from']) && !empty($filters['age_to'])) {
            $sql .= " AND age BETWEEN :age_from AND :age_to";
            $params[':age_from'] = (int)$filters['age_from'];
            $params[':age_to']   = (int)$filters['age_to'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProfileById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_profiles WHERE id = :id AND status = 'active' LIMIT 1"
        );
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
