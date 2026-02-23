<?php

namespace Domain\Repositories;

use Domain\Entities\Service;

interface ServiceRepositoryInterface
{
    public function save(Service $service): Service;
    public function findById(int $id): ?Service;
    public function findByDoctorId(int $doctorId): array;
    public function findByPatientId(int $pacienteId): array;
    public function update(Service $service): bool;
}
