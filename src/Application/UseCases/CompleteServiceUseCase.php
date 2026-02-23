<?php

namespace Application\UseCases;

use Domain\Repositories\ServiceRepositoryInterface;
use Domain\Repositories\ServiceRequestRepositoryInterface;
use Infrastructure\Persistence\Database;

class CompleteServiceUseCase
{
    private ServiceRepositoryInterface $serviceRepository;
    private ServiceRequestRepositoryInterface $requestRepository;

    public function __construct(
        ServiceRepositoryInterface $serviceRepository,
        ServiceRequestRepositoryInterface $requestRepository
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->requestRepository = $requestRepository;
    }

    public function execute(int $serviceId, int $userId, string $userRole): array
    {
        $service = $this->serviceRepository->findById($serviceId);
        
        if (!$service) {
            throw new \InvalidArgumentException('Servicio no encontrado');
        }

        // Validar que quien completa es el doctor o el paciente del servicio
        if ($userRole === 'profesional' && $service->getDoctorId() !== $userId) {
            throw new \InvalidArgumentException('No tienes permiso para completar este servicio');
        }

        if ($userRole === 'paciente' && $service->getPacienteId() !== $userId) {
            throw new \InvalidArgumentException('No tienes permiso para completar este servicio');
        }

        if ($service->isCompleted()) {
            throw new \InvalidArgumentException('Este servicio ya está completado');
        }

        try {
            Database::beginTransaction();

            // Completar servicio
            $service->complete();
            $this->serviceRepository->update($service);

            // Actualizar solicitud a COMPLETED
            $request = $this->requestRepository->findById($service->getServiceRequestId());
            if ($request) {
                $request->markAsCompleted();
                $this->requestRepository->update($request);
            }

            Database::commit();

            return [
                'success' => true,
                'message' => 'Servicio completado exitosamente',
                'data' => $service->toArray()
            ];

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }
}
