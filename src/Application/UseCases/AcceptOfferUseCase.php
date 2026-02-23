<?php

namespace Application\UseCases;

use Domain\Entities\Service;
use Domain\Repositories\OfferRepositoryInterface;
use Domain\Repositories\ServiceRequestRepositoryInterface;
use Domain\Repositories\ServiceRepositoryInterface;
use Infrastructure\Persistence\Database;

class AcceptOfferUseCase
{
    private OfferRepositoryInterface $offerRepository;
    private ServiceRequestRepositoryInterface $requestRepository;
    private ServiceRepositoryInterface $serviceRepository;

    public function __construct(
        OfferRepositoryInterface $offerRepository,
        ServiceRequestRepositoryInterface $requestRepository,
        ServiceRepositoryInterface $serviceRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->requestRepository = $requestRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function execute(int $offerId, int $patientUserId): array
    {
        // Validar que la oferta existe
        $offer = $this->offerRepository->findById($offerId);
        if (!$offer) {
            throw new \InvalidArgumentException('Oferta no encontrada');
        }

        if (!$offer->isPending()) {
            throw new \InvalidArgumentException('Esta oferta ya no está disponible');
        }

        // Validar que la solicitud pertenece al paciente
        $request = $this->requestRepository->findById($offer->getServiceRequestId());
        if (!$request) {
            throw new \InvalidArgumentException('Solicitud no encontrada');
        }

        if ($request->getPacienteId() !== $patientUserId) {
            throw new \InvalidArgumentException('No tienes permiso para aceptar esta oferta');
        }

        if (!$request->isOpen()) {
            throw new \InvalidArgumentException('Esta solicitud ya no está disponible');
        }

        // TRANSACCIÓN: Aceptar oferta, rechazar las demás, marcar solicitud como tomada, crear servicio
        try {
            Database::beginTransaction();

            // 1. Aceptar oferta
            $offer->accept();
            $this->offerRepository->update($offer);

            // 2. Rechazar todas las demás ofertas
            $this->offerRepository->rejectAllExcept($request->getId(), $offer->getId());

            // 3. Marcar solicitud como TAKEN
            $request->markAsTaken($offer->getId());
            $this->requestRepository->update($request);

            // 4. Crear servicio
            $service = new Service(
                $request->getId(),
                $offer->getDoctorId(),
                $patientUserId,
                $offer->getPrice()
            );
            $service = $this->serviceRepository->save($service);

            Database::commit();

            return [
                'success' => true,
                'message' => 'Oferta aceptada exitosamente',
                'data' => [
                    'service' => $service->toArray(),
                    'offer' => $offer->toArray(),
                ]
            ];

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }
}
