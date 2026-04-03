<?php

class UserDetails
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    public function create($data)
    {

        $columns = implode(",", array_keys($data));
        $values  = ":" . implode(",:", array_keys($data));

        $sql = "INSERT INTO user_details ($columns) VALUES ($values)";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {

            // Convert arrays to JSON
            if (is_array($value)) {
                $value = json_encode($value);
            }

            // Handle empty values
            if ($value === '') {
                $value = null;
            }

            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }
}
