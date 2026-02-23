<?php

namespace Domain\Entities;

/**
 * PatientProfile - Información médica adicional del paciente
 * Los datos personales (nombre, teléfono, etc.) están en la entidad User
 */
class PatientProfile
{
    private ?int $id;
    private int $usuarioId;
    private ?string $contactoEmergenciaNombre;
    private ?string $contactoEmergenciaTelefono;
    private ?string $tipoSangre;
    private ?string $alergias;
    private ?string $condicionesCronicas;
    private ?string $notasMedicas;
    private string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $usuarioId,
        ?string $contactoEmergenciaNombre = null,
        ?string $contactoEmergenciaTelefono = null,
        ?string $tipoSangre = null,
        ?string $alergias = null,
        ?string $condicionesCronicas = null,
        ?string $notasMedicas = null,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->contactoEmergenciaNombre = $contactoEmergenciaNombre;
        $this->contactoEmergenciaTelefono = $contactoEmergenciaTelefono;
        $this->tipoSangre = $tipoSangre;
        $this->alergias = $alergias;
        $this->condicionesCronicas = $condicionesCronicas;
        $this->notasMedicas = $notasMedicas;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getContactoEmergenciaNombre(): ?string
    {
        return $this->contactoEmergenciaNombre;
    }

    public function getContactoEmergenciaTelefono(): ?string
    {
        return $this->contactoEmergenciaTelefono;
    }

    public function getTipoSangre(): ?string
    {
        return $this->tipoSangre;
    }

    public function getAlergias(): ?string
    {
        return $this->alergias;
    }

    public function getCondicionesCronicas(): ?string
    {
        return $this->condicionesCronicas;
    }

    public function getNotasMedicas(): ?string
    {
        return $this->notasMedicas;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuarioId,
            'contacto_emergencia_nombre' => $this->contactoEmergenciaNombre,
            'contacto_emergencia_telefono' => $this->contactoEmergenciaTelefono,
            'tipo_sangre' => $this->tipoSangre,
            'alergias' => $this->alergias,
            'condiciones_cronicas' => $this->condicionesCronicas,
            'notas_medicas' => $this->notasMedicas,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
