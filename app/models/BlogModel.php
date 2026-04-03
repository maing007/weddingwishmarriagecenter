<?php

class BlogModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }


    public function getAllBlogs()
    {
        $stmt = $this->db->prepare("SELECT * FROM blogs ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getBlogById($id)
{
    $sql = "SELECT * FROM blogs WHERE id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function getPublishedBlogs()
    {
        $stmt = $this->db->prepare("SELECT * FROM blogs WHERE status = 'published' ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBlogBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM blogs WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function createBlog($title, $content, $image = null, $status = 'draft')
{
    $sql = "INSERT INTO blogs (title, content, image, status) VALUES (:title, :content, :image, :status)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':title'   => $title,
        ':content' => $content,
        ':image'   => $image,
        ':status'  => $status
    ]);
}



    public function deleteBlog($id)
    {
        $stmt = $this->db->prepare("DELETE FROM blogs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
