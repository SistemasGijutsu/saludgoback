<?php

namespace Infrastructure\Controllers;

use Infrastructure\Auth\AuthService;
use Infrastructure\Persistence\UserRepository;
use Infrastructure\Persistence\DoctorProfileRepository;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $userRepo = new UserRepository();
        $this->authService = new AuthService($userRepo);
    }

    public function registerPatient(): void
    {
        try {
            $data = jsonInput();

            if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
                response(['error' => 'Datos incompletos'], 400);
            }

            $result = $this->authService->register(
                $data['nombre'],
                $data['email'],
                $data['password'],
                'paciente'
            );

            response($result, 201);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            response(['error' => 'Error al registrar paciente'], 500);
        }
    }

    public function registerDoctor(): void
    {
        try {
            $data = jsonInput();

            if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
                response(['error' => 'Datos incompletos'], 400);
                return;
            }

            // Registrar el usuario
            $result = $this->authService->register(
                $data['nombre'],
                $data['email'],
                $data['password'],
                'profesional'
            );

            // SIEMPRE crear el perfil del profesional, es obligatorio
            $doctorRepo = new DoctorProfileRepository();
            
            $especialidadId = null;
            if (isset($data['especialidad_id'])) {
                $especialidadId = intval($data['especialidad_id']);
            }
            
            $profile = new \Domain\Entities\DoctorProfile(
                $result['user']['id'], 
                $especialidadId
            );
            
            // Agregar datos opcionales del perfil si vienen
            if (isset($data['cedula'])) {
                $profile->setCedula($data['cedula']);
            }
            if (isset($data['tarjeta_profesional'])) {
                $profile->setTarjetaProfesional($data['tarjeta_profesional']);
            }
            if (isset($data['medio_transporte'])) {
                // Normalizar a minúsculas y validar
                $medioTransporte = strtolower(trim($data['medio_transporte']));
                if (in_array($medioTransporte, ['motocicleta', 'automovil', 'ninguno'])) {
                    $profile->setMedioTransporte($medioTransporte);
                }
            }
            if (isset($data['anos_experiencia'])) {
                $profile->setAnosExperiencia(intval($data['anos_experiencia']));
            }
            if (isset($data['tarifa_consulta'])) {
                $profile->setTarifaConsulta(floatval($data['tarifa_consulta']));
            }
            if (isset($data['descripcion'])) {
                $profile->setDescripcion($data['descripcion']);
            }

            // Guardar el perfil
            $profile = $doctorRepo->save($profile);
            $result['doctor_profile'] = $profile->toArray();

            response($result, 201);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            response([
                'error' => 'Error al registrar profesional: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function login(): void
    {
        try {
            $data = jsonInput();

            if (!isset($data['email']) || !isset($data['password'])) {
                response(['error' => 'Email y contraseña son requeridos'], 400);
            }

            $result = $this->authService->login($data['email'], $data['password']);

            response($result, 200);

        } catch (\InvalidArgumentException $e) {
            response(['error' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            response(['error' => 'Error al iniciar sesión'], 500);
        }
    }

    public function me(array $userData): void
    {
        try {
            $userRepo = new UserRepository();
            $user = $userRepo->findById($userData['user_id']);

            if (!$user) {
                response(['error' => 'Usuario no encontrado'], 404);
            }

            $result = $user->toArray();
            unset($result['password']);

            // Si es doctor, traer su perfil
            if ($user->isProfesional()) {
                $doctorRepo = new DoctorProfileRepository();
                $profile = $doctorRepo->findByUserId($user->getId());
                if ($profile) {
                    $result['doctor_profile'] = $profile->toArray();
                }
            }

            response(['success' => true, 'data' => $result], 200);

        } catch (\Exception $e) {
            response(['error' => 'Error al obtener información del usuario'], 500);
        }
    }
}
