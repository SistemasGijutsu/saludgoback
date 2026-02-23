<?php

namespace Domain\Entities;

class User
{
    private ?int $id;
    private string $nombre;
    private string $email;
    private string $password;
    private string $rol; // 'paciente' | 'profesional'
    private ?string $telefono;
    private ?string $fechaNacimiento;
    private ?int $edad;
    private ?string $genero;
    private ?string $ciudad;
    private ?string $direccion;
    private ?string $fotoPerfil;
    private string $estadoCuenta; // 'activo' | 'pendiente_verificacion' | 'suspendido'
    private int $activo;
    private ?\DateTime $fechaRegistro;

    public function __construct(
        string $nombre,
        string $email,
        string $password,
        string $rol = 'paciente',
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
        $this->estadoCuenta = 'activo';
        $this->activo = 1;
        $this->fechaRegistro = new \DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRol(): string { return $this->rol; }
    public function getTelefono(): ?string { return $this->telefono ?? null; }
    public function getFechaNacimiento(): ?string { return $this->fechaNacimiento ?? null; }
    public function getEdad(): ?int { return $this->edad ?? null; }
    public function getGenero(): ?string { return $this->genero ?? null; }
    public function getCiudad(): ?string { return $this->ciudad ?? null; }
    public function getDireccion(): ?string { return $this->direccion ?? null; }
    public function getFotoPerfil(): ?string { return $this->fotoPerfil ?? null; }
    public function getEstadoCuenta(): string { return $this->estadoCuenta; }
    public function getActivo(): int { return $this->activo; }
    public function getFechaRegistro(): ?\DateTime { return $this->fechaRegistro; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setTelefono(?string $telefono): void { $this->telefono = $telefono; }
    public function setFechaNacimiento(?string $fecha): void { $this->fechaNacimiento = $fecha; }
    public function setEdad(?int $edad): void { $this->edad = $edad; }
    public function setGenero(?string $genero): void { $this->genero = $genero; }
    public function setCiudad(?string $ciudad): void { $this->ciudad = $ciudad; }
    public function setDireccion(?string $direccion): void { $this->direccion = $direccion; }
    public function setFotoPerfil(?string $foto): void { $this->fotoPerfil = $foto; }
    public function setEstadoCuenta(string $estado): void { $this->estadoCuenta = $estado; }
    public function setActivo(int $activo): void { $this->activo = $activo; }

    // Métodos de negocio
    public function isPaciente(): bool {
        return $this->rol === 'paciente';
    }

    public function isProfesional(): bool {
        return $this->rol === 'profesional';
    }

    public function isActive(): bool {
        return $this->activo === 1 && $this->estadoCuenta === 'activo';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'rol' => $this->rol,
            'telefono' => $this->telefono,
            'fecha_nacimiento' => $this->fechaNacimiento,
            'edad' => $this->edad,
            'genero' => $this->genero,
            'ciudad' => $this->ciudad,
            'direccion' => $this->direccion,
            'foto_perfil' => $this->fotoPerfil,
            'estado_cuenta' => $this->estadoCuenta,
            'activo' => $this->activo,
            'fecha_registro' => $this->fechaRegistro?->format('Y-m-d H:i:s'),
        ];
    }
}
