<?php

return [
    'name' => 'SaludGo API',
    'env' => 'development',
    'debug' => true,
    'timezone' => 'America/Bogota',
    
    'jwt' => [
        'secret' => 'tu_clave_secreta_super_segura_cambiala_en_produccion',
        'algorithm' => 'HS256',
        'expiration' => 86400, // 24 horas en segundos
    ],
    
    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization'],
    ]
];
