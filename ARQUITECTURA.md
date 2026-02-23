# 📐 ARQUITECTURA - SaludGo Backend

## 🏛️ Patrón Arquitectónico

Este proyecto implementa **Clean Architecture** (Arquitectura Limpia) con separación en 3 capas:

```
┌─────────────────────────────────────────────────┐
│                   Frontend                      │
│              (Flutter Mobile App)               │
└─────────────────┬───────────────────────────────┘
                  │ HTTP/JSON
                  │ REST API
┌─────────────────▼───────────────────────────────┐
│         INFRASTRUCTURE LAYER                    │
│  • Controllers (HTTP Handlers)                  │
│  • Middleware (Auth, CORS)                      │
│  • Router (URL Routing)                         │
│  • Persistence (MySQL Repositories)             │
│  • Auth (JWT Service)                           │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│         APPLICATION LAYER                       │
│  • Use Cases (Business Logic)                   │
│  • DTOs (Data Transfer Objects)                 │
│  • Orchestration & Validation                   │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│            DOMAIN LAYER                         │
│  • Entities (Business Objects)                  │
│  • Repository Interfaces                        │
│  • Value Objects                                │
│  • Business Rules                               │
└─────────────────────────────────────────────────┘
```

## 📂 Estructura de Directorios

```
saludgo-backend/
│
├── config/                      # Configuración
│   ├── app.php                  # Config general y JWT
│   └── database.php             # Conexión MySQL
│
├── public/                      # Punto de entrada web
│   ├── index.php                # Entry point
│   └── .htaccess                # Apache rewrite rules
│
├── src/
│   ├── Domain/                  # 🟦 CAPA DE DOMINIO
│   │   ├── Entities/            # Objetos de negocio
│   │   │   ├── User.php
│   │   │   ├── DoctorProfile.php
│   │   │   ├── Specialty.php
│   │   │   ├── ServiceRequest.php
│   │   │   ├── Offer.php
│   │   │   └── Service.php
│   │   │
│   │   ├── Repositories/        # Contratos (interfaces)
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── DoctorProfileRepositoryInterface.php
│   │   │   ├── SpecialtyRepositoryInterface.php
│   │   │   ├── ServiceRequestRepositoryInterface.php
│   │   │   ├── OfferRepositoryInterface.php
│   │   │   └── ServiceRepositoryInterface.php
│   │   │
│   │   └── ValueObjects/        # Objetos de valor inmutables
│   │
│   ├── Application/             # 🟨 CAPA DE APLICACIÓN
│   │   ├── UseCases/            # Lógica de negocio
│   │   │   ├── CreateServiceRequestUseCase.php
│   │   │   ├── ListAvailableServiceRequestsUseCase.php
│   │   │   ├── SendOfferUseCase.php
│   │   │   ├── AcceptOfferUseCase.php
│   │   │   ├── CompleteServiceUseCase.php
│   │   │   ├── GetServiceRequestOffersUseCase.php
│   │   │   └── RegisterDoctorUseCase.php
│   │   │
│   │   └── DTOs/                # Transfer objects
│   │       └── CreateServiceRequestDTO.php
│   │
│   └── Infrastructure/          # 🟩 CAPA DE INFRAESTRUCTURA
│       ├── Controllers/         # HTTP Request Handlers
│       │   ├── AuthController.php
│       │   ├── ServiceRequestController.php
│       │   ├── DoctorController.php
│       │   ├── OfferController.php
│       │   ├── ServiceController.php
│       │   └── SpecialtyController.php
│       │
│       ├── Persistence/         # Implementación de repositorios
│       │   ├── Database.php
│       │   ├── UserRepository.php
│       │   ├── DoctorProfileRepository.php
│       │   ├── SpecialtyRepository.php
│       │   ├── ServiceRequestRepository.php
│       │   ├── OfferRepository.php
│       │   └── ServiceRepository.php
│       │
│       ├── Middleware/          # HTTP Middleware
│       │   ├── AuthMiddleware.php
│       │   └── CorsMiddleware.php
│       │
│       ├── Auth/                # Sistema de autenticación
│       │   ├── JWT.php
│       │   └── AuthService.php
│       │
│       ├── Router.php           # Sistema de routing
│       ├── routes.php           # Definición de rutas
│       └── helpers.php          # Funciones helper
│
├── autoload.php                 # PSR-4 Autoloader
├── database.sql                 # Script de creación de BD
├── composer.json                # Dependencias (futuro)
├── README.md                    # Documentación principal
├── INSTALACION.md               # Guía de instalación
├── ARQUITECTURA.md              # Este archivo
├── API_TESTS.sh                 # Tests con cURL
└── SaludGo_Postman_Collection.json  # Colección Postman
```

