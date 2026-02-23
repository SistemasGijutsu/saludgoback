<?php

namespace Infrastructure\Controllers;

use Application\UseCases\CompleteServiceUseCase;
use Infrastructure\Persistence\ServiceRepository;
use Infrastructure\Persistence\ServiceRequestRepository;

class ServiceController
{
    public function complete(int $serviceId, array $userData): void
    {
        try {
            $useCase = new CompleteServiceUseCase(
                new ServiceRepository(),
                new ServiceRequestRepository()
            );

            $result = $useCase->execute($serviceId, $userData['user_id'], $userData['rol']);

            response($result, 200);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            response(['error' => 'Error al completar servicio: ' . $e->getMessage()], 500);
        }
    }

    public function getMyServices(array $userData): void
    {
        try {
            $repo = new ServiceRepository();
            
            if ($userData['rol'] === 'paciente') {
                $services = $repo->findByPatientId($userData['user_id']);
            } else {
                $services = $repo->findByDoctorId($userData['user_id']);
            }

            $result = [];
            foreach ($services as $service) {
                $result[] = $service->toArray();
            }

            response(['success' => true, 'data' => $result], 200);

        } catch (\Exception $e) {
            response(['error' => 'Error al obtener servicios'], 500);
        }
    }
}
