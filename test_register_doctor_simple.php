<?php
// Script para probar el registro de profesional - Versión simplificada

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\UserRepository;
use Infrastructure\Persistence\DoctorProfileRepository;
use Infrastructure\Auth\AuthService;
use Domain\Entities\DoctorProfile;

$testData = [
    'nombre' => 'Dr. Test Directo',
    'email' => 'drtest' . time() . '@test.com',
    'password' => '123456',
    'especialidad_id' => 3,
    'cedula' => '8888888888',
    'tarjeta_profesional' => 'TP999999',
    'medio_transporte' => 'automovil',
    'anos_experiencia' => 7,
    'tarifa_consulta' => 75000,
    'descripcion' => 'Doctor de prueba directo'
];

echo "=== PROBANDO REGISTRO DE PROFESIONAL (Versión directa) ===\n";
echo "Email: " . $testData['email'] . "\n\n";

try {
    // Paso 1: Registrar usuario
    echo "▶ Paso 1: Registrando usuario...\n";
    $authService = new AuthService(new UserRepository());
    $result = $authService->register(
        $testData['nombre'],
        $testData['email'],
        $testData['password'],
        'profesional'
    );
    
    echo "✅ Usuario creado exitosamente (ID: " . $result['user']['id'] . ")\n";
    $userId = $result['user']['id'];
    
    // Paso 2: Crear perfil de doctor
    echo "\n▶ Paso 2: Creando perfil de profesional...\n";
    $doctorRepo = new DoctorProfileRepository();
    
    $especialidadId = null;
    if (isset($testData['especialidad_id'])) {
        $especialidadId = intval($testData['especialidad_id']);
    }
    
    $profile = new DoctorProfile($userId, $especialidadId);
    
    if (isset($testData['cedula'])) {
        $profile->setCedula($testData['cedula']);
    }
    if (isset($testData['tarjeta_profesional'])) {
        $profile->setTarjetaProfesional($testData['tarjeta_profesional']);
    }
    if (isset($testData['medio_transporte'])) {
        $profile->setMedioTransporte($testData['medio_transporte']);
    }
    if (isset($testData['anos_experiencia'])) {
        $profile->setAnosExperiencia(intval($testData['anos_experiencia']));
    }
    if (isset($testData['tarifa_consulta'])) {
        $profile->setTarifaConsulta(floatval($testData['tarifa_consulta']));
    }
    if (isset($testData['descripcion'])) {
        $profile->setDescripcion($testData['descripcion']);
    }
    
    echo "  - Cedula: " . $profile->getCedula() . "\n";
    echo "  - Especialidad ID: " . $profile->getEspecialidadId() . "\n";
    echo "  - Medio transporte: " . $profile->getMedioTransporte() . "\n";
    
    $profile = $doctorRepo->save($profile);
    
    echo "✅ Perfil de profesional creado exitosamente (ID: " . $profile->getId() . ")\n";
    
    echo "\n=== RESULTADO FINAL ===\n";
    echo "✅ TODO EXITOSO\n";
    echo "Usuario ID: " . $userId . "\n";
    echo "Profesional ID: " . $profile->getId() . "\n";
    echo "Email: " . $testData['email'] . "\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
