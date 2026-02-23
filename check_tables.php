<?php
// Script rápido para verificar las tablas en la base de datos

$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    echo "Conectado a la base de datos '{$config['database']}'\n\n";
    
    // Listar todas las tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tablas encontradas:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n";
    
    // Verificar específicamente si existe 'users' o 'usuarios'
    echo "Verificación específica:\n";
    echo "  - Tabla 'users': " . (in_array('users', $tables) ? "✅ EXISTE" : "❌ NO EXISTE") . "\n";
    echo "  - Tabla 'usuarios': " . (in_array('usuarios', $tables) ? "✅ EXISTE" : "❌ NO EXISTE") . "\n";
    echo "  - Tabla 'especialidades': " . (in_array('especialidades', $tables) ? "✅ EXISTE" : "❌ NO EXISTE") . "\n";
    echo "  - Tabla 'profesionales': " . (in_array('profesionales', $tables) ? "✅ EXISTE" : "❌ NO EXISTE") . "\n";
    
    echo "\n";
    
    // Si existe 'users', mostrar su estructura
    if (in_array('users', $tables)) {
        echo "Estructura de la tabla 'users':\n";
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll();
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
    }
    
    // Si existe 'usuarios', mostrar su estructura
    if (in_array('usuarios', $tables)) {
        echo "\nEstructura de la tabla 'usuarios':\n";
        $stmt = $pdo->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll();
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
