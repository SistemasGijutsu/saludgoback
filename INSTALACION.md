# 🚀 INSTALACIÓN RÁPIDA - SaludGo Backend

## Paso 1: Verificar requisitos
- ✅ XAMPP instalado con Apache y MySQL
- ✅ PHP 8.0 o superior
- ✅ Extensión PDO y pdo_mysql habilitadas

## Paso 2: Proyecto ya en su ubicación
El proyecto está en: `c:\xampp\htdocs\saludgoft\saludgo-backend`

## Paso 3: Crear las tablas en la base de datos

1. Abrir phpMyAdmin: http://localhost:8080/phpmyadmin/
2. La base de datos `saludgo` ya existe (según las imágenes que proporcionaste)
3. Seleccionar la base de datos `saludgo`
4. Ir a la pestaña "SQL"
5. Copiar y pegar el contenido del archivo `database.sql`
6. Hacer clic en "Ejecutar"

Esto creará las tablas:
- ✅ `solicitudes_servicio`
- ✅ `ofertas`  
- ✅ `servicios`

Las tablas `users`, `especialidades` y `profesionales` ya existen según las imágenes.

## Paso 4: Configurar la base de datos (OPCIONAL)

Si tu configuración de MySQL es diferente, editar `config/database.php`:

```php
'host' => 'localhost',
'port' => '3306',
'database' => 'saludgo',
'username' => 'root',
'password' => '',  // Cambiar si tienes contraseña
```

## Paso 5: Verificar Apache

1. Asegurarse de que Apache esté corriendo en XAMPP
2. Verificar que el módulo `mod_rewrite` esté habilitado en `httpd.conf`

## Paso 6: Probar la API

La URL base de la API es:
```
http://localhost/saludgoft/saludgo-backend/public/api
```

### Test rápido:
Abrir el navegador o Postman y hacer:
```
GET http://localhost/saludgoft/saludgo-backend/public/api/specialties
```

Deberías ver un JSON con especialidades.

## Paso 7: Importar colección de Postman

1. Abrir Postman
2. Hacer clic en "Import"
3. Seleccionar el archivo `SaludGo_Postman_Collection.json`
4. ¡Listo! Ya tienes todos los endpoints para probar

## Flujo de prueba completo

### 1. Registrar un paciente
```
POST /api/register/patient
{
    "nombre": "Juan Pérez",
    "email": "juan@test.com",
    "password": "123456"
}
```

### 2. Registrar un médico
```
POST /api/register/doctor
{
    "nombre": "Dra. María García",
    "email": "maria@test.com",
    "password": "123456",
    "especialidad_id": 1,
    "cedula": "123456789",
    "tarjeta_profesional": "TP123",
    "medio_transporte": "motocicleta",
    "anos_experiencia": 5,
    "tarifa_consulta": 50000
}
```

**IMPORTANTE:** El médico necesita ser verificado manualmente en la base de datos:
```sql
UPDATE profesionales 
SET verificado = 1, estado_verificacion = 'aprobado' 
WHERE usuario_id = [ID_DEL_USUARIO_MEDICO];
```

### 3. Login paciente
```
POST /api/login
{
    "email": "juan@test.com",
    "password": "123456"
}
```
Guardar el `token` que devuelve.

### 4. Crear solicitud (paciente)
```
POST /api/service-requests
Authorization: Bearer {token_paciente}
{
    "especialidad_id": 1,
    "descripcion": "Necesito consulta médica"
}
```

### 5. Login médico y ver solicitudes
```
POST /api/login
{
    "email": "maria@test.com",
    "password": "123456"
}
```

```
GET /api/service-requests/available
Authorization: Bearer {token_medico}
```

### 6. Médico envía oferta
```
POST /api/service-requests/1/offer
Authorization: Bearer {token_medico}
{
    "price": 50000,
    "message": "Puedo atenderte en 30 minutos"
}
```

### 7. Paciente ve ofertas
```
GET /api/service-requests/1/offers
Authorization: Bearer {token_paciente}
```

### 8. Paciente acepta oferta
```
POST /api/offers/1/accept
Authorization: Bearer {token_paciente}
```

### 9. Completar servicio
```
POST /api/services/1/complete
Authorization: Bearer {token_medico o token_paciente}
```

## 🐛 Solución de problemas

### Error: "Ruta no encontrada"
- Verificar que `mod_rewrite` esté habilitado
- Verificar que el archivo `.htaccess` exista en `/public`

### Error: "Error de conexión a la base de datos"
- Verificar que MySQL esté corriendo
- Verificar credenciales en `config/database.php`
- Verificar que la base de datos `saludgo` exista

### Error: "Token inválido"
- El token expira en 24 horas
- Hacer login nuevamente para obtener un token nuevo

### Error 500
- Activar debug en `config/app.php`: `'debug' => true`
- Ver los errores detallados

## 📝 Notas importantes

1. **Seguridad:** Cambiar `jwt.secret` en `config/app.php` en producción
2. **Verificación de médicos:** Debe hacerse manualmente o crear un endpoint admin
3. **Los médicos NO verificados no pueden ver solicitudes ni enviar ofertas**
4. **Un médico solo puede enviar UNA oferta por solicitud** (restricción en BD)
5. **Al aceptar una oferta, automáticamente se rechazan las demás**

## ✅ Listo para producción

Para mover a producción:
- Cambiar `debug` a `false` en `config/app.php`
- Cambiar `jwt.secret` a un valor seguro
- Configurar variables de entorno
- Habilitar HTTPS
- Configurar límites de rate limiting

## 🎉 ¡Todo listo!

Tu backend está completamente funcional y listo para conectarse desde Flutter.
