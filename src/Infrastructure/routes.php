<?php

use Infrastructure\Router;
use Infrastructure\Controllers\AuthController;
use Infrastructure\Controllers\ServiceRequestController;
use Infrastructure\Controllers\DoctorController;
use Infrastructure\Controllers\OfferController;
use Infrastructure\Controllers\ServiceController;
use Infrastructure\Controllers\SpecialtyController;
use Infrastructure\Controllers\PatientController;

$router = new Router('/api');

// ========================================
// RUTAS PÚBLICAS (Sin autenticación)
// ========================================

// Auth
$router->post('/register/patient', function() {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->register();
});

$router->post('/register/doctor', function() {
    $controller = new AuthController();
    $controller->registerDoctor();
});

$router->post('/login', function() {
    $controller = new AuthController();
    $controller->login();
});

// Especialidades (público para que todos puedan verlas)
$router->get('/specialties', function() {
    $controller = new SpecialtyController();
    $controller->getAll();
});

// ========================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// ========================================

// Perfil
$router->get('/me', function($userData) {
    $controller = new AuthController();
    $controller->me($userData);
}, true);

// Actualizar foto de perfil
$router->post('/me/photo', function($userData) {
    $controller = new AuthController();
    $controller->updateProfilePhoto($userData);
}, true);

// Actualizar perfil (nombre, email, ciudad, etc.)
$router->put('/me', function($userData) {
    $controller = new AuthController();
    $controller->updateProfile($userData);
}, true);

// ========================================
// RUTAS DE PACIENTE
// ========================================

$router->post('/service-requests', function($userData) {
    $controller = new ServiceRequestController();
    $controller->create($userData);
}, true, ['paciente']);

$router->get('/service-requests/my', function($userData) {
    $controller = new ServiceRequestController();
    $controller->getMyRequests($userData);
}, true, ['paciente']);

$router->get('/service-requests/{id}/offers', function($id, $userData) {
    $controller = new ServiceRequestController();
    $controller->getOffers((int)$id, $userData);
}, true, ['paciente']);

$router->post('/offers/{id}/accept', function($id, $userData) {
    $controller = new OfferController();
    $controller->accept((int)$id, $userData);
}, true, ['paciente']);

// ========================================
// RUTAS DE MÉDICO/PROFESIONAL
// ========================================

$router->get('/service-requests/available', function($userData) {
    $controller = new DoctorController();
    $controller->getAvailableRequests($userData);
}, true, ['profesional']);

$router->post('/service-requests/{id}/offer', function($id, $userData) {
    $controller = new DoctorController();
    $controller->sendOffer((int)$id, $userData);
}, true, ['profesional']);

$router->get('/offers/my', function($userData) {
    $controller = new DoctorController();
    $controller->getMyOffers($userData);
}, true, ['profesional']);

// ========================================
// RUTAS DE SERVICIOS (Paciente y Médico)
// ========================================

$router->get('/services/my', function($userData) {
    $controller = new ServiceController();
    $controller->getMyServices($userData);
}, true);

$router->post('/services/{id}/complete', function($id, $userData) {
    $controller = new ServiceController();
    $controller->complete((int)$id, $userData);
}, true);

// ========================================
// RUTAS DE GESTIÓN DE PERFIL DE PACIENTE
// ========================================

// Obtener mi perfil como paciente
$router->get('/patients/me', function($userData) {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->getMyProfile();
}, true, ['paciente']);

// Obtener perfil de paciente por ID
$router->get('/patients/{id}', function($id) {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->getById((int)$id);
}, true);

// Obtener perfil de paciente por user_id
$router->get('/patients/by-user/{userId}', function($userId) {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->getByUserId((int)$userId);
}, true);

// Actualizar perfil de paciente
$router->put('/patients/{id}', function($id, $userData) {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->update((int)$id);
}, true, ['paciente']);

// Listar pacientes (solo para admin o profesionales)
$router->get('/patients', function() {
    $patientRepo = new Infrastructure\Persistence\PatientProfileRepository();
    $userRepo = new Infrastructure\Persistence\UserRepository();
    $controller = new PatientController($patientRepo, $userRepo);
    $controller->list();
}, true);

return $router;
