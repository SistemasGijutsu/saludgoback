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
            // Soportar tanto JSON como multipart/form-data
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'multipart/form-data') !== false) {
                $data = $_POST;
            } else {
                $data = jsonInput();
            }

            if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
                response(['error' => 'Datos incompletos'], 400);
            }

            // Manejar foto de perfil si viene
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                try {
                    $fotoPerfil = uploadImage($_FILES['foto_perfil'], 'profiles');
                } catch (\Exception $e) {
                    response(['error' => 'Error al subir imagen: ' . $e->getMessage()], 400);
                }
            }

            $result = $this->authService->register(
                $data['nombre'],
                $data['email'],
                $data['password'],
                'paciente',
                $fotoPerfil
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
            // Soportar tanto JSON como multipart/form-data
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'multipart/form-data') !== false) {
                $data = $_POST;
            } else {
                $data = jsonInput();
            }

            if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
                response(['error' => 'Datos incompletos'], 400);
                return;
            }

            // Manejar foto de perfil si viene
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                try {
                    $fotoPerfil = uploadImage($_FILES['foto_perfil'], 'profiles');
                } catch (\Exception $e) {
                    response(['error' => 'Error al subir imagen: ' . $e->getMessage()], 400);
                    return;
                }
            }

            // Registrar el usuario
            $result = $this->authService->register(
                $data['nombre'],
                $data['email'],
                $data['password'],
                'profesional',
                $fotoPerfil
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

            // Agregar URL completa de la foto de perfil
            if ($result['foto_perfil']) {
                $result['foto_perfil_url'] = getImageUrl($result['foto_perfil']);
            }

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

    public function updateProfilePhoto(array $userData): void
    {
        try {
            if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
                response(['error' => 'No se recibió ninguna imagen'], 400);
            }

            $userRepo = new UserRepository();
            $user = $userRepo->findById($userData['user_id']);

            if (!$user) {
                response(['error' => 'Usuario no encontrado'], 404);
            }

            // Eliminar foto anterior si existe
            $oldPhoto = $user->getFotoPerfil();
            if ($oldPhoto) {
                deleteImage($oldPhoto);
            }

            // Subir nueva foto
            try {
                $fotoPerfil = uploadImage($_FILES['foto_perfil'], 'profiles');
                $user->setFotoPerfil($fotoPerfil);
                $userRepo->update($user);

                response([
                    'success' => true,
                    'message' => 'Foto de perfil actualizada',
                    'foto_perfil' => $fotoPerfil,
                    'foto_url' => getImageUrl($fotoPerfil)
                ], 200);

            } catch (\Exception $e) {
                response(['error' => 'Error al subir imagen: ' . $e->getMessage()], 400);
            }

        } catch (\Exception $e) {
            response(['error' => 'Error al actualizar foto de perfil'], 500);
        }
    }

    public function updateProfile(array $userData): void
    {
        try {
            $data = jsonInput();

            $userRepo = new UserRepository();
            $user = $userRepo->findById($userData['user_id']);

            if (!$user) {
                response(['error' => 'Usuario no encontrado'], 404);
                return;
            }

            // Actualizar solo los campos que vienen en el request
            if (isset($data['nombre'])) {
                $user->setNombre($data['nombre']);
            }
            if (isset($data['email'])) {
                // Validar que el email no esté en uso por otro usuario
                $existingUser = $userRepo->findByEmail($data['email']);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    response(['error' => 'El email ya está en uso'], 400);
                    return;
                }
                $user->setEmail($data['email']);
            }
            if (isset($data['telefono'])) {
                $user->setTelefono($data['telefono']);
            }
            if (isset($data['ciudad'])) {
                $user->setCiudad($data['ciudad']);
            }
            if (isset($data['direccion'])) {
                $user->setDireccion($data['direccion']);
            }
            if (isset($data['genero'])) {
                $user->setGenero($data['genero']);
            }
            if (isset($data['edad'])) {
                $user->setEdad(intval($data['edad']));
            }

            $success = $userRepo->update($user);

            if ($success) {
                $result = $user->toArray();
                unset($result['password']);

                // Agregar URL completa de la foto de perfil
                if ($result['foto_perfil']) {
                    $result['foto_perfil_url'] = getImageUrl($result['foto_perfil']);
                }

                response([
                    'success' => true,
                    'message' => 'Perfil actualizado correctamente',
                    'data' => $result
                ], 200);
            } else {
                response(['error' => 'No se pudo actualizar el perfil'], 500);
            }

        } catch (\Exception $e) {
            response(['error' => 'Error al actualizar perfil: ' . $e->getMessage()], 500);
        }
    }
