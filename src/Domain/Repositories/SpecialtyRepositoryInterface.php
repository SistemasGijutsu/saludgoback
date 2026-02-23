<?php

namespace Domain\Repositories;

use Domain\Entities\Specialty;

interface SpecialtyRepositoryInterface
{
    public function findById(int $id): ?Specialty;
    public function findAll(): array;
    public function findActive(): array;
}