## 🔄 Flujo de una Petición

```
1. HTTP Request
   ↓
2. public/index.php (Entry Point)
   ↓
3. CorsMiddleware::handle()
   ↓
4. Router->run()
   ↓
5. AuthMiddleware (si requiere auth)
   ↓
6. Controller (Infrastructure)
   ↓
7. Use Case (Application)
   ↓
8. Repository (Interface in Domain, Implementation in Infrastructure)
   ↓
9. Entity (Domain)
   ↓
10. Database (MySQL)
    ↓
11. Response (JSON)
```

## 🎯 Principios SOLID Aplicados

### 1. **Single Responsibility Principle (SRP)**
- Cada entidad tiene una única responsabilidad
- Los Use Cases encapsulan una acción específica
- Los Repositories solo manejan persistencia

### 2. **Open/Closed Principle (OCP)**
- Las entidades están cerradas a modificación pero abiertas a extensión
- Nuevos Use Cases no requieren modificar los existentes

### 3. **Liskov Substitution Principle (LSP)**
- Cualquier implementación de `UserRepositoryInterface` puede sustituirse
- Las abstracciones no dependen de detalles

### 4. **Interface Segregation Principle (ISP)**
- Interfaces pequeñas y específicas por repositorio
- Los clientes no dependen de métodos que no usan

### 5. **Dependency Inversion Principle (DIP)**
- Los Use Cases dependen de interfaces, no de implementaciones concretas
- La capa de dominio no conoce MySQL ni HTTP

## 🔐 Sistema de Autenticación

### JWT (JSON Web Tokens)

```
┌──────────────┐
│   Client     │
└──────┬───────┘
       │ 1. POST /api/login
       │    email + password
       ▼
┌──────────────────┐
│ AuthController   │
└──────┬───────────┘
       │ 2. Validate credentials
       ▼
┌──────────────────┐
│  AuthService     │
└──────┬───────────┘
       │ 3. Generate JWT
       ▼
┌──────────────────┐
│      JWT         │
│  {user_id, rol}  │
└──────┬───────────┘
       │ 4. Return token
       ▼
┌──────────────────┐
│   Client         │
│ Stores token     │
└──────────────────┘

Subsequent requests:
Authorization: Bearer {token}
```

## 🗄️ Modelo de Datos

```
┌─────────────┐
│    users    │
├─────────────┤
│ id          │◄─────┐
│ nombre      │      │
│ email       │      │
│ password    │      │
│ rol         │      │
└─────────────┘      │
       │             │
       │ 1:1         │
       ▼             │
┌─────────────────┐  │
│ profesionales   │  │
├─────────────────┤  │
│ id              │  │
│ usuario_id      ├──┘
│ especialidad_id ├──────┐
│ verificado      │      │
│ tarifa_consulta │      │
└─────────────────┘      │
                         │
        ┌────────────────┘
        │
        ▼
┌─────────────────────┐
│   especialidades    │
├─────────────────────┤
│ id                  │◄──────┐
│ nombre              │       │
│ descripcion         │       │
└─────────────────────┘       │
                              │
┌─────────────────────────┐   │
│ solicitudes_servicio    │   │
├─────────────────────────┤   │
│ id                      │◄──┼──┐
│ paciente_id             │   │  │
│ especialidad_id         ├───┘  │
│ descripcion             │      │
│ status (OPEN/TAKEN)     │      │
│ accepted_offer_id       │      │
└─────────────────────────┘      │
       │                         │
       │ 1:N                     │
       ▼                         │
┌─────────────────┐              │
│    ofertas      │              │
├─────────────────┤              │
│ id              │              │
│ service_request_id ├───────────┘
│ doctor_id       │
│ price           │
│ message         │
│ status          │
└─────────────────┘
       │
       │ 1:1 (aceptada)
       ▼
┌─────────────────┐
│   servicios     │
├─────────────────┤
│ id              │
│ service_request_id
│ doctor_id       │
│ paciente_id     │
│ final_price     │
│ status          │
│ completed_at    │
└─────────────────┘
```

