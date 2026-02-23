<?php

namespace Domain\Repositories;

use Domain\Entities\PatientProfile;

interface PatientProfileRepositoryInterface
{
    public function save(PatientProfile $patientProfile): PatientProfile;
    public function findById(int $id): ?PatientProfile;
    public function findByUserId(int $userId): ?PatientProfile;
    public function update(PatientProfile $patientProfile): bool;
    public function delete(int $id): bool;
    public function findAll(int $limit = 50, int $offset = 0): array;
    public function existsByUserId(int $userId): bool;
}
