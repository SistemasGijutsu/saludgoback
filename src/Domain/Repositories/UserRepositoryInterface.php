<?php

namespace Domain\Repositories;

use Domain\Entities\User;

interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function update(User $user): bool;
    public function delete(int $id): bool;
}
