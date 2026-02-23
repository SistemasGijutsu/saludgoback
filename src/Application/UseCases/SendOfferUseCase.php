<?php

namespace Application\UseCases;

use Domain\Entities\Offer;
use Domain\Repositories\OfferRepositoryInterface;
use Domain\Repositories\ServiceRequestRepositoryInterface;
use Domain\Repositories\DoctorProfileRepositoryInterface;

class SendOfferUseCase
{
    private OfferRepositoryInterface $offerRepository;
    private ServiceRequestRepositoryInterface $requestRepository;
    private DoctorProfileRepositoryInterface $doctorRepository;

    public function __construct(
        OfferRepositoryInterface $offerRepository,
        ServiceRequestRepositoryInterface $requestRepository,
        DoctorProfileRepositoryInterface $doctorRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->requestRepository = $requestRepository;
        $this->doctorRepository = $doctorRepository;
    }

    public function execute(int $serviceRequestId, int $doctorUserId, float $price, ?string $message = null): array
    {
        // Validar que el doctor existe y está verificado
        $doctorProfile = $this->doctorRepository->findByUserId($doctorUserId);
        if (!$doctorProfile || !$doctorProfile->isVerified()) {
            throw new \InvalidArgumentException('Perfil de profesional no válido o no verificado');
        }

        // Validar que la solicitud existe y está abierta
        $request = $this->requestRepository->findById($serviceRequestId);
        if (!$request) {
            throw new \InvalidArgumentException('Solicitud no encontrada');
        }

        if (!$request->isOpen()) {
            throw new \InvalidArgumentException('Esta solicitud ya no está disponible');
        }

        // Validar que el doctor pertenece a la especialidad
        if ($doctorProfile->getEspecialidadId() !== $request->getEspecialidadId()) {
            throw new \InvalidArgumentException('No puede ofertar en solicitudes fuera de su especialidad');
        }

        // Validar que el doctor no haya enviado ya una oferta
        if ($this->offerRepository->existsForDoctorAndRequest($doctorUserId, $serviceRequestId)) {
            throw new \InvalidArgumentException('Ya has enviado una oferta para esta solicitud');
        }

        // Crear oferta
        $offer = new Offer($serviceRequestId, $doctorUserId, $price, $message);
        $offer = $this->offerRepository->save($offer);

        return [
            'success' => true,
            'message' => 'Oferta enviada exitosamente',
            'data' => $offer->toArray()
        ];
    }
}
