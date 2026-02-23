<?php
// Script para simular exactamente lo que envía Flutter

require_once __DIR__ . '/autoload.php';

// Simular la request JSON de Flutter
$flutterData = [
    'nombre' => 'Dr. Flutter Test',
    'email' => 'drflutter' . time() . '@test.com',
    'password' => '123456',
    'especialidad_id' => 3,
    'cedula' => '1234567890',
    'tarjeta_profesional' => 'TP123456',
    'medio_transporte' => 'Motocicleta', // Flutter envía con mayúscula
    'anos_experiencia' => 1,
    'tarifa_consulta' => 50000,
    'descripcion' => 'Profesional de la salud registrado',
    'telefono' => '3001234567',
    'genero' => 'masculino',
    'edad' => 35
];

echo "=== SIMULANDO PETICIÓN DESDE FLUTTER ===\n";
echo "Datos: " . json_encode($flutterData, JSON_PRETTY_PRINT) . "\n\n";

try {
    $controller = new Infrastructure\Controllers\AuthController();
    
    // Simular el jsonInput()
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    file_put_contents('php://input', json_encode($flutterData));
    
    echo "▶ Ejecutando registerDoctor()...\n\n";
    
    // Capturar la salida
    ob_start();
    $controller->registerDoctor();
    $output = ob_get_clean();
    
    echo "=== RESPUESTA DEL BACKEND ===\n";
    echo $output . "\n\n";
    
    // Verificar en la base de datos
    $db = Infrastructure\Persistence\Database::getInstance();
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$flutterData['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Usuario creado (ID: " . $user['id'] . ")\n";
        
        $stmt = $db->prepare("SELECT * FROM profesionales WHERE usuario_id = ?");
        $stmt->execute([$user['id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doctor) {
            echo "✅ Perfil profesional creado (ID: " . $doctor['id'] . ")\n";
            echo "   - Especialidad: " . $doctor['especialidad_id'] . "\n";
            echo "   - Cédula: " . $doctor['cedula'] . "\n";
            echo "   - Medio transporte: " . $doctor['medio_transporte'] . "\n";
        } else {
            echo "❌ NO se creó el perfil en tabla profesionales\n";
        }
    } else {
        echo "❌ NO se creó el usuario\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
