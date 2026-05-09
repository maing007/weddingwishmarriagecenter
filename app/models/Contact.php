<?php

require_once __DIR__ . '/../../core/Database.php';

class Contact
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }
    public function save($data)
    {
        $sql = "INSERT INTO contacts
                (name, email, country_code, phone, subject, description, ip_address, created_at)
                VALUES
                (:name, :email, :country_code, :phone, :subject, :description, :ip_address, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name'         => $data['name'],
            ':email'        => $data['email'],
            ':country_code' => $data['country_code'],
            ':phone'        => $data['phone'],
            ':subject'      => $data['subject'],
            ':description'  => $data['description'],
            ':ip_address'   => $data['ip_address']
        ]);
    }
    public function contact_messages()
    {
        $sql = "
            SELECT
                u.id AS id,
                u.name,
                u.phone,
                u.email,
                u.subject,
                u.description
            FROM contacts u
            ORDER BY u.id DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteContactMessage($id)
    {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function deleteMultipleMessages($ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function getMessagesByIds($ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id IN ($placeholders)");
        $stmt->execute($ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllMessages()
    {
        $stmt = $this->db->query("SELECT * FROM contacts ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
