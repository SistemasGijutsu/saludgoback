<?php

namespace Infrastructure;

class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $path, callable $handler, bool $requiresAuth = false, array $roles = []): void
    {
        $this->addRoute('GET', $path, $handler, $requiresAuth, $roles);
    }

    public function post(string $path, callable $handler, bool $requiresAuth = false, array $roles = []): void
    {
        $this->addRoute('POST', $path, $handler, $requiresAuth, $roles);
    }

    public function put(string $path, callable $handler, bool $requiresAuth = false, array $roles = []): void
    {
        $this->addRoute('PUT', $path, $handler, $requiresAuth, $roles);
    }

    public function delete(string $path, callable $handler, bool $requiresAuth = false, array $roles = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $requiresAuth, $roles);
    }

    private function addRoute(string $method, string $path, callable $handler, bool $requiresAuth, array $roles): void
    {
        $path = $this->basePath . $path;
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'requiresAuth' => $requiresAuth,
            'roles' => $roles,
        ];
    }

    public function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remover index.php de la URI si está presente
        $requestUri = str_replace('/index.php', '', $requestUri);

        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remover el match completo

                // Verificar autenticación si es requerida
                $userData = null;
                if ($route['requiresAuth']) {
                    $authMiddleware = new \Infrastructure\Middleware\AuthMiddleware();
                    $userData = $authMiddleware->handle();
                    
                    // Verificar roles si están especificados
                    if (!empty($route['roles'])) {
                        $authMiddleware->requireRole($userData, ...$route['roles']);
                    }
                }

                // Ejecutar handler
                if ($userData) {
                    $matches[] = $userData; // Agregar userData como último parámetro
                }
                
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        // No se encontró la ruta
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }

    private function convertToRegex(string $path): string
    {
        // Convertir {id} a (\d+) para capturar números
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(\d+)', $path);
        // Escapar slashes
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }
}
