<?php

spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

// Cargar helpers
require_once __DIR__ . '/src/Infrastructure/helpers.php';
