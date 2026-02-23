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
            nombre: $dto->nombre,
            email: $dto->email,
            password: password_hash($dto->password, PASSWORD_BCRYPT),
            rol: 'paciente'
        );

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
                usuarioId: $savedUser->getId(),
                contactoEmergenciaNombre: $dto->contactoEmergenciaNombre,
                contactoEmergenciaTelefono: $dto->contactoEmergenciaTelefono,
                tipoSangre: $dto->tipoSangre,
                alergias: $dto->alergias,
                condicionesCronicas: $dto->condicionesCronicas,
                notasMedicas: $dto->notasMedicas
            );

            $patientProfile = $this->patientRepository->save($patientProfile);
        }

        return [
            'user' => $savedUser->toArray(),
            'medical_profile' => $patientProfile ? $patientProfile->toArray() : null
        ];
    }
}
