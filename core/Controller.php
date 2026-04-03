<?php

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View $viewFile not found");
        }

        require __DIR__ . '/../app/views/partials/header.php';
        require $viewFile;
        require __DIR__ . '/../app/views/partials/footer.php';
    }

    // ✅ Detect AJAX request
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // ✅ Detect JSON request
    protected function isJson(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) &&
               strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    }

    // ✅ Get request data (works for form + JSON)
    protected function requestData(): array
    {
        if ($this->isJson()) {
            $input = json_decode(file_get_contents('php://input'), true);
            return $input ?? [];
        }

        return $_POST;
    }

    // ✅ JSON response
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}