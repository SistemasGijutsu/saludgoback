<?php

namespace Infrastructure\Controllers;

use Application\UseCases\CreateServiceRequestUseCase;
use Application\UseCases\GetServiceRequestOffersUseCase;
use Application\DTOs\CreateServiceRequestDTO;
use Infrastructure\Persistence\ServiceRequestRepository;
use Infrastructure\Persistence\SpecialtyRepository;
use Infrastructure\Persistence\UserRepository;
use Infrastructure\Persistence\OfferRepository;

class ServiceRequestController
{
    public function create(array $userData): void
    {
        try {
            $data = jsonInput();

            if (!isset($data['especialidad_id']) || !isset($data['descripcion'])) {
                response(['error' => 'Datos incompletos'], 400);
            }

            $useCase = new CreateServiceRequestUseCase(
                new ServiceRequestRepository(),
                new SpecialtyRepository(),
                new UserRepository()
            );

            $dto = new CreateServiceRequestDTO(
                $userData['user_id'],
                (int)$data['especialidad_id'],
                $data['descripcion']
            );

            $result = $useCase->execute($dto);

            response($result, 201);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            response(['error' => 'Error al crear solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getMyRequests(array $userData): void
    {
        try {
            $repo = new ServiceRequestRepository();
            $requests = $repo->findByPatientId($userData['user_id']);

            $result = [];
            foreach ($requests as $request) {
                $result[] = $request->toArray();
            }

            response(['success' => true, 'data' => $result], 200);

        } catch (\Exception $e) {
            response(['error' => 'Error al obtener solicitudes'], 500);
        }
    }

    public function getOffers(int $serviceRequestId, array $userData): void
    {
        try {
            $useCase = new GetServiceRequestOffersUseCase(
                new ServiceRequestRepository(),
                new OfferRepository()
            );

            $result = $useCase->execute($serviceRequestId, $userData['user_id']);

            response($result, 200);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            response(['error' => 'Error al obtener ofertas'], 500);
        }
    }
}
