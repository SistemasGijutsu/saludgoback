<?php

namespace Infrastructure\Controllers;

use Application\DTOs\RegisterPatientDTO;
use Application\UseCases\RegisterPatientUseCase;
use Domain\Repositories\PatientProfileRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;

class PatientController
{
    private PatientProfileRepositoryInterface $patientRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        PatientProfileRepositoryInterface $patientRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->patientRepository = $patientRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Registrar un nuevo paciente
     * POST /api/patients/register
     */
    public function register(): void
    {
        try {
            // Leer datos de entrada
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos o JSON mal formado'
                ]);
                return;
            }

            // Crear DTO y ejecutar caso de uso
            $dto = new RegisterPatientDTO($data);
            $useCase = new RegisterPatientUseCase($this->userRepository, $this->patientRepository);
            $result = $useCase->execute($dto);

            // Generar token JWT
            try {
                $jwt = new \Infrastructure\Auth\JWT();
                $token = $jwt->encode([
                    'user_id' => $result['user']['id'],
                    'email' => $result['user']['email'],
                    'rol' => $result['user']['rol']
                ]);
            } catch (\Exception $jwtError) {
                // Si falla el JWT, al menos devolver el usuario sin token
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Paciente registrado exitosamente (sin token)',
                    'user' => $result['user'],
                    'token_error' => 'No se pudo generar el token'
                ]);
                return;
            }

            // Respuesta exitosa con token
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Paciente registrado exitosamente',
                'token' => $token,
                'user' => $result['user']
            ]);

        } catch (\InvalidArgumentException $e) {
            // Errores de validación
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\RuntimeException $e) {
            // Email ya existe
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            // Cualquier otro error
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar paciente',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Obtener perfil de paciente por ID
     * GET /api/patients/{id}
     */
    public function getById(int $id): void
    {
        try {
            $patient = $this->patientRepository->findById($id);

            if (!$patient) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $patient->toArray()
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener paciente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener perfil de paciente por user_id
     * GET /api/patients/by-user/{userId}
     */
    public function getByUserId(int $userId): void
    {
        try {
            $patient = $this->patientRepository->findByUserId($userId);

            if (!$patient) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Perfil de paciente no encontrado'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $patient->toArray()
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener paciente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar perfil de paciente
     * PUT /api/patients/{id}
     */
    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ]);
                return;
            }

            $patient = $this->patientRepository->findById($id);
            
            if (!$patient) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
                return;
            }

            // Crear nuevo objeto con datos actualizados
            $updatedPatient = new \Domain\Entities\PatientProfile(
                usuarioId: $patient->getUsuarioId(),
                contactoEmergenciaNombre: $data['contacto_emergencia_nombre'] ?? $patient->getContactoEmergenciaNombre(),
                contactoEmergenciaTelefono: $data['contacto_emergencia_telefono'] ?? $patient->getContactoEmergenciaTelefono(),
                tipoSangre: $data['tipo_sangre'] ?? $patient->getTipoSangre(),
                alergias: $data['alergias'] ?? $patient->getAlergias(),
                condicionesCronicas: $data['condiciones_cronicas'] ?? $patient->getCondicionesCronicas(),
                notasMedicas: $data['notas_medicas'] ?? $patient->getNotasMedicas(),
                id: $id,
                createdAt: $patient->getCreatedAt(),
                updatedAt: $patient->getUpdatedAt()
            );

            $success = $this->patientRepository->update($updatedPatient);

            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Perfil médico actualizado exitosamente',
                    'data' => $updatedPatient->toArray()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el perfil'
                ]);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar paciente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener perfil del paciente autenticado
     * GET /api/patients/me
     */
    public function getMyProfile(): void
    {
        try {
            // Obtener el usuario autenticado del middleware
            $userId = $_SERVER['AUTH_USER_ID'] ?? null;

            if (!$userId) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'No autenticado'
                ]);
                return;
            }

            $patient = $this->patientRepository->findByUserId($userId);

            if (!$patient) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Perfil de paciente no encontrado'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $patient->toArray()
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener perfil: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Listar todos los pacientes (con paginación)
     * GET /api/patients
     */
    public function list(): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

            $patients = $this->patientRepository->findAll($limit, $offset);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => array_map(fn($p) => $p->toArray(), $patients),
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($patients)
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar pacientes: ' . $e->getMessage()
            ]);
        }
    }
}
