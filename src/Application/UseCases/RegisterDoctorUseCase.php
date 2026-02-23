<?php

namespace Application\UseCases;

use Domain\Repositories\DoctorProfileRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;

class RegisterDoctorUseCase
{
    private UserRepositoryInterface $userRepository;
    private DoctorProfileRepositoryInterface $doctorRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        DoctorProfileRepositoryInterface $doctorRepository
    ) {
        $this->userRepository = $userRepository;
        $this->doctorRepository = $doctorRepository;
    }

    public function execute(int $userId, array $profileData): array
    {
        // Validar que el usuario existe y es profesional
        $user = $this->userRepository->findById($userId);
        if (!$user || !$user->isProfesional()) {
            throw new \InvalidArgumentException('Usuario no válido');
        }

        // Verificar que no tenga ya un perfil
        $existingProfile = $this->doctorRepository->findByUserId($userId);
        if ($existingProfile) {
            throw new \InvalidArgumentException('Ya tienes un perfil de profesional');
        }

        // Crear perfil
        $profile = $this->doctorRepository->save(
            new \Domain\Entities\DoctorProfile($userId, $profileData['especialidad_id'] ?? null)
        );

        // Actualizar datos si vienen
        if (isset($profileData['cedula'])) $profile->setCedula($profileData['cedula']);
        if (isset($profileData['tarjeta_profesional'])) $profile->setTarjetaProfesional($profileData['tarjeta_profesional']);
        if (isset($profileData['medio_transporte'])) $profile->setMedioTransporte($profileData['medio_transporte']);
        if (isset($profileData['anos_experiencia'])) $profile->setAnosExperiencia($profileData['anos_experiencia']);
        if (isset($profileData['tarifa_consulta'])) $profile->setTarifaConsulta($profileData['tarifa_consulta']);
        if (isset($profileData['descripcion'])) $profile->setDescripcion($profileData['descripcion']);

        $this->doctorRepository->update($profile);

        return [
            'success' => true,
            'message' => 'Perfil de profesional creado exitosamente',
            'data' => $profile->toArray()
        ];
    }
}
