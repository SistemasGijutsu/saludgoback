<?php

namespace Domain\Entities;

class Service
{
    private ?int $id;
    private int $serviceRequestId;
    private int $doctorId;
    private int $pacienteId;
    private float $finalPrice;
    private ?\DateTime $startedAt;
    private ?\DateTime $completedAt;
    private ?string $status; // 'IN_PROGRESS' | 'COMPLETED' | 'CANCELLED'

    public function __construct(
        int $serviceRequestId,
        int $doctorId,
        int $pacienteId,
        float $finalPrice,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->serviceRequestId = $serviceRequestId;
        $this->doctorId = $doctorId;
        $this->pacienteId = $pacienteId;
        $this->finalPrice = $finalPrice;
        $this->status = 'IN_PROGRESS';
        $this->startedAt = new \DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getServiceRequestId(): int { return $this->serviceRequestId; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getPacienteId(): int { return $this->pacienteId; }
    public function getFinalPrice(): float { return $this->finalPrice; }
    public function getStartedAt(): ?\DateTime { return $this->startedAt; }
    public function getCompletedAt(): ?\DateTime { return $this->completedAt ?? null; }
    public function getStatus(): ?string { return $this->status; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setStatus(string $status): void { $this->status = $status; }

    // Métodos de negocio
    public function complete(): void {
        if ($this->status !== 'IN_PROGRESS') {
            throw new \DomainException("Solo se pueden completar servicios en progreso");
        }
        $this->status = 'COMPLETED';
        $this->completedAt = new \DateTime();
    }

    public function cancel(): void {
        if ($this->status === 'COMPLETED') {
            throw new \DomainException("No se pueden cancelar servicios completados");
        }
        $this->status = 'CANCELLED';
    }

    public function isCompleted(): bool {
        return $this->status === 'COMPLETED';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'service_request_id' => $this->serviceRequestId,
            'doctor_id' => $this->doctorId,
            'paciente_id' => $this->pacienteId,
            'final_price' => $this->finalPrice,
            'started_at' => $this->startedAt?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
            'status' => $this->status,
        ];
    }
}
