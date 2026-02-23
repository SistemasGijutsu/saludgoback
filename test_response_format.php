<?php
// Ver exactamente qué devuelve el backend

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\UserRepository;
use Infrastructure\Persistence\DoctorProfileRepository;
use Infrastructure\Auth\AuthService;
use Domain\Entities\DoctorProfile;

$testData = [
    'nombre' => 'Dr. Response Test',
    'email' => 'drresponse' . time() . '@test.com',
    'password' => '123456',
    'especialidad_id' => 3,
    'cedula' => '9999999999',
    'tarjeta_profesional' => 'TP123456',
    'medio_transporte' => 'motocicleta',
    'anos_experiencia' => 5,
    'tarifa_consulta' => 50000,
    'descripcion' => 'Doctor de prueba'
];

try {
    // Paso 1: Registrar usuario
    $authService = new AuthService(new UserRepository());
    $result = $authService->register(
        $testData['nombre'],
        $testData['email'],
        $testData['password'],
        'profesional'
    );
    
    echo "=== RESPUESTA DEL AUTH SERVICE ===\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    $userId = $result['user']['id'];
    
    // Paso 2: Crear perfil
    $doctorRepo = new DoctorProfileRepository();
    $profile = new DoctorProfile($userId, intval($testData['especialidad_id']));
    $profile->setCedula($testData['cedula']);
    $profile->setTarjetaProfesional($testData['tarjeta_profesional']);
    $profile->setMedioTransporte($testData['medio_transporte']);
    $profile->setAnosExperiencia(intval($testData['anos_experiencia']));
    $profile->setTarifaConsulta(floatval($testData['tarifa_consulta']));
    $profile->setDescripcion($testData['descripcion']);
    
    $profile = $doctorRepo->save($profile);
    $result['doctor_profile'] = $profile->toArray();
    
    echo "\n=== RESPUESTA FINAL (COMO LA ENVÍA EL BACKEND) ===\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    echo "\n=== VERIFICANDO CAMPOS NULOS ===\n";
    foreach ($result['user'] as $key => $value) {
        if ($value === null) {
            echo "⚠️  Campo user.$key es NULL\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
