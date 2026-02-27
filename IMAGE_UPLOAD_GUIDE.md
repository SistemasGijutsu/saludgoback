# Guía de Manejo de Imágenes - SaludGo Backend

## Resumen de Cambios

Se ha implementado completamente el sistema de manejo de imágenes para fotos de perfil de usuarios. Ahora las imágenes **SÍ se guardan correctamente** en el servidor.

## ✅ Funcionalidades Implementadas

### 1. **Helpers para Upload de Imágenes**
- `uploadImage()` - Sube y valida imágenes
- `deleteImage()` - Elimina imágenes del servidor
- `getImageUrl()` - Obtiene la URL completa de una imagen

### 2. **Directorio de Almacenamiento**
- Las imágenes se guardan en: `uploads/profiles/`
- Se crean automáticamente si no existen
- Nombres únicos generados automáticamente

### 3. **Validaciones**
- ✅ Tamaño máximo: 5MB
- ✅ Formatos permitidos: JPG, PNG, WEBP
- ✅ Validación de tipo MIME real (no solo extensión)

### 4. **Endpoints Actualizados**

#### `POST /api/register/patient`
**Ahora acepta multipart/form-data con imagen**

```bash
# Desde Postman o similar
POST http://localhost:8000/api/register/patient
Content-Type: multipart/form-data

Campos:
- nombre: "Juan Pérez"
- email: "juan@example.com"
- password: "123456"
- foto_perfil: [archivo de imagen]
```

#### `POST /api/register/doctor`
**Ahora acepta multipart/form-data con imagen**

```bash
POST http://localhost:8000/api/register/doctor
Content-Type: multipart/form-data

Campos:
- nombre: "Dr. María García"
- email: "maria@example.com"
- password: "123456"
- especialidad_id: 1
- cedula: "123456789"
- tarjeta_profesional: "987654321"
- medio_transporte: "automovil"
- anos_experiencia: 10
- tarifa_consulta: 50000
- descripcion: "Médico especialista..."
- foto_perfil: [archivo de imagen]
```

#### `POST /api/me/photo` ⭐ NUEVO
**Actualizar foto de perfil (requiere autenticación)**

```bash
POST http://localhost:8000/api/me/photo
Authorization: Bearer {token}
Content-Type: multipart/form-data

Campos:
- foto_perfil: [archivo de imagen]
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Foto de perfil actualizada",
  "foto_perfil": "uploads/profiles/img_abc123_1234567890.jpg",
  "foto_url": "http://localhost:8000/uploads/profiles/img_abc123_1234567890.jpg"
}
```

## 📱 Integración con Flutter

### Ejemplo usando `http` y `http_parser`

```dart
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'dart:io';

Future<Map<String, dynamic>> registerDoctorWithPhoto({
  required String nombre,
  required String email,
  required String password,
  File? fotoPerfil,
  // ... otros campos
}) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('http://localhost:8000/api/register/doctor'),
  );

  // Campos de texto
  request.fields['nombre'] = nombre;
  request.fields['email'] = email;
  request.fields['password'] = password;
  // ... otros campos

  // Foto de perfil (opcional)
  if (fotoPerfil != null) {
    var stream = http.ByteStream(fotoPerfil.openRead());
    var length = await fotoPerfil.length();
    
    var multipartFile = http.MultipartFile(
      'foto_perfil',
      stream,
      length,
      filename: fotoPerfil.path.split('/').last,
      contentType: MediaType('image', 'jpeg'),
    );
    
    request.files.add(multipartFile);
  }

  // Enviar request
  var response = await request.send();
  var responseData = await response.stream.bytesToString();
  
  if (response.statusCode == 201) {
    return jsonDecode(responseData);
  } else {
    throw Exception('Error al registrar');
  }
}
```

### Ejemplo para actualizar foto de perfil

```dart
Future<Map<String, dynamic>> updateProfilePhoto(File image, String token) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('http://localhost:8000/api/me/photo'),
  );

  // Agregar token de autorización
  request.headers['Authorization'] = 'Bearer $token';

  // Agregar imagen
  var stream = http.ByteStream(image.openRead());
  var length = await image.length();
  
  var multipartFile = http.MultipartFile(
    'foto_perfil',
    stream,
    length,
    filename: image.path.split('/').last,
    contentType: MediaType('image', 'jpeg'),
  );
  
  request.files.add(multipartFile);

  // Enviar
  var response = await request.send();
  var responseData = await response.stream.bytesToString();
  
  return jsonDecode(responseData);
}
```

### Ejemplo usando `image_picker`

```dart
import 'package:image_picker/image_picker.dart';

Future<File?> pickImageFromGallery() async {
  final ImagePicker picker = ImagePicker();
  final XFile? image = await picker.pickImage(
    source: ImageSource.gallery,
    maxWidth: 1024,
    maxHeight: 1024,
    imageQuality: 85,
  );
  
  if (image != null) {
    return File(image.path);
  }
  return null;
}

Future<File?> pickImageFromCamera() async {
  final ImagePicker picker = ImagePicker();
  final XFile? image = await picker.pickImage(
    source: ImageSource.camera,
    maxWidth: 1024,
    maxHeight: 1024,
    imageQuality: 85,
  );
  
  if (image != null) {
    return File(image.path);
  }
  return null;
}
```

## 🔧 Configuración

### Variables de Entorno (opcional)
En `config/app.php` puedes configurar:

```php
return [
    'url' => env('APP_URL', 'http://localhost:8000'),
    // ...
];
```

### Servidor de Desarrollo PHP
```bash
cd public
php -S localhost:8000
```

### XAMPP
Las imágenes se sirven automáticamente desde:
```
http://localhost/saludgo-backend/uploads/profiles/imagen.jpg
```

## 📝 Notas Importantes

1. **El directorio `uploads/` debe tener permisos de escritura**
   ```bash
   chmod -R 755 uploads/
   ```

2. **Compatibilidad con JSON**
   - Si NO envías imagen, puedes seguir usando JSON puro
   - El backend detecta automáticamente el Content-Type

3. **Respuestas del API**
   - Al registrarse o actualizar foto, recibes la ruta: `"foto_perfil": "uploads/profiles/..."`
   - Para mostrar la imagen, usa: `http://localhost:8000/uploads/profiles/...`

4. **Manejo de errores**
   - Imagen muy grande: "La imagen es demasiado grande. Máximo 5MB"
   - Formato inválido: "Tipo de archivo no válido. Solo JPG, PNG y WEBP"
   - Sin imagen: "No se recibió ninguna imagen"

## ✅ Testing

### Probar con cURL:

```bash
# Registrar doctor con foto
curl -X POST http://localhost:8000/api/register/doctor \
  -F "nombre=Dr. Test" \
  -F "email=test@test.com" \
  -F "password=123456" \
  -F "especialidad_id=1" \
  -F "foto_perfil=@/ruta/a/imagen.jpg"

# Actualizar foto de perfil
curl -X POST http://localhost:8000/api/me/photo \
  -H "Authorization: Bearer {tu_token}" \
  -F "foto_perfil=@/ruta/a/nueva_imagen.jpg"
```

## 🎉 Resultado

Ahora cuando subas imágenes desde el emulador o dispositivo real:
- ✅ Se validan correctamente
- ✅ Se guardan en el servidor
- ✅ Se almacena la ruta en la base de datos
- ✅ Puedes acceder a ellas vía URL
- ✅ Se eliminan al actualizar (no quedan huérfanas)
