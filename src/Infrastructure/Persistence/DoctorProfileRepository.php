<?php

namespace Infrastructure\Persistence;

use Domain\Entities\DoctorProfile;
use Domain\Repositories\DoctorProfileRepositoryInterface;
use PDO;

class DoctorProfileRepository implements DoctorProfileRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(DoctorProfile $profile): DoctorProfile
    {
        $sql = "INSERT INTO profesionales 
                (usuario_id, especialidad_id, cedula, tarjeta_profesional, medio_transporte, anos_experiencia, tarifa_consulta, descripcion, verificado, estado_verificacion, acepta_terminos, fecha_acepta_terminos) 
                VALUES (:usuario_id, :especialidad_id, :cedula, :tarjeta_profesional, :medio_transporte, :anos_experiencia, :tarifa_consulta, :descripcion, :verificado, :estado_verificacion, :acepta_terminos, :fecha_acepta_terminos)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $profile->getUsuarioId(),
            ':especialidad_id' => $profile->getEspecialidadId(),
            ':cedula' => $profile->getCedula(),
            ':tarjeta_profesional' => $profile->getTarjetaProfesional(),
            ':medio_transporte' => $profile->getMedioTransporte(),
            ':anos_experiencia' => $profile->getAnosExperiencia(),
            ':tarifa_consulta' => $profile->getTarifaConsulta(),
            ':descripcion' => $profile->getDescripcion(),
            ':verificado' => $profile->getVerificado(),
            ':estado_verificacion' => $profile->getEstadoVerificacion(),
            ':acepta_terminos' => $profile->getAceptaTerminos(),
            ':fecha_acepta_terminos' => null,
        ]);

        $profile->setId((int)$this->db->lastInsertId());
        return $profile;
    }

    public function findById(int $id): ?DoctorProfile
    {
        $sql = "SELECT * FROM profesionales WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findByUserId(int $userId): ?DoctorProfile
    {
        $sql = "SELECT * FROM profesionales WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $userId]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function update(DoctorProfile $profile): bool
    {
        $sql = "UPDATE profesionales SET 
                especialidad_id = :especialidad_id,
                cedula = :cedula,
                tarjeta_profesional = :tarjeta_profesional,
                medio_transporte = :medio_transporte,
                anos_experiencia = :anos_experiencia,
                tarifa_consulta = :tarifa_consulta,
                descripcion = :descripcion,
                verificado = :verificado,
                estado_verificacion = :estado_verificacion,
                fecha_verificacion = :fecha_verificacion,
                notas_verificacion = :notas_verificacion,
                verificado_por = :verificado_por
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $profile->getId(),
            ':especialidad_id' => $profile->getEspecialidadId(),
            ':cedula' => $profile->getCedula(),
            ':tarjeta_profesional' => $profile->getTarjetaProfesional(),
            ':medio_transporte' => $profile->getMedioTransporte(),
            ':anos_experiencia' => $profile->getAnosExperiencia(),
            ':tarifa_consulta' => $profile->getTarifaConsulta(),
            ':descripcion' => $profile->getDescripcion(),
            ':verificado' => $profile->getVerificado(),
            ':estado_verificacion' => $profile->getEstadoVerificacion(),
            ':fecha_verificacion' => $profile->getFechaVerificacion()?->format('Y-m-d H:i:s'),
            ':notas_verificacion' => $profile->getNotasVerificacion(),
            ':verificado_por' => null, // TODO: agregar cuando tengamos el método
        ]);
    }

    public function findVerifiedBySpecialty(int $especialidadId): array
    {
        $sql = "SELECT * FROM profesionales 
                WHERE especialidad_id = :especialidad_id 
                AND verificado = 1 
                AND estado_verificacion = 'aprobado'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':especialidad_id' => $especialidadId]);
        
        $profiles = [];
        while ($row = $stmt->fetch()) {
            $profiles[] = $this->hydrate($row);
        }
        
        return $profiles;
    }

    private function hydrate(array $row): DoctorProfile
    {
        $profile = new DoctorProfile(
            (int)$row['usuario_id'],
            $row['especialidad_id'] ? (int)$row['especialidad_id'] : null,
            (int)$row['id']
        );

        $profile->setCedula($row['cedula']);
        $profile->setTarjetaProfesional($row['tarjeta_profesional']);
        $profile->setMedioTransporte($row['medio_transporte']);
        $profile->setAnosExperiencia($row['anos_experiencia'] ? (int)$row['anos_experiencia'] : null);
        $profile->setTarifaConsulta($row['tarifa_consulta'] ? (float)$row['tarifa_consulta'] : null);
        $profile->setDescripcion($row['descripcion']);
        $profile->setVerificado((int)$row['verificado']);
        $profile->setEstadoVerificacion($row['estado_verificacion']);
        $profile->setNotasVerificacion($row['notas_verificacion'] ?? null);
        $profile->setAceptaTerminos((int)$row['acepta_terminos']);

        if ($row['fecha_verificacion']) {
            $profile->setFechaVerificacion(new \DateTime($row['fecha_verificacion']));
        }

        return $profile;
    }
}
