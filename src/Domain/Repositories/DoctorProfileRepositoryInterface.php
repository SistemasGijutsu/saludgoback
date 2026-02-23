<?php

namespace Domain\Repositories;

use Domain\Entities\DoctorProfile;

interface DoctorProfileRepositoryInterface
{
    public function save(DoctorProfile $profile): DoctorProfile;
    public function findById(int $id): ?DoctorProfile;
    public function findByUserId(int $userId): ?DoctorProfile;
    public function update(DoctorProfile $profile): bool;
    public function findVerifiedBySpecialty(int $especialidadId): array;
}
