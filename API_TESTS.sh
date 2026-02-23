# =====================================================
# SALUDGO API - Pruebas con cURL
# =====================================================
# Base URL: http://localhost/saludgoft/saludgo-backend/public/api
# =====================================================

# ===========================================
# 1. REGISTRAR PACIENTE
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/register/patient \
  -H "Content-Type: application/json" \
  -d "{\"nombre\":\"Juan Pérez\",\"email\":\"juan@test.com\",\"password\":\"123456\"}"

# ===========================================
# 2. REGISTRAR MÉDICO
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/register/doctor \
  -H "Content-Type: application/json" \
  -d "{\"nombre\":\"Dra. María García\",\"email\":\"maria@test.com\",\"password\":\"123456\",\"especialidad_id\":1,\"cedula\":\"123456789\",\"tarjeta_profesional\":\"TP123\",\"medio_transporte\":\"motocicleta\",\"anos_experiencia\":5,\"tarifa_consulta\":50000,\"descripcion\":\"Médico general\"}"

# ===========================================
# 3. LOGIN PACIENTE
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"juan@test.com\",\"password\":\"123456\"}"

# Guardar el token que devuelve en una variable:
# TOKEN_PACIENTE="eyJ0eXAiOiJKV1QiLCJhbGc..."

# ===========================================
# 4. LOGIN MÉDICO
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"maria@test.com\",\"password\":\"123456\"}"

# Guardar el token:
# TOKEN_DOCTOR="eyJ0eXAiOiJKV1QiLCJhbGc..."

# ===========================================
# 5. VER MI PERFIL
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/me \
  -H "Authorization: Bearer $TOKEN_PACIENTE"

# ===========================================
# 6. LISTAR ESPECIALIDADES
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/specialties

# ===========================================
# 7. CREAR SOLICITUD DE SERVICIO (Paciente)
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/service-requests \
  -H "Authorization: Bearer $TOKEN_PACIENTE" \
  -H "Content-Type: application/json" \
  -d "{\"especialidad_id\":1,\"descripcion\":\"Necesito consulta médica urgente en mi domicilio\"}"

# Guardar el ID de la solicitud:
# REQUEST_ID=1

# ===========================================
# 8. VER MIS SOLICITUDES (Paciente)
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/service-requests/my \
  -H "Authorization: Bearer $TOKEN_PACIENTE"

# ===========================================
# 9. VER SOLICITUDES DISPONIBLES (Médico)
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/service-requests/available \
  -H "Authorization: Bearer $TOKEN_DOCTOR"

# ===========================================
# 10. ENVIAR OFERTA (Médico)
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/service-requests/$REQUEST_ID/offer \
  -H "Authorization: Bearer $TOKEN_DOCTOR" \
  -H "Content-Type: application/json" \
  -d "{\"price\":50000,\"message\":\"Puedo atenderte en 30 minutos\"}"

# Guardar el ID de la oferta:
# OFFER_ID=1

# ===========================================
# 11. VER OFERTAS DE UNA SOLICITUD (Paciente)
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/service-requests/$REQUEST_ID/offers \
  -H "Authorization: Bearer $TOKEN_PACIENTE"

# ===========================================
# 12. ACEPTAR OFERTA (Paciente)
# ===========================================
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/offers/$OFFER_ID/accept \
  -H "Authorization: Bearer $TOKEN_PACIENTE"

# Esto crea automáticamente el servicio
# Guardar el ID del servicio:
# SERVICE_ID=1

# ===========================================
# 13. VER MIS SERVICIOS
# ===========================================
# Como paciente:
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/services/my \
  -H "Authorization: Bearer $TOKEN_PACIENTE"

# Como médico:
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/services/my \
  -H "Authorization: Bearer $TOKEN_DOCTOR"

# ===========================================
# 14. COMPLETAR SERVICIO
# ===========================================
# El médico o paciente pueden completarlo:
curl -X POST http://localhost/saludgoft/saludgo-backend/public/api/services/$SERVICE_ID/complete \
  -H "Authorization: Bearer $TOKEN_DOCTOR"

# ===========================================
# 15. VER MIS OFERTAS (Médico)
# ===========================================
curl -X GET http://localhost/saludgoft/saludgo-backend/public/api/offers/my \
  -H "Authorization: Bearer $TOKEN_DOCTOR"
