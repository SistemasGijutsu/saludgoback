<?php

namespace Infrastructure\Controllers;

use Application\UseCases\ListAvailableServiceRequestsUseCase;
use Application\UseCases\SendOfferUseCase;
use Infrastructure\Persistence\ServiceRequestRepository;
use Infrastructure\Persistence\DoctorProfileRepository;
use Infrastructure\Persistence\OfferRepository;

class DoctorController
{
    public function getAvailableRequests(array $userData): void
    {
        try {
            $useCase = new ListAvailableServiceRequestsUseCase(
                new ServiceRequestRepository(),
                new DoctorProfileRepository()
            );

            $result = $useCase->execute($userData['user_id']);

            response($result, 200);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            response(['error' => 'Error al obtener solicitudes disponibles'], 500);
        }
    }

    public function sendOffer(int $serviceRequestId, array $userData): void
    {
        try {
            $data = jsonInput();

            if (!isset($data['price'])) {
                response(['error' => 'El precio es requerido'], 400);
            }

            $useCase = new SendOfferUseCase(
                new OfferRepository(),
                new ServiceRequestRepository(),
                new DoctorProfileRepository()
            );

            $result = $useCase->execute(
                $serviceRequestId,
                $userData['user_id'],
                (float)$data['price'],
                $data['message'] ?? null
            );

            response($result, 201);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            response(['error' => 'Error al enviar oferta: ' . $e->getMessage()], 500);
        }
    }

    public function getMyOffers(array $userData): void
    {
        try {
            $repo = new OfferRepository();
            $offers = $repo->findByDoctorId($userData['user_id']);

            $result = [];
            foreach ($offers as $offer) {
                $result[] = $offer->toArray();
            }

            response(['success' => true, 'data' => $result], 200);

        } catch (\Exception $e) {
            response(['error' => 'Error al obtener ofertas'], 500);
        }
    }
}
