<?php

namespace Infrastructure\Persistence;

use Domain\Entities\Specialty;
use Domain\Repositories\SpecialtyRepositoryInterface;
use PDO;

class SpecialtyRepository implements SpecialtyRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Specialty
    {
        $sql = "SELECT * FROM especialidades WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM especialidades ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        
        $specialties = [];
        while ($row = $stmt->fetch()) {
            $specialties[] = $this->hydrate($row);
        }
        
        return $specialties;
    }

    public function findActive(): array
    {
        $sql = "SELECT * FROM especialidades WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        
        $specialties = [];
        while ($row = $stmt->fetch()) {
            $specialties[] = $this->hydrate($row);
        }
        
        return $specialties;
    }

    private function hydrate(array $row): Specialty
    {
        $specialty = new Specialty(
            $row['nombre'],
            $row['descripcion'],
            (int)$row['id']
        );

        $specialty->setActivo((int)$row['activo']);

        return $specialty;
    }
}
