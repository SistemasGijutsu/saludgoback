<?php

namespace Domain\Entities;

class DoctorProfile
{
    private ?int $id;
    private int $usuarioId;
    private ?int $especialidadId = null;
    private ?string $cedula = null;
    private ?string $tarjetaProfesional = null;
    private ?string $medioTransporte = null;
    private ?int $anosExperiencia = null;
    private ?float $tarifaConsulta = null;
    private ?string $descripcion = null;
    private ?string $fotoDocumentoIdentidad = null;
    private ?string $fotoTarjetaProfesional = null;
    private ?string $selfieConTarjeta = null;
    private ?string $documentoAdicional1 = null;
    private ?string $documentoAdicional2 = null;
    private ?string $documentoAdicional3 = null;
    private int $verificado;
    private string $estadoVerificacion; // 'pendiente' | 'en_revision' | 'aprobado' | 'rechazado'
    private ?\DateTime $fechaVerificacion = null;
    private ?string $notasVerificacion = null;
    private ?int $verificadoPor = null;
    private int $aceptaTerminos;
    private ?\DateTime $fechaAceptaTerminos = null;

    public function __construct(
        int $usuarioId,
        ?int $especialidadId = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->especialidadId = $especialidadId;
        $this->verificado = 0;
        $this->estadoVerificacion = 'pendiente';
        $this->aceptaTerminos = 0;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getEspecialidadId(): ?int { return $this->especialidadId; }
    public function getCedula(): ?string { return $this->cedula; }
    public function getTarjetaProfesional(): ?string { return $this->tarjetaProfesional; }
    public function getMedioTransporte(): ?string { return $this->medioTransporte; }
    public function getAnosExperiencia(): ?int { return $this->anosExperiencia; }
    public function getTarifaConsulta(): ?float { return $this->tarifaConsulta; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getVerificado(): int { return $this->verificado; }
    public function getEstadoVerificacion(): string { return $this->estadoVerificacion; }
    public function getFechaVerificacion(): ?\DateTime { return $this->fechaVerificacion; }
    public function getNotasVerificacion(): ?string { return $this->notasVerificacion; }
    public function getAceptaTerminos(): int { return $this->aceptaTerminos; }
    public function getFotoDocumentoIdentidad(): ?string { return $this->fotoDocumentoIdentidad; }
    public function getFotoTarjetaProfesional(): ?string { return $this->fotoTarjetaProfesional; }
    public function getSelfieConTarjeta(): ?string { return $this->selfieConTarjeta; }
    public function getDocumentoAdicional1(): ?string { return $this->documentoAdicional1; }
    public function getDocumentoAdicional2(): ?string { return $this->documentoAdicional2; }
    public function getDocumentoAdicional3(): ?string { return $this->documentoAdicional3; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setEspecialidadId(?int $id): void { $this->especialidadId = $id; }
    public function setCedula(?string $cedula): void { $this->cedula = $cedula; }
    public function setTarjetaProfesional(?string $tarjeta): void { $this->tarjetaProfesional = $tarjeta; }
    public function setMedioTransporte(?string $medio): void { $this->medioTransporte = $medio; }
    public function setAnosExperiencia(?int $anos): void { $this->anosExperiencia = $anos; }
    public function setTarifaConsulta(?float $tarifa): void { $this->tarifaConsulta = $tarifa; }
    public function setDescripcion(?string $desc): void { $this->descripcion = $desc; }
    public function setFotoDocumentoIdentidad(?string $foto): void { $this->fotoDocumentoIdentidad = $foto; }
    public function setFotoTarjetaProfesional(?string $foto): void { $this->fotoTarjetaProfesional = $foto; }
    public function setSelfieConTarjeta(?string $foto): void { $this->selfieConTarjeta = $foto; }
    public function setDocumentoAdicional1(?string $doc): void { $this->documentoAdicional1 = $doc; }
    public function setDocumentoAdicional2(?string $doc): void { $this->documentoAdicional2 = $doc; }
    public function setDocumentoAdicional3(?string $doc): void { $this->documentoAdicional3 = $doc; }
    public function setVerificado(int $verificado): void { $this->verificado = $verificado; }
    public function setEstadoVerificacion(string $estado): void { $this->estadoVerificacion = $estado; }
    public function setFechaVerificacion(?\DateTime $fecha): void { $this->fechaVerificacion = $fecha; }
    public function setNotasVerificacion(?string $notas): void { $this->notasVerificacion = $notas; }
    public function setVerificadoPor(?int $id): void { $this->verificadoPor = $id; }
    public function setAceptaTerminos(int $acepta): void { $this->aceptaTerminos = $acepta; }
    public function setFechaAceptaTerminos(?\DateTime $fecha): void { $this->fechaAceptaTerminos = $fecha; }

    // Métodos de negocio
    public function isVerified(): bool {
        return $this->verificado === 1 && $this->estadoVerificacion === 'aprobado';
    }

    public function approve(int $verificadoPorId, ?string $notas = null): void {
        $this->verificado = 1;
        $this->estadoVerificacion = 'aprobado';
        $this->fechaVerificacion = new \DateTime();
        $this->verificadoPor = $verificadoPorId;
        $this->notasVerificacion = $notas;
    }

    public function reject(int $verificadoPorId, string $motivo): void {
        $this->verificado = 0;
        $this->estadoVerificacion = 'rechazado';
        $this->fechaVerificacion = new \DateTime();
        $this->verificadoPor = $verificadoPorId;
        $this->notasVerificacion = $motivo;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuarioId,
            'especialidad_id' => $this->especialidadId,
            'cedula' => $this->cedula,
            'tarjeta_profesional' => $this->tarjetaProfesional,
            'medio_transporte' => $this->medioTransporte,
            'anos_experiencia' => $this->anosExperiencia,
            'tarifa_consulta' => $this->tarifaConsulta,
            'descripcion' => $this->descripcion,
            'foto_documento_identidad' => $this->fotoDocumentoIdentidad,
            'foto_tarjeta_profesional' => $this->fotoTarjetaProfesional,
            'selfie_con_tarjeta' => $this->selfieConTarjeta,
            'documento_adicional_1' => $this->documentoAdicional1,
            'documento_adicional_2' => $this->documentoAdicional2,
            'documento_adicional_3' => $this->documentoAdicional3,
            'verificado' => $this->verificado,
            'estado_verificacion' => $this->estadoVerificacion,
            'fecha_verificacion' => $this->fechaVerificacion?->format('Y-m-d H:i:s'),
            'acepta_terminos' => $this->aceptaTerminos,
        ];
    }
}
