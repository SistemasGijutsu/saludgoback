<?php

namespace Domain\Repositories;

use Domain\Entities\Offer;

interface OfferRepositoryInterface
{
    public function save(Offer $offer): Offer;
    public function findById(int $id): ?Offer;
    public function findByServiceRequestId(int $serviceRequestId): array;
    public function findByDoctorId(int $doctorId): array;
    public function existsForDoctorAndRequest(int $doctorId, int $serviceRequestId): bool;
    public function update(Offer $offer): bool;
    public function rejectAllExcept(int $serviceRequestId, int $acceptedOfferId): bool;
}
