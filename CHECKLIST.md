# ✅ CHECKLIST DE VERIFICACIÓN - SaludGo Backend

Usa este checklist para asegurarte de que todo está funcionando correctamente.

## 📋 Pre-Instalación

- [ ] XAMPP está instalado
- [ ] Apache está corriendo
- [ ] MySQL está corriendo
- [ ] PHP versión >= 8.0
- [ ] La carpeta del proyecto está en `c:\xampp\htdocs\saludgoft\saludgo-backend`

## 🗄️ Base de Datos

- [ ] Abrir http://localhost:8080/phpmyadmin/
- [ ] La base de datos `saludgo` existe
- [ ] Ejecutar el script `database.sql` en la base de datos
- [ ] Verificar que se crearon las tablas:
  - [ ] `solicitudes_servicio`
  - [ ] `ofertas`
  - [ ] `servicios`
- [ ] Verificar que existen las tablas previas:
  - [ ] `users`
  - [ ] `especialidades`
  - [ ] `profesionales`

## 🔧 Configuración

- [ ] El archivo `config/database.php` tiene las credenciales correctas
- [ ] El archivo `config/app.php` existe
- [ ] El archivo `autoload.php` existe en la raíz
- [ ] El archivo `public/.htaccess` existe

## 🧪 Testing Básico

### Test 1: Endpoint público
- [ ] Abrir navegador
- [ ] Ir a: `http://localhost/saludgoft/saludgo-backend/public/api/specialties`
- [ ] Debe mostrar un JSON con especialidades

### Test 2: Registrar paciente
- [ ] Abrir Postman
- [ ] POST a `/api/register/patient` con datos válidos
- [ ] Debe devolver `token` y datos del usuario

### Test 3: Login
- [ ] POST a `/api/login` con email y password
- [ ] Debe devolver `token`

### Test 4: Endpoint protegido
- [ ] GET a `/api/me` con `Authorization: Bearer {token}`
- [ ] Debe devolver datos del usuario

## 📱 Tests de Flujo Completo

### Flujo Paciente
- [ ] Registrar paciente
- [ ] Login paciente
- [ ] Crear solicitud de servicio
- [ ] Ver mis solicitudes

### Flujo Médico  
- [ ] Registrar médico
- [ ] **IMPORTANTE:** Verificar médico en BD:
  ```sql
  UPDATE profesionales 
  SET verificado = 1, estado_verificacion = 'aprobado' 
  WHERE usuario_id = [ID];
  ```
- [ ] Login médico
- [ ] Ver solicitudes disponibles
- [ ] Enviar oferta a una solicitud

### Flujo Completo
- [ ] Paciente crea solicitud
- [ ] Médico ve la solicitud
- [ ] Médico envía oferta
- [ ] Paciente ve las ofertas
- [ ] Paciente acepta una oferta
- [ ] Se crea el servicio automáticamente
- [ ] Médico o paciente completa el servicio

## 🔍 Verificaciones de Lógica de Negocio

### Restricciones que deben funcionar:
- [ ] Un médico NO puede enviar 2 ofertas a la misma solicitud
- [ ] Un médico NO verificado NO puede ver solicitudes
- [ ] Un médico NO puede ver solicitudes fuera de su especialidad
- [ ] Un paciente solo puede aceptar ofertas de SUS solicitudes
- [ ] Al aceptar una oferta, las demás se rechazan automáticamente
- [ ] No se puede aceptar una oferta si la solicitud ya está TAKEN
- [ ] No se puede completar un servicio ajeno

## 🐛 Si algo falla...

### Error: "Ruta no encontrada"
**Solución:**
1. Verificar que Apache tenga `mod_rewrite` habilitado
2. Verificar que `.htaccess` exista en `/public`
3. Reiniciar Apache

### Error: "Error de conexión a BD"
**Solución:**
1. Verificar que MySQL esté corriendo
2. Verificar credenciales en `config/database.php`
3. Verificar que la BD `saludgo` exista

### Error: "Token inválido"
**Solución:**
1. El token expira en 24h, hacer login de nuevo
2. Verificar que estás usando `Authorization: Bearer {token}`
3. Copiar el token completo sin espacios

### Error: "Médico no puede ver solicitudes"
**Solución:**
1. Verificar que el médico esté verificado en la BD
2. Ejecutar:
   ```sql
   UPDATE profesionales 
   SET verificado = 1, estado_verificacion = 'aprobado' 
   WHERE usuario_id = [ID];
   ```

### Error 500
**Solución:**
1. Activar debug: `config/app.php` → `'debug' => true`
2. Ver el mensaje de error completo
3. Verificar logs de PHP en XAMPP

## 📊 Herramientas de Testing

- [ ] Importar `SaludGo_Postman_Collection.json` en Postman
- [ ] Configurar variable `base_url` en Postman
- [ ] Configurar variable `token` después del login
- [ ] Usar `API_TESTS.sh` para tests con cURL (si usas Git Bash)

## 🎉 Todo Funciona

Si todos los checks están marcados, ¡tu backend está 100% funcional!

### URLs importantes:
- **API Base:** http://localhost/saludgoft/saludgo-backend/public/api
- **phpMyAdmin:** http://localhost:8080/phpmyadmin/
- **Documentación:** README.md
- **Arquitectura:** ARQUITECTURA.md
- **Instalación:** INSTALACION.md

### Próximos pasos:
1. ✅ Conectar desde Flutter
2. ✅ Implementar notificaciones push
3. ✅ Añadir sistema de ratings
4. ✅ Implementar chat en tiempo real
5. ✅ Agregar geolocalización

---

**¿Algo no funciona?** Revisa los archivos:
- `INSTALACION.md` para setup inicial
- `ARQUITECTURA.md` para entender el código
- `README.md` para documentación de endpoints

**¡Éxito con tu proyecto SaludGo! 🚀**
