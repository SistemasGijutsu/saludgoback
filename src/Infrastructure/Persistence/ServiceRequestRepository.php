<?php

namespace Infrastructure\Persistence;

use Domain\Entities\ServiceRequest;
use Domain\Repositories\ServiceRequestRepositoryInterface;
use PDO;

class ServiceRequestRepository implements ServiceRequestRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(ServiceRequest $request): ServiceRequest
    {
        $sql = "INSERT INTO solicitudes_servicio (paciente_id, especialidad_id, descripcion, status, created_at) 
                VALUES (:paciente_id, :especialidad_id, :descripcion, :status, :created_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':paciente_id' => $request->getPacienteId(),
            ':especialidad_id' => $request->getEspecialidadId(),
            ':descripcion' => $request->getDescripcion(),
            ':status' => $request->getStatus(),
            ':created_at' => $request->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);

        $request->setId((int)$this->db->lastInsertId());
        return $request;
    }

    public function findById(int $id): ?ServiceRequest
    {
        $sql = "SELECT * FROM solicitudes_servicio WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findByPatientId(int $pacienteId): array
    {
        $sql = "SELECT * FROM solicitudes_servicio 
                WHERE paciente_id = :paciente_id 
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':paciente_id' => $pacienteId]);
        
        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = $this->hydrate($row);
        }
        
        return $requests;
    }

    public function findOpenBySpecialty(int $especialidadId): array
    {
        $sql = "SELECT * FROM solicitudes_servicio 
                WHERE especialidad_id = :especialidad_id 
                AND status = 'OPEN'
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':especialidad_id' => $especialidadId]);
        
        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = $this->hydrate($row);
        }
        
        return $requests;
    }

    public function update(ServiceRequest $request): bool
    {
        $sql = "UPDATE solicitudes_servicio SET 
                status = :status,
                accepted_offer_id = :accepted_offer_id
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $request->getId(),
            ':status' => $request->getStatus(),
            ':accepted_offer_id' => $request->getAcceptedOfferId(),
        ]);
    }

    private function hydrate(array $row): ServiceRequest
    {
        $request = new ServiceRequest(
            (int)$row['paciente_id'],
            (int)$row['especialidad_id'],
            $row['descripcion'],
            (int)$row['id']
        );

        $request->setStatus($row['status']);
        
        if (isset($row['accepted_offer_id']) && $row['accepted_offer_id']) {
            $request->setAcceptedOfferId((int)$row['accepted_offer_id']);
        }

        return $request;
    }
}
