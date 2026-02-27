# 📱 Guía: Registro de Profesionales desde Android

## ✅ Estado: COMPLETAMENTE IMPLEMENTADO

El backend ahora soporta **MÚLTIPLES archivos** en el registro de profesionales.

---

## 📤 Archivos que puedes enviar

### Obligatorio
- **foto_perfil** - Se guarda en `usuarios.foto_perfil`

### Documentos del Profesional (Opcionales)
- **foto_documento_identidad** - Foto del documento de identidad
- **foto_tarjeta_profesional** - Foto de la tarjeta profesional
- **selfie_con_tarjeta** - Selfie sosteniendo la tarjeta
- **documento_adicional_1** - Documento extra 1
- **documento_adicional_2** - Documento extra 2
- **documento_adicional_3** - Documento extra 3

---

## 🔧 Endpoint

```
POST http://localhost:8080/saludgoft/saludgo-backend/public/api/register/doctor
Content-Type: multipart/form-data
```

---

## 📝 Campos del Formulario

### Datos Básicos (texto)
- **nombre** - Nombre completo
- **email** - Correo electrónico
- **password** - Contraseña
- **cedula** - Número de cédula
- **especialidad_id** - ID de especialidad (1, 2, 3...)
- **tarjeta_profesional** - Número de tarjeta profesional
- **medio_transporte** - motocicleta | automovil | ninguno
- **anos_experiencia** - Años de experiencia
- **tarifa_consulta** - Tarifa por consulta
- **descripcion** - Descripción del profesional

### Archivos (multipart)
- **foto_perfil** - Imagen de perfil (opcional)
- **foto_documento_identidad** - Foto documento ID (opcional)
- **foto_tarjeta_profesional** - Foto tarjeta prof (opcional)
- **selfie_con_tarjeta** - Selfie con tarjeta (opcional)
- **documento_adicional_1** - Extra 1 (opcional)
- **documento_adicional_2** - Extra 2 (opcional)
- **documento_adicional_3** - Extra 3 (opcional)

---

## 📱 Código Flutter/Dart para Android

```dart
import 'dart:io';
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> registerDoctor({
  required String nombre,
  required String email,
  required String password,
  required String cedula,
  required int especialidadId,
  required String tarjetaProfesional,
  File? fotoPerfil,
  File? fotoDocumentoIdentidad,
  File? fotoTarjetaProfesional,
  File? selfieConTarjeta,
  File? documentoAdicional1,
  File? documentoAdicional2,
  File? documentoAdicional3,
  String? medioTransporte,
  int? anosExperiencia,
  double? tarifaConsulta,
  String? descripcion,
}) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('http://10.0.2.2:8080/saludgoft/saludgo-backend/public/api/register/doctor'),
  );

  // Campos de texto obligatorios
  request.fields['nombre'] = nombre;
  request.fields['email'] = email;
  request.fields['password'] = password;
  request.fields['cedula'] = cedula;
  request.fields['especialidad_id'] = especialidadId.toString();
  request.fields['tarjeta_profesional'] = tarjetaProfesional;

  // Campos opcionales
  if (medioTransporte != null) request.fields['medio_transporte'] = medioTransporte;
  if (anosExperiencia != null) request.fields['anos_experiencia'] = anosExperiencia.toString();
  if (tarifaConsulta != null) request.fields['tarifa_consulta'] = tarifaConsulta.toString();
  if (descripcion != null) request.fields['descripcion'] = descripcion;

  // Agregar foto de perfil si existe
  if (fotoPerfil != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'foto_perfil',
      fotoPerfil.path,
    ));
  }

  // Agregar documentos si existen
  if (fotoDocumentoIdentidad != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'foto_documento_identidad',
      fotoDocumentoIdentidad.path,
    ));
  }

  if (fotoTarjetaProfesional != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'foto_tarjeta_profesional',
      fotoTarjetaProfesional.path,
    ));
  }

  if (selfieConTarjeta != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'selfie_con_tarjeta',
      selfieConTarjeta.path,
    ));
  }

  if (documentoAdicional1 != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'documento_adicional_1',
      documentoAdicional1.path,
    ));
  }

  if (documentoAdicional2 != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'documento_adicional_2',
      documentoAdicional2.path,
    ));
  }

  if (documentoAdicional3 != null) {
    request.files.add(await http.MultipartFile.fromPath(
      'documento_adicional_3',
      documentoAdicional3.path,
    ));
  }

  // Enviar la petición
  var streamedResponse = await request.send();
  var response = await http.Response.fromStream(streamedResponse);

  if (response.statusCode == 201) {
    return jsonDecode(response.body);
  } else {
    throw Exception('Error al registrar: ${response.body}');
  }
}
```

---

## 🎯 Ejemplo de Uso

