<?php

namespace Domain\Entities;

class DoctorProfile
{
    private ?int $id;
    private int $usuarioId;
    private ?int $especialidadId;
    private ?string $cedula;
    private ?string $tarjetaProfesional;
    private ?string $medioTransporte;
    private ?int $anosExperiencia;
    private ?float $tarifaConsulta;
    private ?string $descripcion;
    private ?string $fotoDocumentoIdentidad;
    private ?string $fotoTarjetaProfesional;
    private ?string $selfieConTarjeta;
    private ?string $documentoAdicional1;
    private ?string $documentoAdicional2;
    private ?string $documentoAdicional3;
    private int $verificado;
    private string $estadoVerificacion; // 'pendiente' | 'en_revision' | 'aprobado' | 'rechazado'
    private ?\DateTime $fechaVerificacion;
    private ?string $notasVerificacion;
    private ?int $verificadoPor;
    private int $aceptaTerminos;
    private ?\DateTime $fechaAceptaTerminos;

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
    public function getCedula(): ?string { return $this->cedula ?? null; }
    public function getTarjetaProfesional(): ?string { return $this->tarjetaProfesional ?? null; }
    public function getMedioTransporte(): ?string { return $this->medioTransporte ?? null; }
    public function getAnosExperiencia(): ?int { return $this->anosExperiencia ?? null; }
    public function getTarifaConsulta(): ?float { return $this->tarifaConsulta ?? null; }
    public function getDescripcion(): ?string { return $this->descripcion ?? null; }
    public function getVerificado(): int { return $this->verificado; }
    public function getEstadoVerificacion(): string { return $this->estadoVerificacion; }
    public function getFechaVerificacion(): ?\DateTime { return $this->fechaVerificacion ?? null; }
    public function getNotasVerificacion(): ?string { return $this->notasVerificacion ?? null; }
    public function getAceptaTerminos(): int { return $this->aceptaTerminos; }

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
            'verificado' => $this->verificado,
            'estado_verificacion' => $this->estadoVerificacion,
            'fecha_verificacion' => $this->fechaVerificacion?->format('Y-m-d H:i:s'),
            'acepta_terminos' => $this->aceptaTerminos,
        ];
    }
}
