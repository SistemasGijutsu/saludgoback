<?php

namespace Infrastructure\Middleware;

class CorsMiddleware
{
    public static function handle(): void
    {
        $allowedOrigins = config('app.cors.allowed_origins');
        $allowedMethods = config('app.cors.allowed_methods');
        $allowedHeaders = config('app.cors.allowed_headers');

        // Si se permite cualquier origen
        if (in_array('*', $allowedOrigins)) {
            header('Access-Control-Allow-Origin: *');
        } else {
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if (in_array($origin, $allowedOrigins)) {
                header('Access-Control-Allow-Origin: ' . $origin);
            }
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
        header('Access-Control-Max-Age: 86400');

        // Manejar preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
