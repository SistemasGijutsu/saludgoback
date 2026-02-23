-- =====================================================
-- DATOS DE PRUEBA PARA PACIENTES
-- =====================================================
-- Ejecutar DESPUÉS de database.sql
-- =====================================================

USE saludgo;

-- Insertar usuarios de prueba tipo paciente (si no existen)
INSERT IGNORE INTO usuarios (nombre, email, password, rol, telefono, fecha_nacimiento, genero, ciudad, direccion, activo, fecha_registro) VALUES
('María González', 'paciente1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paciente', '+57 300 111 1111', '1985-03-15', 'femenino', 'Bogotá', 'Calle 100 #15-20', 1, NOW()),
('Carlos Ramírez', 'paciente2@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paciente', '+57 300 222 2222', '1990-07-22', 'masculino', 'Medellín', 'Carrera 70 #45-80', 1, NOW()),
('Laura Martínez', 'paciente3@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paciente', '+57 300 333 3333', '1995-12-10', 'femenino', 'Cali', 'Avenida 6 Norte #25-30', 1, NOW());

-- Password para todos: password

-- Obtener IDs de los usuarios recién creados
SET @user_id_1 = (SELECT id FROM usuarios WHERE email = 'paciente1@test.com');
SET @user_id_2 = (SELECT id FROM usuarios WHERE email = 'paciente2@test.com');
SET @user_id_3 = (SELECT id FROM usuarios WHERE email = 'paciente3@test.com');

-- Insertar información médica adicional de pacientes
INSERT IGNORE INTO pacientes (
    usuario_id, contacto_emergencia_nombre, contacto_emergencia_telefono,
    tipo_sangre, alergias, condiciones_cronicas, notas_medicas
) VALUES
(
    @user_id_1,
    'Pedro González',
    '+57 300 444 4444',
    'O+',
    'Ninguna conocida',
    'Ninguna',
    'Paciente sana, sin antecedentes'
),
(
    @user_id_2,
    'Ana Ramírez',
    '+57 300 555 5555',
    'A+',
    'Penicilina',
    'Hipertensión leve',
    'En tratamiento con enalapril 10mg/día'
),
(
    @user_id_3,
    'José Martínez',
    '+57 300 666 6666',
    'B+',
    'Polen, ácaros del polvo',
    'Asma leve',
    'Usa inhalador de rescate según necesidad'
);

-- Verificar datos insertados
SELECT 
    u.id as user_id,
    u.nombre,
    u.email,
    u.ciudad,
    u.telefono,
    p.tipo_sangre,
    p.alergias,
    p.condiciones_cronicas
FROM usuarios u
LEFT JOIN pacientes p ON u.id = p.usuario_id
WHERE u.rol = 'paciente'
ORDER BY u.id;

SELECT 'Pacientes de prueba creados exitosamente!' as mensaje;
