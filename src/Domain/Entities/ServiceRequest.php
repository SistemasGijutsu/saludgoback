<?php

namespace Domain\Entities;

class ServiceRequest
{
    private ?int $id;
    private int $pacienteId;
    private int $especialidadId;
    private string $descripcion;
    private string $status; // 'OPEN' | 'TAKEN' | 'COMPLETED' | 'CANCELLED'
    private ?\DateTime $createdAt;
    private ?int $acceptedOfferId; // La oferta que fue aceptada

    public function __construct(
        int $pacienteId,
        int $especialidadId,
        string $descripcion,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->pacienteId = $pacienteId;
        $this->especialidadId = $especialidadId;
        $this->descripcion = $descripcion;
        $this->status = 'OPEN';
        $this->createdAt = new \DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getPacienteId(): int { return $this->pacienteId; }
    public function getEspecialidadId(): int { return $this->especialidadId; }
    public function getDescripcion(): string { return $this->descripcion; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    public function getAcceptedOfferId(): ?int { return $this->acceptedOfferId ?? null; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setAcceptedOfferId(?int $offerId): void { $this->acceptedOfferId = $offerId; }

    // Métodos de negocio
    public function isOpen(): bool {
        return $this->status === 'OPEN';
    }

    public function isTaken(): bool {
        return $this->status === 'TAKEN';
    }

    public function isCompleted(): bool {
        return $this->status === 'COMPLETED';
    }

    public function markAsTaken(int $acceptedOfferId): void {
        if (!$this->isOpen()) {
            throw new \DomainException("Solo se pueden tomar solicitudes abiertas");
        }
        $this->status = 'TAKEN';
        $this->acceptedOfferId = $acceptedOfferId;
    }

    public function markAsCompleted(): void {
        if (!$this->isTaken()) {
            throw new \DomainException("Solo se pueden completar solicitudes tomadas");
        }
        $this->status = 'COMPLETED';
    }

    public function cancel(): void {
        if ($this->isCompleted()) {
            throw new \DomainException("No se pueden cancelar solicitudes completadas");
        }
        $this->status = 'CANCELLED';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'paciente_id' => $this->pacienteId,
            'especialidad_id' => $this->especialidadId,
            'descripcion' => $this->descripcion,
            'status' => $this->status,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'accepted_offer_id' => $this->acceptedOfferId,
        ];
    }
}
