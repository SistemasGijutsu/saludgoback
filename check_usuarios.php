<?php
// Script para verificar usuarios recientes

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\Database;

$db = Database::getInstance();

try {
    echo "=== ESTRUCTURA DE LA TABLA usuarios ===\n\n";
    
    $stmt = $db->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo sprintf("%-30s %-20s\n", $col['Field'], $col['Type']);
    }
    
    echo "\n=== ÚLTIMOS USUARIOS REGISTRADOS ===\n\n";
    
    $stmt = $db->query("SELECT * FROM usuarios ORDER BY id DESC LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($usuarios as $user) {
        echo "ID: " . $user['id'] . " | Nombre: " . $user['nombre'] . " | Email: " . $user['email'] . " | Rol: " . $user['rol'] . "\n";
    }
    
    echo "\n=== USUARIOS PROFESIONALES SIN PERFIL ===\n";
    $stmt = $db->query("
        SELECT u.id, u.nombre, u.email
        FROM usuarios u 
        LEFT JOIN profesionales p ON u.id = p.usuario_id 
        WHERE u.rol = 'profesional' AND p.id IS NULL
    ");
    $sinPerfil = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($sinPerfil) > 0) {
        foreach ($sinPerfil as $user) {
            echo "ID: " . $user['id'] . " | Nombre: " . $user['nombre'] . " | Email: " . $user['email'] . "\n";
        }
        echo "\n⚠️ Estos " . count($sinPerfil) . " usuarios profesionales NO tienen perfil en la tabla profesionales\n";
    } else {
        echo "✅ Todos los profesionales tienen su perfil creado\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
