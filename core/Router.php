<?php
// core/Router.php

class Router
{
    private $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    private $notFound;

    public function get(string $path, $handler)
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, $handler)
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, $handler)
    {
        // Normalise path: "/register-user/", "register-user" -> "/register-user"
        $path = '/' . trim($path, '/');
        $this->routes[$method][$path] = $handler;
    }

    public function setNotFound($handler)
    {
        $this->notFound = $handler;
    }

    public function dispatch(string $method, string $uri)
    {
        // Remove query string: /register-user?x=1 => /register-user
        $path = parse_url($uri, PHP_URL_PATH);

        // If project sub-folder me hai to uska base path remove karo
        // jaise /wedding/public/index.php => base /wedding/public
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($scriptDir !== '' && $scriptDir !== '/') {
            if (strpos($path, $scriptDir) === 0) {
                $path = substr($path, strlen($scriptDir));
            }
        }

        // Normalise final path
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        // Route match try karo
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            return $this->callHandler($handler);
        }

        // Agar exact match na mile toh trailing slash variant bhi try karlo
        $alt = rtrim($path, '/');
        if ($alt === '') {
            $alt = '/';
        }
        if (isset($this->routes[$method][$alt])) {
            $handler = $this->routes[$method][$alt];
            return $this->callHandler($handler);
        }

        // Match parameterized/regex routes (e.g. /profile/{id}, /user/(\d+))
        foreach ($this->routes[$method] as $routePath => $handler) {
            $params = $this->extractParams($routePath, $path);
            if ($params !== null) {
                return $this->callHandler($handler, $params);
            }
        }

        // 404 handler
        if ($this->notFound) {
            return $this->callHandler($this->notFound);
        }

        http_response_code(404);
        echo '404 - Page not found';
    }

    
private function callHandler($handler, array $params = [])
{
    // If closure callback
    if (is_callable($handler)) {
        return call_user_func_array($handler, $params);
    }

    // If controller@method format
    if (is_string($handler) && strpos($handler, '@') !== false) {

        list($controllerName, $methodName) = explode('@', $handler);

        // Absolute path safe way
        $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file {$controllerFile} not found");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            throw new Exception("Controller {$controllerName} not found");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            throw new Exception("Method {$methodName} not found in {$controllerName}");
        }

        return call_user_func_array([$controller, $methodName], $params);
    }

    throw new Exception("Invalid route handler");
}

private function extractParams(string $routePath, string $requestPath): ?array
{
    // Support {param} style placeholders
    if (strpos($routePath, '{') !== false && strpos($routePath, '}') !== false) {
        $trimmed = trim($routePath, '/');
        if ($trimmed === '') {
            $pattern = '#^/$#';
        } else {
            $segments = explode('/', $trimmed);
            $parts = [];

            foreach ($segments as $segment) {
                if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $m) === 1) {
                    $parts[] = '(?P<' . $m[1] . '>[^/]+)';
                } else {
                    $parts[] = preg_quote($segment, '#');
                }
            }

            $pattern = '#^/' . implode('/', $parts) . '$#';
        }

        if (preg_match($pattern, $requestPath, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[] = $value;
                }
            }
            return $params;
        }
        return null;
    }

    // Support legacy regex-like route definitions (e.g. /dashboard/user/(\d+))
    if (preg_match('/[\(\)\[\]\+\*\?]/', $routePath) === 1) {
        $pattern = '#^' . $routePath . '$#';
        if (preg_match($pattern, $requestPath, $matches)) {
            array_shift($matches);
            return $matches;
        }
    }

    return null;
}

}
