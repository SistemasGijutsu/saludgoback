# 📖 GUÍA RÁPIDA DE REFERENCIA - API SaludGo

## 🌐 Base URL
```
http://localhost/saludgoft/saludgo-backend/public/api
```

---

## 🔑 AUTENTICACIÓN

### Registrar Paciente
```http
POST /register/patient
Content-Type: application/json

{
  "nombre": "string",
  "email": "string",
  "password": "string"
}

Response 201:
{
  "user": { ... },
  "token": "string"
}
```

### Registrar Médico
```http
POST /register/doctor
Content-Type: application/json

{
  "nombre": "string",
  "email": "string",
  "password": "string",
  "especialidad_id": number,
  "cedula": "string",
  "tarjeta_profesional": "string",
  "medio_transporte": "string",
  "anos_experiencia": number,
  "tarifa_consulta": number,
  "descripcion": "string"
}

Response 201:
{
  "user": { ... },
  "doctor_profile": { ... },
  "token": "string"
}
```

### Login
```http
POST /login
Content-Type: application/json

{
  "email": "string",
  "password": "string"
}

Response 200:
{
  "user": { ... },
  "token": "string"
}
```

### Mi Perfil
```http
GET /me
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": {
    "id": number,
    "nombre": "string",
    "email": "string",
    "rol": "string",
    ...
  }
}
```

---

## 🏥 ESPECIALIDADES

### Listar Especialidades
```http
GET /specialties

Response 200:
{
  "success": true,
  "data": [
    {
      "id": number,
      "nombre": "string",
      "descripcion": "string",
      "activo": number
    }
  ]
}
```

---

## 👤 PACIENTE

### Crear Solicitud de Servicio
```http
POST /service-requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "especialidad_id": number,
  "descripcion": "string"
}

Response 201:
{
  "success": true,
  "message": "Solicitud creada exitosamente",
  "data": {
    "id": number,
    "paciente_id": number,
    "especialidad_id": number,
    "descripcion": "string",
    "status": "OPEN",
    "created_at": "datetime"
  }
}
```

### Ver Mis Solicitudes
```http
GET /service-requests/my
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "id": number,
      "especialidad_id": number,
      "descripcion": "string",
      "status": "OPEN|TAKEN|COMPLETED|CANCELLED",
      ...
    }
  ]
}
```

### Ver Ofertas de una Solicitud
```http
GET /service-requests/{id}/offers
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": {
    "request": { ... },
    "offers": [
      {
        "offer": {
          "id": number,
          "doctor_id": number,
          "price": number,
          "message": "string",
          "status": "PENDING|ACCEPTED|REJECTED",
          "created_at": "datetime"
        },
        "doctor_info": {
          "nombre": "string",
          "anos_experiencia": number,
          "tarifa_consulta": number
        }
      }
    ]
  }
}
```

### Aceptar una Oferta
```http
POST /offers/{id}/accept
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "message": "Oferta aceptada exitosamente",
  "data": {
    "service": { ... },
    "offer": { ... }
  }
}
```

---

## 🧑‍⚕️ MÉDICO

### Ver Solicitudes Disponibles
```http
GET /service-requests/available
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "id": number,
      "paciente_id": number,
      "especialidad_id": number,
      "descripcion": "string",
      "status": "OPEN",
      "created_at": "datetime"
    }
  ]
}
```

### Enviar Oferta
```http
POST /service-requests/{id}/offer
Authorization: Bearer {token}
Content-Type: application/json

{
  "price": number,
  "message": "string (opcional)"
}

Response 201:
{
  "success": true,
  "message": "Oferta enviada exitosamente",
  "data": {
    "id": number,
    "service_request_id": number,
    "doctor_id": number,
    "price": number,
    "message": "string",
    "status": "PENDING",
    "created_at": "datetime"
  }
}
```

### Ver Mis Ofertas
```http
GET /offers/my
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "id": number,
      "service_request_id": number,
      "price": number,
      "message": "string",
      "status": "PENDING|ACCEPTED|REJECTED",
      ...
    }
  ]
}
```

---

## 🔄 SERVICIOS (Paciente y Médico)

### Ver Mis Servicios
```http
GET /services/my
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "id": number,
      "service_request_id": number,
      "doctor_id": number,
      "paciente_id": number,
      "final_price": number,
      "started_at": "datetime",
      "completed_at": "datetime",
      "status": "IN_PROGRESS|COMPLETED|CANCELLED"
    }
  ]
}
```

### Completar Servicio
```http
POST /services/{id}/complete
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "message": "Servicio completado exitosamente",
  "data": {
    "id": number,
    "status": "COMPLETED",
    "completed_at": "datetime",
    ...
  }
}
```

---

## 🚨 CÓDIGOS DE ERROR

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - Token inválido/expirado |
| 403 | Forbidden - Sin permisos |
| 404 | Not Found - Recurso no encontrado |
| 500 | Internal Server Error - Error del servidor |

---

## 🔐 AUTENTICACIÓN

Todas las rutas protegidas requieren el header:
```
Authorization: Bearer {token}
```

El token expira en **24 horas**.

---

## 📊 ESTADOS

### Estado de Solicitud:
- `OPEN` - Abierta, esperando ofertas
- `TAKEN` - Oferta aceptada, servicio creado
- `COMPLETED` - Servicio completado
- `CANCELLED` - Cancelada

### Estado de Oferta:
- `PENDING` - Esperando respuesta del paciente
- `ACCEPTED` - Aceptada por el paciente
- `REJECTED` - Rechazada (manual o automáticamente)

### Estado de Servicio:
- `IN_PROGRESS` - En curso
- `COMPLETED` - Finalizado
- `CANCELLED` - Cancelado

---

## 💡 TIPS

1. **Siempre guardar el token** después del login
2. **Verificar médicos manualmente** en la BD para que puedan operar
3. **Un médico solo puede enviar UNA oferta por solicitud**
4. **Al aceptar una oferta, las demás se rechazan automáticamente**
5. **Solo el dueño puede aceptar/completar sus recursos**

---

## 📝 EJEMPLO DE FLUJO COMPLETO

```bash
# 1. Registrar paciente
POST /register/patient
→ Guardar token_paciente

# 2. Registrar médico
POST /register/doctor
→ Guardar token_medico
→ Verificar en BD

# 3. Paciente crea solicitud
POST /service-requests (con token_paciente)
→ Guardar request_id

# 4. Médico ve solicitudes
GET /service-requests/available (con token_medico)

# 5. Médico envía oferta
POST /service-requests/{request_id}/offer (con token_medico)
→ Guardar offer_id

# 6. Paciente ve ofertas
GET /service-requests/{request_id}/offers (con token_paciente)

# 7. Paciente acepta oferta
POST /offers/{offer_id}/accept (con token_paciente)
→ Se crea servicio automáticamente, guardar service_id

# 8. Completar servicio
POST /services/{service_id}/complete (con cualquier token)
```

---

**¿Dudas?** Consulta el README.md completo o ARQUITECTURA.md
