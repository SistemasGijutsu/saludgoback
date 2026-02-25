<?php
// Probar el login y ver la respuesta exacta

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\UserRepository;
use Infrastructure\Auth\AuthService;

try {
    $authService = new AuthService(new UserRepository());
    
    // Intentar login con un usuario existente
    $result = $authService->login('drresponse1771884276@test.com', '123456');
    
    echo "=== RESPUESTA DEL LOGIN ===\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    echo "\n=== TIPOS DE DATOS ===\n";
    echo "Tipo de user['id']: " . gettype($result['user']['id']) . "\n";
    echo "Valor de user['id']: " . $result['user']['id'] . "\n";
    echo "Tipo de user['activo']: " . gettype($result['user']['activo']) . "\n";
    echo "Valor de user['activo']: " . $result['user']['activo'] . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
