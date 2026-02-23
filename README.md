# SaludGo Backend API

API REST para la plataforma SaludGo - Sistema de servicios médicos similar a Indriver.

## 📋 Requisitos

- PHP 8.0+
- MySQL 5.7+
- Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, pdo_mysql

## 🚀 Instalación

1. Clonar o descargar el proyecto en `c:\xampp\htdocs\saludgoft\saludgo-backend`

2. Crear la base de datos MySQL con el script SQL incluido en este README (ver más abajo)

3. Configurar la conexión a la base de datos en `config/database.php`

4. Asegurarse de que el módulo `mod_rewrite` de Apache esté habilitado

5. La URL base de la API será: `http://localhost/saludgoft/saludgo-backend/public/api`

## 📊 Script SQL

Ejecutar este script en phpMyAdmin para crear las tablas necesarias:

```sql
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS saludgo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saludgo;

-- Tabla solicitudes_servicio
CREATE TABLE IF NOT EXISTS solicitudes_servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    especialidad_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    status ENUM('OPEN', 'TAKEN', 'COMPLETED', 'CANCELLED') DEFAULT 'OPEN',
    accepted_offer_id INT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_paciente (paciente_id),
    INDEX idx_especialidad_status (especialidad_id, status),
    FOREIGN KEY (paciente_id) REFERENCES users(id),
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla ofertas
CREATE TABLE IF NOT EXISTS ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_request_id INT NOT NULL,
    doctor_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    message TEXT NULL,
    status ENUM('PENDING', 'ACCEPTED', 'REJECTED') DEFAULT 'PENDING',
    created_at DATETIME NOT NULL,
    INDEX idx_service_request (service_request_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_doctor_request (doctor_id, service_request_id),
    FOREIGN KEY (service_request_id) REFERENCES solicitudes_servicio(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_request_id INT NOT NULL,
    doctor_id INT NOT NULL,
    paciente_id INT NOT NULL,
    final_price DECIMAL(10,2) NOT NULL,
    started_at DATETIME NOT NULL,
    completed_at DATETIME NULL,
    status ENUM('IN_PROGRESS', 'COMPLETED', 'CANCELLED') DEFAULT 'IN_PROGRESS',
    INDEX idx_doctor (doctor_id),
    INDEX idx_paciente (paciente_id),
    FOREIGN KEY (service_request_id) REFERENCES solicitudes_servicio(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (paciente_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 🔌 Endpoints de la API

### 🔑 Autenticación

#### Registrar Paciente
```
POST /api/register/patient
Content-Type: application/json

{
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "password": "mipassword123"
}
```

#### Registrar Médico/Profesional
```
POST /api/register/doctor
Content-Type: application/json

{
    "nombre": "Dr. María García",
    "email": "maria@example.com",
    "password": "mipassword123",
    "especialidad_id": 1,
    "cedula": "123456789",
    "tarjeta_profesional": "TP123456",
    "medio_transporte": "motocicleta",
    "anos_experiencia": 5,
    "tarifa_consulta": 50000,
    "descripcion": "Médico general con 5 años de experiencia"
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
    "email": "juan@example.com",
    "password": "mipassword123"
}

Response:
{
    "user": {...},
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

#### Obtener perfil actual
```
GET /api/me
Authorization: Bearer {token}
```

### 🏥 Especialidades

#### Listar especialidades activas
```
GET /api/specialties
```

### 👤 Endpoints de Paciente

#### Crear solicitud de servicio
```
POST /api/service-requests
Authorization: Bearer {token}
Content-Type: application/json

{
    "especialidad_id": 1,
    "descripcion": "Necesito consulta médica general en mi domicilio"
}
```

#### Ver mis solicitudes
```
GET /api/service-requests/my
Authorization: Bearer {token}
```

#### Ver ofertas de una solicitud
```
GET /api/service-requests/{id}/offers
Authorization: Bearer {token}
```

#### Aceptar una oferta
```
POST /api/offers/{id}/accept
Authorization: Bearer {token}
```

### 🧑‍⚕️ Endpoints de Médico/Profesional

#### Ver solicitudes disponibles
```
GET /api/service-requests/available
Authorization: Bearer {token}
```

#### Enviar oferta a una solicitud
```
POST /api/service-requests/{id}/offer
Authorization: Bearer {token}
Content-Type: application/json

{
    "price": 50000,
    "message": "Puedo atenderte en 30 minutos"
}
```

#### Ver mis ofertas
```
GET /api/offers/my
Authorization: Bearer {token}
```

### 🔄 Endpoints de Servicios (Paciente y Médico)

#### Ver mis servicios
```
GET /api/services/my
Authorization: Bearer {token}
```

#### Completar un servicio
```
POST /api/services/{id}/complete
Authorization: Bearer {token}
```

## 🏗️ Arquitectura

El proyecto sigue una arquitectura limpia (Clean Architecture) con las siguientes capas:

```
src/
├── Domain/              # Capa de dominio (entidades, interfaces)
│   ├── Entities/        # Entidades del negocio
│   ├── Repositories/    # Interfaces de repositorios
│   └── ValueObjects/    # Objetos de valor
│
├── Application/         # Capa de aplicación (lógica de negocio)
│   ├── UseCases/        # Casos de uso
│   └── DTOs/            # Data Transfer Objects
│
└── Infrastructure/      # Capa de infraestructura
    ├── Controllers/     # Controladores HTTP
    ├── Persistence/     # Implementación de repositorios
    ├── Middleware/      # Middlewares
    └── Auth/            # Sistema de autenticación JWT
```

## 🔒 Seguridad

- Autenticación JWT con tokens de 24 horas
- Validación de roles (paciente/profesional)
- Middleware de autenticación en rutas protegidas
- Passwords hasheados con bcrypt
- Validación de permisos en cada acción

## 🧪 Reglas de Negocio Implementadas

✅ Un paciente puede crear muchas solicitudes  
✅ Solo médicos de la especialidad correcta pueden ver solicitudes  
✅ Un médico solo puede enviar una oferta por solicitud  
✅ El paciente solo puede aceptar una oferta  
✅ Al aceptar una oferta:
  - Se crea un servicio
  - Se bloquean las demás ofertas (REJECTED)
  - La solicitud queda tomada (TAKEN)  
✅ Solo el dueño puede completar su servicio  
✅ Los médicos deben estar verificados para ofertar

## 📝 Variables de Entorno

Editar `config/app.php` para cambiar:

- `jwt.secret`: Clave secreta para JWT (¡cambiar en producción!)
- `jwt.expiration`: Tiempo de expiración del token (segundos)
- `debug`: Activar/desactivar mensajes de error detallados

## 🤝 Flujo de Trabajo

1. **Paciente** se registra y crea una solicitud de servicio
2. **Médico** verificado ve solicitudes de su especialidad
3. **Médico** envía oferta con precio y mensaje
4. **Paciente** revisa ofertas y acepta una
5. Se crea el **Servicio** automáticamente
6. **Médico** o **Paciente** completan el servicio
7. La solicitud queda como **COMPLETED**

## 📧 Soporte

Para dudas o issues, contactar al equipo de desarrollo.
