<?php

namespace Application\UseCases;

use Application\DTOs\CreateServiceRequestDTO;
use Domain\Entities\ServiceRequest;
use Domain\Repositories\ServiceRequestRepositoryInterface;
use Domain\Repositories\SpecialtyRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;

class CreateServiceRequestUseCase
{
    private ServiceRequestRepositoryInterface $requestRepository;
    private SpecialtyRepositoryInterface $specialtyRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        ServiceRequestRepositoryInterface $requestRepository,
        SpecialtyRepositoryInterface $specialtyRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->specialtyRepository = $specialtyRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreateServiceRequestDTO $dto): array
    {
        // Validar que el usuario existe y es paciente
        $user = $this->userRepository->findById($dto->pacienteId);
        if (!$user) {
            throw new \InvalidArgumentException('Usuario no encontrado');
        }

        if (!$user->isPaciente()) {
            throw new \InvalidArgumentException('Solo los pacientes pueden crear solicitudes de servicio');
        }

        // Validar que la especialidad existe y está activa
        $specialty = $this->specialtyRepository->findById($dto->especialidadId);
        if (!$specialty || !$specialty->isActive()) {
            throw new \InvalidArgumentException('Especialidad no válida');
        }

        // Crear solicitud
        $request = new ServiceRequest(
            $dto->pacienteId,
            $dto->especialidadId,
            $dto->descripcion,
            $dto->latPatient,
            $dto->lngPatient
        );

        $request = $this->requestRepository->save($request);

        return [
            'success' => true,
            'message' => 'Solicitud creada exitosamente',
            'data' => $request->toArray()
        ];
    }
}
