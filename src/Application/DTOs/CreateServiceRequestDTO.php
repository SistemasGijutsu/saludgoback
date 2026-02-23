<?php

namespace Application\DTOs;

class CreateServiceRequestDTO
{
    public int $pacienteId;
    public int $especialidadId;
    public string $descripcion;

    public function __construct(int $pacienteId, int $especialidadId, string $descripcion)
    {
        $this->pacienteId = $pacienteId;
        $this->especialidadId = $especialidadId;
        $this->descripcion = $descripcion;
    }
}
