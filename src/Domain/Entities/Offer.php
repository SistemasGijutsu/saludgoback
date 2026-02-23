<?php

namespace Domain\Entities;

class Offer
{
    private ?int $id;
    private int $serviceRequestId;
    private int $doctorId;
    private float $price;
    private ?string $message;
    private string $status; // 'PENDING' | 'ACCEPTED' | 'REJECTED'
    private ?\DateTime $createdAt;

    public function __construct(
        int $serviceRequestId,
        int $doctorId,
        float $price,
        ?string $message = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->serviceRequestId = $serviceRequestId;
        $this->doctorId = $doctorId;
        $this->price = $price;
        $this->message = $message;
        $this->status = 'PENDING';
        $this->createdAt = new \DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getServiceRequestId(): int { return $this->serviceRequestId; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getPrice(): float { return $this->price; }
    public function getMessage(): ?string { return $this->message; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setStatus(string $status): void { $this->status = $status; }

    // Métodos de negocio
    public function isPending(): bool {
        return $this->status === 'PENDING';
    }

    public function isAccepted(): bool {
        return $this->status === 'ACCEPTED';
    }

    public function isRejected(): bool {
        return $this->status === 'REJECTED';
    }

    public function accept(): void {
        if (!$this->isPending()) {
            throw new \DomainException("Solo se pueden aceptar ofertas pendientes");
        }
        $this->status = 'ACCEPTED';
    }

    public function reject(): void {
        if (!$this->isPending()) {
            throw new \DomainException("Solo se pueden rechazar ofertas pendientes");
        }
        $this->status = 'REJECTED';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'service_request_id' => $this->serviceRequestId,
            'doctor_id' => $this->doctorId,
            'price' => $this->price,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
