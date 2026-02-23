<?php

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        static $config = [];
        
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        if (!isset($config[$file])) {
            $path = __DIR__ . '/../../config/' . $file . '.php';
            if (!file_exists($path)) {
                return $default;
            }
            $config[$file] = require $path;
        }
        
        $value = $config[$file];
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
}

if (!function_exists('response')) {
    function response(array $data = [], int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('jsonInput')) {
    function jsonInput(): array {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