## 🔒 Reglas de Negocio Implementadas

### ✅ Validaciones Críticas

1. **Registro de Médico**
   - Email único
   - Rol debe ser 'profesional'
   - Requiere especialidad

2. **Crear Solicitud**
   - Solo pacientes
   - Especialidad válida y activa
   - Descripción requerida

3. **Ver Solicitudes Disponibles**
   - Solo médicos verificados
   - Solo de su especialidad
   - Solo status OPEN

4. **Enviar Oferta**
   - Médico verificado
   - Solicitud abierta
   - Especialidad correcta
   - **UNA sola oferta por médico/solicitud**

5. **Aceptar Oferta**
   - Solo el paciente dueño
   - Solicitud abierta
   - Oferta pendiente
   - **Transacción atómica:**
     - Aceptar oferta
     - Rechazar todas las demás
     - Marcar solicitud como TAKEN
     - Crear servicio

6. **Completar Servicio**
   - Solo doctor o paciente del servicio
   - Servicio en progreso
   - **Transacción atómica:**
     - Marcar servicio como COMPLETED
     - Actualizar solicitud

## 🛡️ Seguridad

### Implementado
- ✅ JWT con expiración (24h)
- ✅ Passwords hasheados (bcrypt)
- ✅ Validación de roles por endpoint
- ✅ Middleware de autenticación
- ✅ CORS configurable
- ✅ Prepared statements (SQL injection prevention)
- ✅ Validación de ownership (no acceder datos ajenos)

### Recomendaciones para Producción
- 🔴 HTTPS obligatorio
- 🔴 Rate limiting
- 🔴 Refresh tokens
- 🔴 Logs de auditoría
- 🔴 Validación de input más estricta
- 🔴 Sanitización de output
- 🔴 CSRF protection (si hay web)

## 🚀 Escalabilidad

### Preparado para:
- ✅ Separar en microservicios por dominio
- ✅ Añadir cache (Redis)
- ✅ Añadir cola de mensajes (RabbitMQ)
- ✅ Implementar CQRS si crece
- ✅ Separar lectura/escritura en BD

### Posibles Mejoras:
- Implementar eventos de dominio
- Agregar sistema de notificaciones push
- Sistema de calificaciones (ratings)
- Chat en tiempo real
- Geolocalización
- Pasarela de pagos

## 📊 Monitoreo Recomendado

```
┌────────────────┐
│   Application  │
│   Monitoring   │
└────────┬───────┘
         │
    ┌────▼────┐
    │  Logs   │  → Errores, warnings, info
    └────┬────┘
         │
    ┌────▼────┐
    │ Metrics │  → Response time, throughput
    └────┬────┘
         │
    ┌────▼────┐
    │ Alerts  │  → Errores críticos, caídas
    └─────────┘
```

## 🧪 Testing (Futuro)

```
tests/
├── Unit/              # Tests unitarios
│   ├── Entities/
│   ├── UseCases/
│   └── Repositories/
│
├── Integration/       # Tests de integración
│   ├── Controllers/
│   └── Database/
│
└── E2E/              # Tests end-to-end
    └── Workflows/
```

## 📈 Métricas Clave

- **Complejidad Ciclomática**: Baja (código simple)
- **Cobertura de Tests**: 0% (pendiente)
- **Deuda Técnica**: Mínima
- **Acoplamiento**: Bajo (gracias a interfaces)
- **Cohesión**: Alta (SRP aplicado)

## 🎓 Patrones de Diseño Utilizados

1. **Repository Pattern** - Abstracción de persistencia
2. **Dependency Injection** - Inyección manual en constructores
3. **Factory Pattern** - Instanciación de objetos complejos
4. **Singleton** - Conexión a base de datos
5. **Strategy Pattern** - Diferentes tipos de usuarios
6. **DTO Pattern** - Transferencia de datos entre capas

---

**Autor:** SaludGo Development Team  
**Versión:** 1.0.0  
**Fecha:** Febrero 2026
