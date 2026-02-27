<?php

namespace Infrastructure\Auth;

use Domain\Entities\User;
use Domain\Repositories\UserRepositoryInterface;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private JWT $jwt;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->jwt = new JWT();
    }

    public function register(string $nombre, string $email, string $password, string $rol = 'paciente', ?string $fotoPerfil = null): array
    {
        // Verificar si el email ya existe
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            throw new \InvalidArgumentException('El email ya está registrado');
        }

        // Validar rol
        if (!in_array($rol, ['paciente', 'profesional'])) {
            throw new \InvalidArgumentException('Rol inválido');
        }

        // Crear usuario
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $user = new User($nombre, $email, $hashedPassword, $rol);
        
        // Agregar foto de perfil si existe
        if ($fotoPerfil) {
            $user->setFotoPerfil($fotoPerfil);
        }
        
        $user = $this->userRepository->save($user);

        // Generar token
        $token = $this->generateToken($user);

        return [
            'message' => 'Usuario registrado exitosamente',
            'user' => $this->userToArray($user),
            'token' => $token
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException('Credenciales inválidas');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new \InvalidArgumentException('Credenciales inválidas');
        }

        if (!$user->isActive()) {
            throw new \InvalidArgumentException('Usuario inactivo o suspendido');
        }

        $token = $this->generateToken($user);

        return [
            'message' => 'Inicio de sesión exitoso',
            'user' => $this->userToArray($user),
            'token' => $token
        ];
    }

    public function validateToken(string $token): ?array
    {
        return $this->jwt->decode($token);
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);
        
        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        return $this->userRepository->findById($payload['user_id']);
    }

    private function generateToken(User $user): string
    {
        return $this->jwt->encode([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'rol' => $user->getRol(),
        ]);
    }

    private function userToArray(User $user): array
    {
        $data = $user->toArray();
        unset($data['password']); // No enviar password nunca
        return $data;
    }
}
