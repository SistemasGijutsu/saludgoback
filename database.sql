-- =====================================================
-- SALUDGO - Script de creación de tablas faltantes
-- =====================================================
-- Este script asume que ya existen las tablas:
-- - users
-- - especialidades
-- - profesionales
-- =====================================================

USE saludgo;

-- =====================================================
-- TABLA: pacientes (Información médica adicional)
-- =====================================================
-- NOTA: Los pacientes son usuarios con rol='paciente'
-- Esta tabla solo guarda información MÉDICA adicional
-- Los datos personales (nombre, teléfono, etc.) están en 'usuarios'
-- =====================================================
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
    
    INDEX idx_usuario_id (usuario_id),
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: solicitudes_servicio
-- =====================================================
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
    INDEX idx_status (status),
    
    FOREIGN KEY (paciente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: ofertas
-- =====================================================
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
    INDEX idx_status (status),
    
    FOREIGN KEY (service_request_id) REFERENCES solicitudes_servicio(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    
    -- Restricción: Un doctor no puede hacer más de una oferta por solicitud
    UNIQUE KEY unique_doctor_offer (doctor_id, service_request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: servicios
-- =====================================================
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
    INDEX idx_status (status),
    
    FOREIGN KEY (service_request_id) REFERENCES solicitudes_servicio(id) ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (paciente_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE PRUEBA (OPCIONAL)
-- =====================================================

-- Insertar especialidades de ejemplo si la tabla está vacía
INSERT IGNORE INTO especialidades (id, nombre, descripcion, activo) VALUES
(1, 'Medicina General', 'Atención médica general y consultas básicas', 1),
(2, 'Pediatría', 'Atención médica para niños y adolescentes', 1),
(3, 'Cardiología', 'Especialista en enfermedades del corazón', 1),
(4, 'Dermatología', 'Tratamiento de enfermedades de la piel', 1),
(5, 'Odontología', 'Atención dental y bucal', 1);

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 'Tablas creadas exitosamente' AS mensaje;

SHOW TABLES LIKE '%solicitudes_servicio%';
SHOW TABLES LIKE '%ofertas%';
SHOW TABLES LIKE '%servicios%';
