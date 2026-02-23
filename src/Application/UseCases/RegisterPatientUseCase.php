<?php

namespace Application\UseCases;

use Application\DTOs\RegisterPatientDTO;
use Domain\Entities\User;
use Domain\Entities\PatientProfile;
use Domain\Repositories\UserRepositoryInterface;
use Domain\Repositories\PatientProfileRepositoryInterface;

class RegisterPatientUseCase
{
    private UserRepositoryInterface $userRepository;
    private PatientProfileRepositoryInterface $patientRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PatientProfileRepositoryInterface $patientRepository
    ) {
        $this->userRepository = $userRepository;
        $this->patientRepository = $patientRepository;
    }

    public function execute(RegisterPatientDTO $dto): array
    {
        // Validar DTO
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }

        // Verificar que el email no exista
        if ($this->userRepository->findByEmail($dto->email)) {
            throw new \RuntimeException('El email ya está registrado');
        }

        // Crear usuario con todos los datos personales
        $user = new User(
            $dto->nombre,
            $dto->email,
            password_hash($dto->password, PASSWORD_BCRYPT),
            'paciente'
        );
        
        // Agregar datos adicionales del paciente
        if ($dto->telefono) $user->setTelefono($dto->telefono);
        if ($dto->fechaNacimiento) $user->setFechaNacimiento($dto->fechaNacimiento);
        if ($dto->edad) $user->setEdad($dto->edad);
        if ($dto->genero) $user->setGenero($dto->genero);
        if ($dto->ciudad) $user->setCiudad($dto->ciudad);
        if ($dto->direccion) $user->setDireccion($dto->direccion);

        $savedUser = $this->userRepository->save($user);

        // Crear perfil médico adicional (solo si hay datos médicos)
        $hasPatientData = $dto->contactoEmergenciaNombre 
            || $dto->contactoEmergenciaTelefono 
            || $dto->tipoSangre 
            || $dto->alergias 
            || $dto->condicionesCronicas
            || $dto->notasMedicas;

        $patientProfile = null;
        if ($hasPatientData) {
            $patientProfile = new PatientProfile(
                $savedUser->getId(),
                $dto->contactoEmergenciaNombre,
                $dto->contactoEmergenciaTelefono,
                $dto->tipoSangre,
                $dto->alergias,
                $dto->condicionesCronicas,
                $dto->notasMedicas
            );

            $patientProfile = $this->patientRepository->save($patientProfile);
        }

        return [
            'user' => $savedUser->toArray(),
            'medical_profile' => $patientProfile ? $patientProfile->toArray() : null
        ];
    }
}
