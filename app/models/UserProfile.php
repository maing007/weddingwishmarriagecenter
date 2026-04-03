<?php

class UserProfile
{
  protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }
    public function saveOrUpdate($data)
    {
        $sql = "
        INSERT INTO user_profile_details
        (user_id, education, occupation, annual_income, eating_habits, drinking, smoking,
         appearance, complexion, body_type, horoscope_details, `cast`, height, mother_tongue, created_at)
        VALUES
        (:user_id, :education, :occupation, :annual_income, :eating_habits, :drinking, :smoking,
         :appearance, :complexion, :body_type, :horoscope_details, :member_cast, :height, :mother_tongue, NOW())
        ON DUPLICATE KEY UPDATE
          education = VALUES(education),
          occupation = VALUES(occupation),
          annual_income = VALUES(annual_income),
          eating_habits = VALUES(eating_habits),
          drinking = VALUES(drinking),
          smoking = VALUES(smoking),
          appearance = VALUES(appearance),
          complexion = VALUES(complexion),
          body_type = VALUES(body_type),
          horoscope_details = VALUES(horoscope_details),
          `cast` = VALUES(`cast`),
          height = VALUES(height),
          mother_tongue = VALUES(mother_tongue),
          updated_at = NOW()
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':education' => $data['education'],
            ':occupation' => $data['occupation'],
            ':annual_income' => $data['annual_income'],
            ':eating_habits' => $data['eating_habits'] !== '' ? $data['eating_habits'] : null,
            ':drinking' => $data['drinking'] !== '' ? $data['drinking'] : null,
            ':smoking' => $data['smoking'] !== '' ? $data['smoking'] : null,
            ':appearance' => $data['appearance'],
            ':complexion' => $data['complexion'] !== '' ? $data['complexion'] : null,
            ':body_type' => $data['body_type'] !== '' ? $data['body_type'] : null,
            ':horoscope_details' => $data['horoscope_details'],
            ':member_cast' => $data['cast'],
            ':height' => $data['height'],
            ':mother_tongue' => $data['mother_tongue'],
        ]);
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_profile_details WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
