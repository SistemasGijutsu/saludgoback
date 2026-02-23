<?php

namespace Infrastructure\Persistence;

use Domain\Entities\User;
use Domain\Repositories\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save(User $user): User
    {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, telefono, fecha_nacimiento, edad, genero, ciudad, direccion, foto_perfil, estado_cuenta, activo, fecha_registro) 
                VALUES (:nombre, :email, :password, :rol, :telefono, :fecha_nacimiento, :edad, :genero, :ciudad, :direccion, :foto_perfil, :estado_cuenta, :activo, :fecha_registro)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $user->getNombre(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':rol' => $user->getRol(),
            ':telefono' => $user->getTelefono(),
            ':fecha_nacimiento' => $user->getFechaNacimiento(),
            ':edad' => $user->getEdad(),
            ':genero' => $user->getGenero(),
            ':ciudad' => $user->getCiudad(),
            ':direccion' => $user->getDireccion(),
            ':foto_perfil' => $user->getFotoPerfil(),
            ':estado_cuenta' => $user->getEstadoCuenta(),
            ':activo' => $user->getActivo(),
            ':fecha_registro' => $user->getFechaRegistro()?->format('Y-m-d H:i:s'),
        ]);

        $user->setId((int)$this->db->lastInsertId());
        return $user;
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function update(User $user): bool
    {
        $sql = "UPDATE usuarios SET 
                nombre = :nombre, 
                email = :email, 
                telefono = :telefono, 
                fecha_nacimiento = :fecha_nacimiento, 
                edad = :edad, 
                genero = :genero, 
                ciudad = :ciudad, 
                direccion = :direccion, 
                foto_perfil = :foto_perfil, 
                estado_cuenta = :estado_cuenta, 
                activo = :activo
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $user->getId(),
            ':nombre' => $user->getNombre(),
            ':email' => $user->getEmail(),
            ':telefono' => $user->getTelefono(),
            ':fecha_nacimiento' => $user->getFechaNacimiento(),
            ':edad' => $user->getEdad(),
            ':genero' => $user->getGenero(),
            ':ciudad' => $user->getCiudad(),
            ':direccion' => $user->getDireccion(),
            ':foto_perfil' => $user->getFotoPerfil(),
            ':estado_cuenta' => $user->getEstadoCuenta(),
            ':activo' => $user->getActivo(),
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): User
    {
        $user = new User(
            $row['nombre'],
            $row['email'],
            $row['password'],
            $row['rol'],
            (int)$row['id']
        );

        $user->setTelefono($row['telefono']);
        $user->setFechaNacimiento($row['fecha_nacimiento']);
        $user->setEdad($row['edad'] ? (int)$row['edad'] : null);
        $user->setGenero($row['genero']);
        $user->setCiudad($row['ciudad']);
        $user->setDireccion($row['direccion']);
        $user->setFotoPerfil($row['foto_perfil']);
        $user->setEstadoCuenta($row['estado_cuenta']);
        $user->setActivo((int)$row['activo']);

        return $user;
    }
}
