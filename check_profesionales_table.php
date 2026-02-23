<?php
// Script para verificar la estructura de la tabla profesionales

require_once __DIR__ . '/autoload.php';

use Infrastructure\Persistence\Database;

$db = Database::getInstance();

try {
    echo "=== ESTRUCTURA DE LA TABLA profesionales ===\n\n";
    
    $stmt = $db->query("DESCRIBE profesionales");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo sprintf("%-30s %-20s %-10s %-10s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'], 
            $col['Key']
        );
    }
    
    echo "\n=== CONTEO DE REGISTROS ===\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM profesionales");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de profesionales: " . $result['total'] . "\n\n";
    
    echo "\n=== ÚLTIMOS REGISTROS ===\n";
    $stmt = $db->query("SELECT * FROM profesionales ORDER BY id DESC LIMIT 3");
    $profesionales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($profesionales);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
