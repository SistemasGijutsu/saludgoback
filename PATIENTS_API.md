# API de Pacientes - SaludGo Backend

## 📋 Resumen de Cambios

Se ha agregado la funcionalidad completa para gestionar **Pacientes** en el sistema SaludGo, siguiendo la arquitectura hexagonal del proyecto.

## 🎯 Diseño

**Importante:** Los pacientes usan la tabla `usuarios` para sus datos personales (nombre, teléfono, dirección, etc.). La tabla `pacientes` solo almacena **información médica adicional** que no está en `usuarios`.

Esta estructura es consistente con cómo funciona `profesionales`:
- `usuarios` → Datos básicos del usuario (nombre, email, teléfono, ciudad, etc.)
- `pacientes` → Info médica (alergias, tipo sangre, contacto emergencia)
- `profesionales` → Info adicional (cédula, tarifa, especialidad)

## 🗄️ Base de Datos

### Nueva Tabla: `pacientes`

```sql
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    contacto_emergencia_nombre VARCHAR(200) NULL,
    contacto_emergencia_telefono VARCHAR(20) NULL,
    tipo_sangre ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
    alergias TEXT NULL,
    condiciones_cronicas TEXT NULL,
    notas_medicas TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

**Para aplicar cambios:** Ejecutar el archivo `database.sql` en tu base de datos MySQL.

## 🚀 Nuevos Endpoints

### 1. Registrar Paciente (Público)
```http
POST /api/register/patient
Content-Type: application/json

{
  "email": "paciente@example.com",
  "password": "password123",
  "nombre": "Juan Pérez López",
  "telefono": "+57 300 123 4567",
  "fecha_nacimiento": "1990-05-15",
  "edad": 35,
  "genero": "masculino",
  "ciudad": "Bogotá",
  "direccion": "Calle 123 #45-67",
  
  "_comment": "Información médica (opcional)",
  "contacto_emergencia_nombre": "María Pérez",
  "contacto_emergencia_telefono": "+57 300 765 4321",
  "tipo_sangre": "O+",
  "alergias": "Ninguna conocida",
  "condiciones_cronicas": "Ninguna",
  "notas_medicas": "Paciente sano"
}
```

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "message": "Paciente registrado exitosamente",
  "data": {
    "user": {
      "id": 1,
      "nombre": "Juan Pérez López",
      "email": "paciente@example.com",
      "rol": "paciente",
      "telefono": "+57 300 123 4567",
      "ciudad": "Bogotá",
      ...
    },
    "medical_profile": {
      "id": 1,
      "usuario_id": 1,
      "contacto_emergencia_nombre": "María Pérez",
      "tipo_sangre": "O+",
      "alergias": "Ninguna conocida",
      ...
    }
  }
}
```

### 2. Obtener Mi Perfil (Autenticado - Solo Pacientes)
```http
GET /api/patients/me
Authorization: Bearer <token>
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "nombre_completo": "Juan Pérez",
    "fecha_nacimiento": "1990-05-15",
    "genero": "M",
    "telefono": "+57 300 123 4567",
    "ciudad": "Bogotá",
    "blood_type": "O+",
    ...
  }
}
```

### 3. Obtener Perfil por ID (Autenticado)
```http
GET /api/patients/{id}
Authorization: Bearer <token>
```

### 4. Obtener Perfil por User ID (Autenticado)
```http
GET /api/patients/by-user/{userId}
Authorization: Bearer <token>
```

### 5. Actualizar Perfil de Paciente (Autenticado - Solo Pacientes)
```http
PUT /api/patients/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
  "telefono": "+57 300 999 8888",
  "direccion": "Nueva dirección",
  "ciudad": "Medellín",
  "estado": "Antioquia",
  "allergies": "Penicilina"
}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Perfil actualizado exitosamente",
  "data": { ... }
}
```

### 6. Listar Pacientes (Autenticado - Con Paginación)
```http
GET /api/patients?limit=50&offset=0
Authorization: Bearer <token>
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "limit": 50,
    "offset": 0,
    "count": 10
  }
}
```

