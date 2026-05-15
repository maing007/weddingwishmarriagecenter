<?php

require_once __DIR__ . '/../models/BlogModel.php';

class BlogController
{
    public function detail(string $slug): void
    {
        $slug = trim($slug);
        if ($slug === '') {
            http_response_code(404);
            echo 'Blog not found';
            return;
        }

        $model = new BlogModel();
        $blog = $model->getBlogBySlug($slug);
        if (!$blog || (($blog['status'] ?? '') !== 'published')) {
            http_response_code(404);
            echo 'Blog not found';
            return;
        }

        $title = $blog['title'] ?? 'Blog';
        require __DIR__ . '/../views/partials/header.php';
        require __DIR__ . '/../views/blog/detail.php';
        require __DIR__ . '/../views/partials/footer.php';
    }
}
