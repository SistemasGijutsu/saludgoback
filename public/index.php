<?php

/**
 * SaludGo API Backend
 * Punto de entrada principal
 */

// Manejo de errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Cargar autoloader
require_once __DIR__ . '/../autoload.php';

// Aplicar middleware CORS
Infrastructure\Middleware\CorsMiddleware::handle();

// Servir archivos estáticos de uploads
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/uploads/') !== false) {
    // Extraer la parte después de 'public'
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    // Si la ruta contiene 'public/uploads', remover todo antes de 'uploads'
    if (preg_match('#/uploads/.*$#', $path, $matches)) {
        $uploadPath = $matches[0];  // /uploads/profiles/...
        $filePath = __DIR__ . '/..' . $uploadPath;
        
        if (file_exists($filePath) && is_file($filePath)) {
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Archivo no encontrado: ' . $filePath]);
            exit;
        }
    }
}

// Manejar errores globales
set_exception_handler(function($exception) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => config('app.debug') ? $exception->getMessage() : 'Ocurrió un error',
        'trace' => config('app.debug') ? $exception->getTraceAsString() : null
    ]);
});

// Cargar rutas y ejecutar router
try {
    $router = require_once __DIR__ . '/../src/Infrastructure/routes.php';
    $router->run();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al procesar la solicitud',
        'message' => config('app.debug') ? $e->getMessage() : null
    ]);
}
