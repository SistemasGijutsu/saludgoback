<?php

namespace Application\UseCases;

use Domain\Repositories\ServiceRequestRepositoryInterface;
use Domain\Repositories\OfferRepositoryInterface;

class GetServiceRequestOffersUseCase
{
    private ServiceRequestRepositoryInterface $requestRepository;
    private OfferRepositoryInterface $offerRepository;

    public function __construct(
        ServiceRequestRepositoryInterface $requestRepository,
        OfferRepositoryInterface $offerRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->offerRepository = $offerRepository;
    }

    public function execute(int $serviceRequestId, int $patientUserId): array
    {
        // Validar que la solicitud existe y pertenece al paciente
        $request = $this->requestRepository->findById($serviceRequestId);
        if (!$request) {
            throw new \InvalidArgumentException('Solicitud no encontrada');
        }

        if ($request->getPacienteId() !== $patientUserId) {
            throw new \InvalidArgumentException('No tienes permiso para ver estas ofertas');
        }

        // Obtener ofertas
        $offers = $this->offerRepository->findByServiceRequestId($serviceRequestId);

        return [
            'success' => true,
            'data' => [
                'request' => $request->toArray(),
                'offers' => $offers
            ]
        ];
    }
}
