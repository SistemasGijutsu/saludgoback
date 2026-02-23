<?php

namespace Infrastructure\Controllers;

use Application\UseCases\AcceptOfferUseCase;
use Application\UseCases\CompleteServiceUseCase;
use Infrastructure\Persistence\OfferRepository;
use Infrastructure\Persistence\ServiceRequestRepository;
use Infrastructure\Persistence\ServiceRepository;

class OfferController
{
    public function accept(int $offerId, array $userData): void
    {
        try {
            $useCase = new AcceptOfferUseCase(
                new OfferRepository(),
                new ServiceRequestRepository(),
                new ServiceRepository()
            );

            $result = $useCase->execute($offerId, $userData['user_id']);

            response($result, 200);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            response(['error' => 'Error al aceptar oferta: ' . $e->getMessage()], 500);
        }
    }
}
