<?php

namespace Infrastructure\Persistence;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    
    private function __construct() {}
    
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../../config/database.php';
            
            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                    $config['host'],
                    $config['port'],
                    $config['database'],
                    $config['charset']
                );
                
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                throw new \RuntimeException("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
    
    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }
    
    public static function commit(): void
    {
        self::getInstance()->commit();
    }
    
    public static function rollback(): void
    {
        self::getInstance()->rollBack();
    }
}
