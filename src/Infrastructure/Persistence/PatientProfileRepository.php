<?php

namespace Infrastructure\Persistence;

use PDO;
use Domain\Entities\PatientProfile;
use Domain\Repositories\PatientProfileRepositoryInterface;

class PatientProfileRepository implements PatientProfileRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(PatientProfile $patientProfile): PatientProfile
    {
        $sql = "INSERT INTO pacientes (
                    usuario_id, contacto_emergencia_nombre, contacto_emergencia_telefono,
                    tipo_sangre, alergias, condiciones_cronicas, notas_medicas, created_at
                ) VALUES (
                    :usuario_id, :contacto_emergencia_nombre, :contacto_emergencia_telefono,
                    :tipo_sangre, :alergias, :condiciones_cronicas, :notas_medicas, :created_at
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $patientProfile->getUsuarioId(),
            ':contacto_emergencia_nombre' => $patientProfile->getContactoEmergenciaNombre(),
            ':contacto_emergencia_telefono' => $patientProfile->getContactoEmergenciaTelefono(),
            ':tipo_sangre' => $patientProfile->getTipoSangre(),
            ':alergias' => $patientProfile->getAlergias(),
            ':condiciones_cronicas' => $patientProfile->getCondicionesCronicas(),
            ':notas_medicas' => $patientProfile->getNotasMedicas(),
            ':created_at' => $patientProfile->getCreatedAt()
        ]);

        $patientProfile->setId((int) $this->db->lastInsertId());
        return $patientProfile;
    }

    public function findById(int $id): ?PatientProfile
    {
        $sql = "SELECT * FROM pacientes WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findByUserId(int $userId): ?PatientProfile
    {
        $sql = "SELECT * FROM pacientes WHERE usuario_id = :usuario_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $userId]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function update(PatientProfile $patientProfile): bool
    {
        $sql = "UPDATE pacientes SET
                    contacto_emergencia_nombre = :contacto_emergencia_nombre,
                    contacto_emergencia_telefono = :contacto_emergencia_telefono,
                    tipo_sangre = :tipo_sangre,
                    alergias = :alergias,
                    condiciones_cronicas = :condiciones_cronicas,
                    notas_medicas = :notas_medicas,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $patientProfile->getId(),
            ':contacto_emergencia_nombre' => $patientProfile->getContactoEmergenciaNombre(),
            ':contacto_emergencia_telefono' => $patientProfile->getContactoEmergenciaTelefono(),
            ':tipo_sangre' => $patientProfile->getTipoSangre(),
            ':alergias' => $patientProfile->getAlergias(),
            ':condiciones_cronicas' => $patientProfile->getCondicionesCronicas(),
            ':notas_medicas' => $patientProfile->getNotasMedicas()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM pacientes 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        return array_map(fn($data) => $this->hydrate($data), $results);
    }

    public function existsByUserId(int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM pacientes WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $userId]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function hydrate(array $data): PatientProfile
    {
        return new PatientProfile(
            usuarioId: (int) $data['usuario_id'],
            contactoEmergenciaNombre: $data['contacto_emergencia_nombre'],
            contactoEmergenciaTelefono: $data['contacto_emergencia_telefono'],
            tipoSangre: $data['tipo_sangre'],
            alergias: $data['alergias'],
            condicionesCronicas: $data['condiciones_cronicas'],
            notasMedicas: $data['notas_medicas'],
            id: (int) $data['id'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at']
        );
    }
}