## 📁 Archivos Creados/Modificados

### Nuevos Archivos:
- ✅ `src/Domain/Entities/PatientProfile.php` - Entidad de perfil médico
- ✅ `src/Domain/Repositories/PatientProfileRepositoryInterface.php` - Interfaz del repositorio
- ✅ `src/Infrastructure/Persistence/PatientProfileRepository.php` - Implementación del repositorio
- ✅ `src/Application/DTOs/RegisterPatientDTO.php` - DTO para registro
- ✅ `src/Application/UseCases/RegisterPatientUseCase.php` - Caso de uso
- ✅ `src/Infrastructure/Controllers/PatientController.php` - Controlador
- ✅ `test_patients_data.sql` - Datos de prueba

### Modificados:
- ✅ `database.sql` - Agregada tabla `pacientes`
- ✅ `src/Infrastructure/routes.php` - Agregadas rutas de pacientes

## 🔑 Campos Obligatorios vs Opcionales

### Obligatorios (En tabla `usuarios`):
- `email` (único)
- `password` (mínimo 6 caracteres)
- `nombre`

### Opcionales (En tabla `usuarios`):
- `telefono`
- `fecha_nacimiento` (formato: Y-m-d)
- `edad`
- `genero` (masculino, femenino, otro)
- `ciudad`
- `direccion`

### Opcionales (Información médica en tabla `pacientes`):
- `contacto_emergencia_nombre`
- `contacto_emergencia_telefono`
- `tipo_sangre` (A+, A-, B+, B-, AB+, AB-, O+, O-)
- `alergias`
- `condiciones_cronicas`
- `notas_medicas`

**Nota:** La tabla `pacientes` se crea automáticamente solo si se proporciona al menos un campo médico durante el registro.

## ✅ Validaciones

El backend valida:
- ✓ Email único y formato válido
- ✓ Contraseña con mínimo 6 caracteres
- ✓ Campo nombre obligatorio
- ✓ Género debe ser masculino, femenino u otro
- ✓ Tipo de sangre debe ser válido
- ✓ Fecha de nacimiento en formato Y-m-d

## 🔐 Autenticación

- Las rutas públicas: `/register/patient`
- Las rutas protegidas requieren: Header `Authorization: Bearer <token>`
- Algunas rutas están restringidas a rol `paciente`

## 🧪 Prueba Rápida con cURL

```bash
# 1. Registrar un paciente
curl -X POST http://localhost:8080/api/register/patient \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "nombre": "Juan Pérez",
    "telefono": "+57 300 123 4567",
    "ciudad": "Bogotá",
    "tipo_sangre": "O+",
    "alergias": "Ninguna"
  }'

# 2. Login
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# 3. Obtener mi perfil (usar el token del login)
curl -X GET http://localhost:8080/api/patients/me \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

## 📝 Notas Importantes

1. **Arquitectura:** Se siguió el patrón de arquitectura hexagonal del proyecto existente
2. **Diseño:** Similar a `profesionales`, los datos personales están en `usuarios` y solo info adicional en `pacientes`
3. **Namespaces:** Se usaron los namespaces sin prefijo (`Domain`, `Application`, `Infrastructure`)
4. **Tabla usuarios:** Los pacientes se registran en `usuarios` con rol='paciente'
5. **Tabla pacientes:** Solo se crea un registro si hay información médica que guardar
6. **Consistencia:** Compatible con las tablas `solicitudes_servicio`, `ofertas` y `servicios` existentes

## 🔄 Próximos Pasos

Una vez aplicados estos cambios, podrás:
1. Ejecutar `database.sql` para crear la tabla
2. Probar el registro de pacientes desde Flutter
3. Los pacientes podrán crear solicitudes de servicio
4. Ver y actualizar su perfil completo

---
**SaludGo Backend** - Sistema de gestión de salud 🏥
