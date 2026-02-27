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

if (!function_exists('uploadImage')) {
    /**
     * Sube una imagen al servidor y retorna la ruta relativa
     * 
     * @param array $file El archivo de $_FILES
     * @param string $directory El directorio donde guardar (relativo a uploads/)
     * @param int $maxSize Tamaño máximo en bytes (default 5MB)
     * @return string|null Ruta relativa de la imagen o null si falla
     */
    function uploadImage(array $file, string $directory = 'profiles', int $maxSize = 5242880): ?string {
        // Validar que se subió correctamente
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validar tamaño
        if ($file['size'] > $maxSize) {
            throw new \InvalidArgumentException('La imagen es demasiado grande. Máximo 5MB');
        }

        // Validar tipo MIME
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new \InvalidArgumentException('Tipo de archivo no válido. Solo JPG, PNG y WEBP');
        }

        // Crear directorio si no existe
        $uploadDir = __DIR__ . '/../../uploads/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('img_') . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . '/' . $fileName;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new \RuntimeException('Error al guardar la imagen');
        }

        // Retornar ruta relativa
        return 'uploads/' . $directory . '/' . $fileName;
    }
}

if (!function_exists('deleteImage')) {
    /**
     * Elimina una imagen del servidor
     * 
     * @param string|null $imagePath Ruta relativa de la imagen
     * @return bool
     */
    function deleteImage(?string $imagePath): bool {
        if (!$imagePath) {
            return false;
        }

        $fullPath = __DIR__ . '/../../' . $imagePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}

if (!function_exists('getImageUrl')) {
    /**
     * Obtiene la URL completa de una imagen
     * 
     * @param string|null $imagePath Ruta relativa de la imagen
     * @return string|null
     */
    function getImageUrl(?string $imagePath): ?string {
        if (!$imagePath) {
            return null;
        }

        $baseUrl = config('app.url', 'http://localhost:8000');
        return $baseUrl . '/' . $imagePath;
    }
}
