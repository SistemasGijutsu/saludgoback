<?php
// Simular exactamente la petición de login de Flutter

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\UserRepository;
use Infrastructure\Auth\AuthService;

// Datos que enviaría Flutter
$loginData = [
    'email' => 'drresponse1771884276@test.com',
    'password' => '123456'
];

echo "=== SIMULANDO LOGIN DESDE FLUTTER ===\n";
echo "Email: " . $loginData['email'] . "\n\n";

try {
    $authService = new AuthService(new UserRepository());
    $result = $authService->login($loginData['email'], $loginData['password']);
    
    echo "=== RESPUESTA DEL BACKEND ===\n";
    $json = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo $json . "\n";
    
    echo "\n=== ANÁLISIS DE TIPOS ===\n";
    foreach ($result['user'] as $key => $value) {
        $type = gettype($value);
        $val = is_null($value) ? 'NULL' : $value;
        echo sprintf("%-20s %-10s %s\n", $key, "($type)", $val);
    }
    
    echo "\n=== PRUEBA DE PARSEO COMO LO HARÍA FLUTTER ===\n";
    // Simular lo que hace Flutter
    $decoded = json_decode($json, true);
    if ($decoded && isset($decoded['user'])) {
        echo "✓ JSON decodificable\n";
        echo "✓ user['id'] tipo: " . gettype($decoded['user']['id']) . " valor: " . $decoded['user']['id'] . "\n";
        echo "✓ user['rol'] tipo: " . gettype($decoded['user']['rol']) . " valor: " . $decoded['user']['rol'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
