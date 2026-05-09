<?php
// app/models/User.php

class User
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    /* =========================
       TRANSACTION SUPPORT
    ========================== */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollBack();
    }

    /* =========================
       CHECK EMAIL EXISTS
    ========================== */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ? true : false;
    }

    /* =========================
       CREATE USER (STEP 1)
    ========================== */
    public function modelone($data)
    {
        $sql = "INSERT INTO user_details 
            (first_name, second_name, gender, dob, email, password,phone,country_code, created_at)
            VALUES 
            (:first_name, :second_name, :gender, :dob, :email, :password, :phone, :country_code, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name'    => $data['first_name'],
            ':second_name'   => $data['second_name'],
            ':gender'        => $data['gender'],
            ':dob'           => $data['dob'],
            ':email'         => $data['email'],
            ':password'      => $data['password'],
            ':phone'         => $data['phone'],
            ':country_code' => $data['country_code'],
        ]);

        return $this->db->lastInsertId();
    }

    /* =========================
       CREATE PROFILE (STEP 2)
    ========================== */
    public function modeltwo($data)
    {
        $sql = "INSERT INTO user_profile_details
            (user_id, education, occupation, annual_income, marital_status, languages, religion, body_type, complexion, bio, created_at)
            VALUES
            (:user_id, :education, :occupation, :annual_income, :marital_status, :languages, :religion, :body_type, :complexion, :bio, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':education'      => $data['education'],
            ':occupation'     => $data['occupation'],
            ':annual_income'  => $data['annual_income'],
            ':marital_status' => $data['marital_status'],
            ':languages'      => $data['languages'],
            ':religion'       => $data['religion'],
            ':body_type'      => $data['body_type'],
            ':complexion'     => $data['complexion'],
            ':bio'            => $data['bio'],
        ]);

        return true;
    }

    /* =========================
       FIND METHODS
    ========================== */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmailOrMatriId($value)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_details WHERE email = :v OR matri_id = :v LIMIT 1"
        );
        $stmt->execute([':v' => $value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_details WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Member card / assignment target: user_details first, else legacy users row (normalized keys).
     */
    /** Recent members for homepage (user_details). */
    public function getRecentPublicMembers(int $limit = 8): array
    {
        $lim = max(1, min(24, $limit));
        $stmt = $this->db->prepare("
            SELECT id, first_name, second_name, gender, dob, religion, city, country, matri_id,
                   COALESCE(NULLIF(photo2_url, ''), NULLIF(photo1_status, '')) AS photo_path
            FROM user_details
            ORDER BY created_at DESC
            LIMIT {$lim}
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findMemberForDisplay($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }
        $row = $this->findById($id);
        if ($row) {
            return $row;
        }
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$u) {
            return null;
        }
        return [
            'id'          => (int)$u['id'],
            'first_name'  => $u['first_name'] ?? '',
            'second_name' => $u['last_name'] ?? '',
            'email'       => $u['email'] ?? '',
            'phone'       => $u['phone'] ?? '',
            'gender'      => $u['gender'] ?? '',
            'dob'         => $u['dob'] ?? '',
            'religion'    => $u['religion'] ?? '',
            'about_us'    => '',
            'country_code' => $u['country_code'] ?? '',
            'photo2_url'  => $u['avatar'] ?? '',
            'avatar'      => $u['avatar'] ?? '',
            'photo1_status' => null,
        ];
    }

    /* =========================
       UPDATE USER (USED IN CONTROLLER)
    ========================== */
    public function updateUser($id, $data)
    {
        $sql = "UPDATE user_details SET
                    first_name = :first_name,
                    second_name = :second_name,
                    email      = :email
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':second_name' => $data['second_name'],
            ':email'      => $data['email'],
            ':id'         => $id,
        ]);
    }

    /* =========================
       FULL PROFILE UPDATE (OPTIONAL)
    ========================== */
    public function updateProfile($id, array $data)
    {
        $fields = [
            'first_name'    => $data['first_name'],
            'second_name'   => $data['last_name'],
            'phone'         => $data['phone'],
            'country_code'  => $data['country_code'] ?? '',
            'religion'      => $data['religion'],
            'dob'           => $data['dob'],
            'about_us'      => $data['bio'] ?? '',
        ];

        if (!empty($data['avatar'])) {
            $fields['photo2_url'] = $data['avatar'];
        }

        $set = [];
        $params = [':id' => $id];
        foreach ($fields as $col => $val) {
            $set[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }

        $sql = 'UPDATE user_details SET ' . implode(', ', $set) . ' WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    /* =========================
       MEMBERS FEED
    ========================== */
    public function getAllExcept($userId)
    {
        $sql = "
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.gender,
                u.dob,
                u.religion,
                u.avatar,
                p.education,
                p.occupation,
                p.annual_income,
                p.height,
                p.mother_tongue
            FROM users u
            LEFT JOIN user_profile_details p 
                ON p.user_id = u.id
            WHERE u.id != :id
            ORDER BY u.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       SAVED PROFILES
    ========================== */
    public function getSavedProfiles($userId)
    {
        $stmt = $this->db->prepare("
            SELECT sp.saved_user_id AS id,
                   COALESCE(ud.first_name, u.first_name) AS first_name,
                   COALESCE(NULLIF(ud.second_name, ''), u.last_name) AS last_name,
                   COALESCE(ud.gender, u.gender) AS gender,
                   COALESCE(ud.dob, u.dob) AS dob,
                   COALESCE(ud.religion, u.religion) AS religion,
                   COALESCE(
                       NULLIF(ud.photo2_url, ''),
                       NULLIF(ud.photo1_status, ''),
                       u.avatar,
                       ''
                   ) AS avatar,
                   sp.created_at AS saved_at
            FROM saved_profiles sp
            LEFT JOIN user_details ud ON sp.saved_user_id = ud.id
            LEFT JOIN users u ON sp.saved_user_id = u.id AND ud.id IS NULL
            WHERE sp.user_id = :user_id
            ORDER BY sp.created_at DESC
        ");

        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProfile($userId, $savedUserId)
    {
        $check = $this->db->prepare("
            SELECT id FROM saved_profiles 
            WHERE user_id = ? AND saved_user_id = ?
        ");
        $check->execute([$userId, $savedUserId]);

        if ($check->fetch()) {
            return true;
        }

        $stmt = $this->db->prepare("
            INSERT INTO saved_profiles (user_id, saved_user_id)
            VALUES (?, ?)
        ");

        return $stmt->execute([$userId, $savedUserId]);
    }

    public function isProfileSaved(int $userId, int $savedUserId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM saved_profiles WHERE user_id = ? AND saved_user_id = ? LIMIT 1'
        );
        $stmt->execute([$userId, $savedUserId]);

        return (bool) $stmt->fetchColumn();
    }
}
