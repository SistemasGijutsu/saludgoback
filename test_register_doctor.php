<?php
// Script para probar el registro de profesional

require_once __DIR__ . '/autoload.php';

// Simular datos JSON de la petición
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

$testData = [
    'nombre' => 'Dr. Test Backend',
    'email' => 'drtest' . time() . '@test.com',
    'password' => '123456',
    'especialidad_id' => 3,
    'cedula' => '9999999999',
    'tarjeta_profesional' => 'TP123456',
    'medio_transporte' => 'automovil',
    'anos_experiencia' => 5,
    'tarifa_consulta' => 50000,
    'descripcion' => 'Doctor de prueba desde backend'
];

echo "=== PROBANDO REGISTRO DE PROFESIONAL ===\n";
echo "Email: " . $testData['email'] . "\n";
echo "Datos: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Simular la entrada JSON
$_POST = [];
file_put_contents('php://input', json_encode($testData));

try {
    // Cargar el controller
    $controller = new Infrastructure\Controllers\AuthController();
    
    echo "Ejecutando registro...\n";
    ob_start();
    $controller->registerDoctor();
    $output = ob_get_clean();
    
    echo "\n=== RESPUESTA ===\n";
    echo $output;
    
    // Verificar si se creó
    $db = Infrastructure\Persistence\Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$testData['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "\n\n✅ Usuario creado en tabla usuarios (ID: " . $user['id'] . ")\n";
        
        $stmt = $db->prepare("SELECT * FROM profesionales WHERE usuario_id = ?");
        $stmt->execute([$user['id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doctor) {
            echo "✅ Perfil creado en tabla profesionales (ID: " . $doctor['id'] . ")\n";
            echo "   - Especialidad: " . $doctor['especialidad_id'] . "\n";
            echo "   - Cédula: " . $doctor['cedula'] . "\n";
        } else {
            echo "❌ NO se creó el perfil en tabla profesionales\n";
        }
    } else {
        echo "\n\n❌ NO se creó el usuario en tabla usuarios\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
