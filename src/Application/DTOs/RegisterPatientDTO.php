<?php

namespace Application\DTOs;

class RegisterPatientDTO
{
    // Datos del usuario (tabla usuarios)
    public string $nombre;
    public string $email;
    public string $password;
    public ?string $telefono;
    public ?string $fechaNacimiento;
    public ?int $edad;
    public ?string $genero;
    public ?string $ciudad;
    public ?string $direccion;
    
    // Datos médicos adicionales (tabla pacientes)
    public ?string $contactoEmergenciaNombre;
    public ?string $contactoEmergenciaTelefono;
    public ?string $tipoSangre;
    public ?string $alergias;
    public ?string $condicionesCronicas;
    public ?string $notasMedicas;

    public function __construct(array $data)
    {
        // Datos básicos del usuario
        $this->nombre = $data['nombre'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->telefono = $data['telefono'] ?? null;
        $this->fechaNacimiento = $data['fecha_nacimiento'] ?? null;
        $this->edad = isset($data['edad']) ? (int)$data['edad'] : null;
        $this->genero = $data['genero'] ?? null;
        $this->ciudad = $data['ciudad'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        
        // Datos médicos adicionales
        $this->contactoEmergenciaNombre = $data['contacto_emergencia_nombre'] ?? null;
        $this->contactoEmergenciaTelefono = $data['contacto_emergencia_telefono'] ?? null;
        $this->tipoSangre = $data['tipo_sangre'] ?? null;
        $this->alergias = $data['alergias'] ?? null;
        $this->condicionesCronicas = $data['condiciones_cronicas'] ?? null;
        $this->notasMedicas = $data['notas_medicas'] ?? null;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->email)) {
            $errors[] = 'El email es obligatorio';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del email es inválido';
        }

        if (empty($this->password)) {
            $errors[] = 'La contraseña es obligatoria';
        } elseif (strlen($this->password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if (empty($this->nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }

        // Validar género si se proporciona
        if ($this->genero !== null && !in_array($this->genero, ['masculino', 'femenino', 'otro'])) {
            $errors[] = 'El género debe ser masculino, femenino u otro';
        }

        // Validar tipo de sangre si se proporciona
        if ($this->tipoSangre !== null) {
            $validBloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            if (!in_array($this->tipoSangre, $validBloodTypes)) {
                $errors[] = 'Tipo de sangre inválido';
            }
        }

        // Validar fecha de nacimiento si se proporciona
        if ($this->fechaNacimiento !== null) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->fechaNacimiento);
            if (!$date || $date->format('Y-m-d') !== $this->fechaNacimiento) {
                $errors[] = 'Formato de fecha de nacimiento inválido (debe ser Y-m-d)';
            }
        }

        return $errors;
    }
}
