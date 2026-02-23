<?php

namespace Infrastructure\Middleware;

use Infrastructure\Auth\JWT;

class AuthMiddleware
{
    private JWT $jwt;

    public function __construct()
    {
        $this->jwt = new JWT();
    }

    public function handle(): ?array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['error' => 'Token no proporcionado']);
            exit;
        }

        // Formato: "Bearer <token>"
        $parts = explode(' ', $authHeader);
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            http_response_code(401);
            echo json_encode(['error' => 'Formato de token inválido']);
            exit;
        }

        $token = $parts[1];
        $payload = $this->jwt->decode($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido o expirado']);
            exit;
        }

        return $payload;
    }

    public function requireRole(array $userData, string ...$allowedRoles): void
    {
        if (!isset($userData['rol']) || !in_array($userData['rol'], $allowedRoles)) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para realizar esta acción']);
            exit;
        }
    }
}