```dart
try {
  // Seleccionar imágenes (ejemplo con image_picker)
  final ImagePicker picker = ImagePicker();
  
  final XFile? fotoPerfil = await picker.pickImage(source: ImageSource.gallery);
  final XFile? fotoDoc = await picker.pickImage(source: ImageSource.gallery);
  final XFile? fotoTarjeta = await picker.pickImage(source: ImageSource.gallery);
  final XFile? selfie = await picker.pickImage(source: ImageSource.gallery);

  // Registrar doctor
  final result = await registerDoctor(
    nombre: 'Dr. Juan Pérez',
    email: 'juan@example.com',
    password: '123456',
    cedula: '1234567890',
    especialidadId: 1,
    tarjetaProfesional: '987654321',
    fotoPerfil: fotoPerfil != null ? File(fotoPerfil.path) : null,
    fotoDocumentoIdentidad: fotoDoc != null ? File(fotoDoc.path) : null,
    fotoTarjetaProfesional: fotoTarjeta != null ? File(fotoTarjeta.path) : null,
    selfieConTarjeta: selfie != null ? File(selfie.path) : null,
    medioTransporte: 'automovil',
    anosExperiencia: 5,
    tarifaConsulta: 50000,
    descripcion: 'Médico especialista con 5 años de experiencia',
  );

  print('Usuario registrado: ${result['user']['id']}');
  print('Token: ${result['token']}');
  print('Documentos subidos: ${result['documentos_subidos']}');
  
} catch (e) {
  print('Error: $e');
}
```

---

## 📦 Respuesta del API

```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 25,
    "nombre": "Dr. Juan Pérez",
    "email": "juan@example.com",
    "rol": "profesional",
    "foto_perfil": "uploads/profiles/img_abc123_1234567890.jpg"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "doctor_profile": {
    "id": 15,
    "usuario_id": 25,
    "especialidad_id": 1,
    "cedula": "1234567890",
    "tarjeta_profesional": "987654321",
    "foto_documento_identidad": "uploads/documentos/img_xyz789_1234567891.jpg",
    "foto_tarjeta_profesional": "uploads/documentos/img_def456_1234567892.jpg",
    "selfie_con_tarjeta": "uploads/documentos/img_ghi789_1234567893.jpg",
    "documento_adicional_1": null,
    "documento_adicional_2": null,
    "documento_adicional_3": null,
    "verificado": 0,
    "estado_verificacion": "pendiente"
  },
  "documentos_subidos": {
    "foto_documento_identidad": "uploads/documentos/img_xyz789_1234567891.jpg",
    "foto_tarjeta_profesional": "uploads/documentos/img_def456_1234567892.jpg",
    "selfie_con_tarjeta": "uploads/documentos/img_ghi789_1234567893.jpg"
  }
}
```

---

## 🖼️ URLs para Mostrar Imágenes

```dart
String baseUrl = 'http://10.0.2.2:8080/saludgoft/saludgo-backend';

// Foto de perfil
String fotoPerfilUrl = '$baseUrl/${user['foto_perfil']}';

// Documentos
String docIdentidadUrl = '$baseUrl/${doctorProfile['foto_documento_identidad']}';
String tarjetaUrl = '$baseUrl/${doctorProfile['foto_tarjeta_profesional']}';
String selfieUrl = '$baseUrl/${doctorProfile['selfie_con_tarjeta']}';

// Mostrar en Image.network()
Image.network(fotoPerfilUrl)
```

---

## ⚙️ Configuración Android

### AndroidManifest.xml
```xml
<uses-permission android:name="android.permission.INTERNET"/>
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE"/>
<uses-permission android:name="android.permission.CAMERA"/>
```

### pubspec.yaml
```yaml
dependencies:
  http: ^1.1.0
  image_picker: ^1.0.4
```

---

## 🔍 Validaciones en el Backend

- **Tamaño máximo:** 5MB por archivo
- **Formatos permitidos:** JPG, PNG, WEBP
- **Validación MIME:** Verificación del tipo real del archivo
- **Nombres únicos:** Se generan automáticamente con timestamp

---

## ✅ Checklist para Android

- [ ] Agregar permisos en AndroidManifest.xml
- [ ] Instalar paquetes `http` e `image_picker`
- [ ] Cambiar URL de `localhost` a `10.0.2.2` (emulador Android)
- [ ] Si es dispositivo físico, usar IP de tu PC (ej: `192.168.1.100`)
- [ ] Enviar archivos con nombres de campo exactos
- [ ] Manejar errores de conexión
- [ ] Mostrar imágenes con URL completa

---

## 🐛 Troubleshooting

### Error: "Network error"
- Verifica que XAMPP esté corriendo en puerto 8080
- En emulador usa `10.0.2.2` en lugar de `localhost`
- En dispositivo físico, asegúrate de estar en la misma red WiFi

### Imágenes no se muestran
- Verifica que la URL incluya la ruta base completa
- No incluyas `/public/` en la URL de imágenes
- Ejemplo correcto: `http://10.0.2.2:8080/saludgoft/saludgo-backend/uploads/...`

### Archivo muy grande
- Comprime las imágenes antes de enviar
- Usa calidad media (70-80%)
- Considera redimensionar a máximo 1920x1080

---

## 📞 Soporte

Si algo no funciona:
1. Revisa los errores en la respuesta JSON
2. Verifica que los nombres de campos sean exactos
3. Confirma que XAMPP esté corriendo
4. Prueba primero con la página de prueba HTML
