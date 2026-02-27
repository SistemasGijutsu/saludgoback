# 🔍 Solución de Problemas - Uploads No se Guardan

## Cambios Realizados

He identificado y corregido varios problemas con el sistema de uploads:

### 1. ✅ Directorio Creado
- Se creó el directorio `uploads/profiles/`
- Se agregó `.htaccess` para permitir acceso a las imágenes

### 2. ✅ Código Mejorado con Debug
- Ahora el registro **NO se detiene** si falla la imagen
- Se agrega información de debug detallada en la respuesta
- Mensajes de error descriptivos para cada tipo de problema

### 3. ✅ Herramienta de Diagnóstico
- Creada: `test_debug_upload.html` - Página web para probar y diagnosticar

## 🧪 CÓMO PROBAR AHORA

### Opción 1: Página de Prueba (RECOMENDADO)

1. **Abre en tu navegador:**
   ```
   http://localhost:8080/saludgoft/saludgo-backend/test_debug_upload.html
   ```

2. **Completa el formulario:**
   - Nombre, email, contraseña (ya prellenados)
   - **IMPORTANTE:** Selecciona una foto de perfil

3. **Haz clic en "Registrar y Diagnosticar"**

4. **Revisa el resultado:** te mostrará:
   - ✅ Si se guardó o no la foto
   - 🔍 Información detallada de debug
   - ❌ Errores específicos si algo falló
   - 📦 Respuesta completa del servidor

### Opción 2: Desde Flutter/Postman

**La respuesta ahora incluye `upload_debug`:**

```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 123,
    "nombre": "Dr. Test",
    "email": "test@test.com",
    "foto_perfil": "uploads/profiles/img_abc123_1234567890.jpg" // ← O NULL si falló
  },
  "upload_debug": {  // ← NUEVA INFO DE DEBUG
    "files_exists": true,
    "files_count": 1,
    "content_type": "multipart/form-data; boundary=...",
    "file_error": 0,
    "file_size": 245678,
    "file_name": "foto.jpg",
    "upload_success": true,
    "saved_path": "uploads/profiles/img_abc123_1234567890.jpg"
  }
}
```

## 🔍 Posibles Problemas y Soluciones

### Problema 1: `files_exists: false`
**Causa:** El campo no se llama "foto_perfil" en el request
**Solución:** Asegúrate que el campo se llame exactamente `foto_perfil`

### Problema 2: `file_error: 1` (UPLOAD_ERR_INI_SIZE)
**Causa:** Archivo muy grande para PHP
**Solución:** Edita `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Problema 3: `file_error: 4` (UPLOAD_ERR_NO_FILE)
**Causa:** No se seleccionó archivo
**Solución:** Asegúrate de seleccionar un archivo antes de enviar

### Problema 4: `upload_error: "Error al guardar la imagen"`
**Causa:** Permisos de escritura en el directorio
**Solución:** 
```bash
chmod -R 755 uploads/
```

### Problema 5: Foto es NULL pero no hay `upload_debug`
**Causa:** No se envió ninguna imagen
**Solución:** Este es el comportamiento esperado - la foto es opcional

## 📱 Configuración en Flutter

### Verificar que envías multipart/form-data:

```dart
var request = http.MultipartRequest(
  'POST',
  Uri.parse('http://tu-servidor/api/register/doctor'),
);

// IMPORTANTE: El campo debe llamarse 'foto_perfil'
request.files.add(await http.MultipartFile.fromPath(
  'foto_perfil',  // ← Nombre exacto
  imagePath,
));

// Campos de texto
request.fields['nombre'] = 'Dr. Test';
request.fields['email'] = 'test@test.com';
request.fields['password'] = '123456';
// ... otros campos

var response = await request.send();
var responseBody = await response.stream.bytesToString();
var data = jsonDecode(responseBody);

// Revisar debug info
if (data['upload_debug'] != null) {
  print('Debug Upload: ${data['upload_debug']}');
}

// Verificar si se guardó
if (data['user']['foto_perfil'] != null) {
  print('✅ Foto guardada: ${data['user']['foto_perfil']}');
} else {
  print('❌ Foto NO guardada');
}
```

## 🛠️ Verificar Configuración PHP

### Ver configuración actual:
```bash
php -i | grep upload
```

### Verificar que funcione:
```bash
cd c:\xampp\htdocs\saludgoft\saludgo-backend
php -S localhost:8000 -t public
```

## ✅ Checklist de Verificación

Antes de volver a probar desde Flutter, verifica:

- [ ] El directorio `uploads/profiles/` existe
- [ ] Tienes permisos de escritura en `uploads/`
- [ ] PHP tiene `upload_max_filesize` >= 5M
- [ ] El servidor está corriendo
- [ ] Puedes acceder a: `http://localhost/saludgo-backend/test_debug_upload.html`
- [ ] La prueba con la página HTML funciona correctamente

## 📞 Siguiente Paso

1. **Primero prueba con la página HTML** (`test_debug_upload.html`)
2. **Si funciona:** El problema está en tu código Flutter
3. **Si NO funciona:** Revisa la información de debug que muestra

**Envíame el resultado de `upload_debug` para ayudarte mejor.**
