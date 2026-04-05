<?php
require_once __DIR__ . '/../models/BlogModel.php';

class AdminBlogController extends Controller
{
    private $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogModel();
              require_once __DIR__.'/../models/Admin.php';
        $this->admin = new Admin();
    }
    
    
    public function displayadminname() {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
         require __DIR__.'/../views/admin/partials/header.php';
    }
    
    
    
public function blogDetailById()
{
    $id = $_GET['id'] ?? null;
    if(!$id) {
        echo "No ID provided"; exit;
    }
    $blog = $this->blogModel->getBlogById($id);
    if(!$blog) {
        echo "Blog not found"; exit;
    }
    $this->view('/../views/blog/detail', ['blog'=>$blog]);
}



public function blogview()
    {
$blogs = $this->blogModel->getAllBlogs();

        // Load view directly without partials
        $viewFile = __DIR__ . '/../views/blog/index.php';
        if (file_exists($viewFile)) {
            // Make $blogs variable available inside the view
            include $viewFile;
        } else {
            echo "View file not found: " . $viewFile;
        }

    }
    // ===== LIST ALL BLOGS =====
    public function index()
    {
        $blogs = $this->blogModel->getAllBlogs();

        // Load view directly without partials
        $viewFile = __DIR__ . '/../views/admin/blog/index.php';
        if (file_exists($viewFile)) {
            // Make $blogs variable available inside the view
            include $viewFile;
        } else {
            echo "View file not found: " . $viewFile;
        }
    }

    // ===== ADD BLOG (Show Form + Save) =====
    public function create()
    {
        $uploadDir = app_public_uploads_subdir('blogs');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Media delete action from same page
            if (!empty($_POST['media_delete']) && !empty($_POST['file_name'])) {
                $fileName = basename((string)$_POST['file_name']);
                $target = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                if ($fileName !== '' && is_file($target)) {
                    @unlink($target);
                }
                header("Location: " . BASE_URL . "/admin/blog/create");
                exit;
            }

            $title   = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $status  = $_POST['status'] ?? 'draft';
            $image   = null;

            // ---- Image Upload Optional → public/uploads/blogs ----
            if (!empty($_FILES['image']['name'])) {
                $rel = app_save_upload($_FILES['image'], 'blogs');
                if ($rel !== null) {
                    $image = basename($rel);
                }
            }

            $this->blogModel->createBlog($title, $content, $image, $status);

            header("Location: " . BASE_URL . "/admin/blogs");
            exit;
        }

        // Build media list for preview section
        $mediaFiles = [];
        if (is_dir($uploadDir)) {
            $items = scandir($uploadDir);
            if ($items !== false) {
                foreach ($items as $it) {
                    if ($it === '.' || $it === '..') {
                        continue;
                    }
                    $ext = strtolower(pathinfo($it, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        continue;
                    }
                    $full = $uploadDir . DIRECTORY_SEPARATOR . $it;
                    if (!is_file($full)) {
                        continue;
                    }
                    $mediaFiles[] = [
                        'name' => $it,
                        'size' => filesize($full) ?: 0,
                        'mtime' => filemtime($full) ?: 0,
                        'url' => BASE_URL . '/uploads/blogs/' . rawurlencode($it),
                    ];
                }
            }
        }
        usort($mediaFiles, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
        });

        // Load create view directly without partials
        $viewFile = __DIR__ . '/../views/admin/blog/create.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View file not found: " . $viewFile;
        }
    }

    // ===== DELETE BLOG =====
    public function delete($id)
    {
        $this->blogModel->deleteBlog($id);

        header("Location: " . BASE_URL . "/admin/blogs");
        exit;
    }
}
?>
