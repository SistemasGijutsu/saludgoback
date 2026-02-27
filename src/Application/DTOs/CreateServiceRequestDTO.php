<?php

namespace Application\DTOs;

class CreateServiceRequestDTO
{
    public int $pacienteId;
    public int $especialidadId;
    public string $descripcion;
    public ?float $latPatient;
    public ?float $lngPatient;

    public function __construct(
        int $pacienteId, 
        int $especialidadId, 
        string $descripcion,
        ?float $latPatient = null,
        ?float $lngPatient = null
    ) {
        $this->pacienteId = $pacienteId;
        $this->especialidadId = $especialidadId;
        $this->descripcion = $descripcion;
        $this->latPatient = $latPatient;
        $this->lngPatient = $lngPatient;
    }
}
