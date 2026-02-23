<?php

namespace Domain\Repositories;

use Domain\Entities\ServiceRequest;

interface ServiceRequestRepositoryInterface
{
    public function save(ServiceRequest $request): ServiceRequest;
    public function findById(int $id): ?ServiceRequest;
    public function findByPatientId(int $pacienteId): array;
    public function findOpenBySpecialty(int $especialidadId): array;
    public function update(ServiceRequest $request): bool;
}
