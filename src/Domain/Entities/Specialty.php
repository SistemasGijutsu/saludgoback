<?php

namespace Domain\Entities;

class Specialty
{
    private ?int $id;
    private string $nombre;
    private ?string $descripcion;
    private int $activo;

    public function __construct(
        string $nombre,
        ?string $descripcion = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->activo = 1;
    }

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getActivo(): int { return $this->activo; }

    public function setId(int $id): void { $this->id = $id; }
    public function setActivo(int $activo): void { $this->activo = $activo; }

    public function isActive(): bool {
        return $this->activo === 1;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];
    }
}
