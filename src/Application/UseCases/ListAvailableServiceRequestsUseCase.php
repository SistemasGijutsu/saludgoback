<?php

namespace Application\UseCases;

use Domain\Repositories\ServiceRequestRepositoryInterface;
use Domain\Repositories\DoctorProfileRepositoryInterface;

class ListAvailableServiceRequestsUseCase
{
    private ServiceRequestRepositoryInterface $requestRepository;
    private DoctorProfileRepositoryInterface $doctorRepository;

    public function __construct(
        ServiceRequestRepositoryInterface $requestRepository,
        DoctorProfileRepositoryInterface $doctorRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->doctorRepository = $doctorRepository;
    }

    public function execute(int $doctorUserId): array
    {
        // Obtener perfil del doctor
        $doctorProfile = $this->doctorRepository->findByUserId($doctorUserId);
        
        if (!$doctorProfile) {
            throw new \InvalidArgumentException('Perfil de profesional no encontrado');
        }

        if (!$doctorProfile->isVerified()) {
            throw new \InvalidArgumentException('Debe estar verificado para ver solicitudes');
        }

        if (!$doctorProfile->getEspecialidadId()) {
            throw new \InvalidArgumentException('Debe tener una especialidad asignada');
        }

        // Obtener solicitudes abiertas de su especialidad
        $requests = $this->requestRepository->findOpenBySpecialty($doctorProfile->getEspecialidadId());

        $result = [];
        foreach ($requests as $request) {
            $result[] = $request->toArray();
        }

        return [
            'success' => true,
            'data' => $result
        ];
    }
}
