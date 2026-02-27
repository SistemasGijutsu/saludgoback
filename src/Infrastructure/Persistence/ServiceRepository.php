<?php

namespace Infrastructure\Persistence;

use Domain\Entities\Service;
use Domain\Repositories\ServiceRepositoryInterface;
use PDO;

class ServiceRepository implements ServiceRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(Service $service): Service
    {
        $sql = "INSERT INTO servicios (service_request_id, doctor_id, paciente_id, final_price, started_at, status, commission_percentage, app_commission, doctor_earning, payment_status) 
                VALUES (:service_request_id, :doctor_id, :paciente_id, :final_price, :started_at, :status, :commission_percentage, :app_commission, :doctor_earning, :payment_status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':service_request_id' => $service->getServiceRequestId(),
            ':doctor_id' => $service->getDoctorId(),
            ':paciente_id' => $service->getPacienteId(),
            ':final_price' => $service->getFinalPrice(),
            ':started_at' => $service->getStartedAt()?->format('Y-m-d H:i:s'),
            ':status' => $service->getStatus(),
            ':commission_percentage' => $service->getCommissionPercentage(),
            ':app_commission' => $service->getAppCommission(),
            ':doctor_earning' => $service->getDoctorEarning(),
            ':payment_status' => $service->getPaymentStatus(),
        ]);

        $service->setId((int)$this->db->lastInsertId());
        return $service;
    }

    public function findById(int $id): ?Service
    {
        $sql = "SELECT * FROM servicios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findByDoctorId(int $doctorId): array
    {
        $sql = "SELECT * FROM servicios 
                WHERE doctor_id = :doctor_id 
                ORDER BY started_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':doctor_id' => $doctorId]);
        
        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->hydrate($row);
        }
        
        return $services;
    }

    public function findByPatientId(int $pacienteId): array
    {
        $sql = "SELECT * FROM servicios 
                WHERE paciente_id = :paciente_id 
                ORDER BY started_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':paciente_id' => $pacienteId]);
        
        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->hydrate($row);
        }
        
        return $services;
    }

    public function update(Service $service): bool
    {
        $sql = "UPDATE servicios 
                SET status = :status, completed_at = :completed_at, payment_status = :payment_status 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $service->getId(),
            ':status' => $service->getStatus(),
            ':completed_at' => $service->getCompletedAt()?->format('Y-m-d H:i:s'),
            ':payment_status' => $service->getPaymentStatus(),
        ]);
    }

    private function hydrate(array $row): Service
    {
        $service = new Service(
            (int)$row['service_request_id'],
            (int)$row['doctor_id'],
            (int)$row['paciente_id'],
            (float)$row['final_price'],
            isset($row['commission_percentage']) ? (float)$row['commission_percentage'] : 12.00,
            (int)$row['id']
        );

        $service->setStatus($row['status']);
        
        if (isset($row['payment_status'])) {
            $service->setPaymentStatus($row['payment_status']);
        }

        return $service;
    }
}
