<?php

namespace Infrastructure\Persistence;

use Domain\Entities\Offer;
use Domain\Repositories\OfferRepositoryInterface;
use PDO;

class OfferRepository implements OfferRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(Offer $offer): Offer
    {
        $sql = "INSERT INTO ofertas (service_request_id, doctor_id, price, message, status, created_at) 
                VALUES (:service_request_id, :doctor_id, :price, :message, :status, :created_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':service_request_id' => $offer->getServiceRequestId(),
            ':doctor_id' => $offer->getDoctorId(),
            ':price' => $offer->getPrice(),
            ':message' => $offer->getMessage(),
            ':status' => $offer->getStatus(),
            ':created_at' => $offer->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);

        $offer->setId((int)$this->db->lastInsertId());
        return $offer;
    }

    public function findById(int $id): ?Offer
    {
        $sql = "SELECT * FROM ofertas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findByServiceRequestId(int $serviceRequestId): array
    {
        $sql = "SELECT o.*, u.nombre as doctor_nombre, p.tarifa_consulta, p.anos_experiencia
                FROM ofertas o
                INNER JOIN profesionales p ON o.doctor_id = p.usuario_id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE o.service_request_id = :service_request_id 
                ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':service_request_id' => $serviceRequestId]);
        
        $offers = [];
        while ($row = $stmt->fetch()) {
            $offer = $this->hydrate($row);
            $offers[] = [
                'offer' => $offer->toArray(),
                'doctor_info' => [
                    'nombre' => $row['doctor_nombre'],
                    'anos_experiencia' => $row['anos_experiencia'],
                    'tarifa_consulta' => $row['tarifa_consulta'],
                ]
            ];
        }
        
        return $offers;
    }

    public function findByDoctorId(int $doctorId): array
    {
        $sql = "SELECT * FROM ofertas 
                WHERE doctor_id = :doctor_id 
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':doctor_id' => $doctorId]);
        
        $offers = [];
        while ($row = $stmt->fetch()) {
            $offers[] = $this->hydrate($row);
        }
        
        return $offers;
    }

    public function existsForDoctorAndRequest(int $doctorId, int $serviceRequestId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM ofertas 
                WHERE doctor_id = :doctor_id 
                AND service_request_id = :service_request_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':doctor_id' => $doctorId,
            ':service_request_id' => $serviceRequestId,
        ]);
        
        $row = $stmt->fetch();
        return $row['count'] > 0;
    }

    public function update(Offer $offer): bool
    {
        $sql = "UPDATE ofertas SET status = :status WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $offer->getId(),
            ':status' => $offer->getStatus(),
        ]);
    }

    public function rejectAllExcept(int $serviceRequestId, int $acceptedOfferId): bool
    {
        $sql = "UPDATE ofertas 
                SET status = 'REJECTED' 
                WHERE service_request_id = :service_request_id 
                AND id != :accepted_offer_id 
                AND status = 'PENDING'";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':service_request_id' => $serviceRequestId,
            ':accepted_offer_id' => $acceptedOfferId,
        ]);
    }

    private function hydrate(array $row): Offer
    {
        $offer = new Offer(
            (int)$row['service_request_id'],
            (int)$row['doctor_id'],
            (float)$row['price'],
            $row['message'],
            (int)$row['id']
        );

        $offer->setStatus($row['status']);

        return $offer;
    }
}
