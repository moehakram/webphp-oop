<?php

use App\Controllers\AuthController;
use App\Controllers\Testing;
use App\Controllers\ProfileController;
use App\Middleware\{AuthMiddleware, CSRFMiddleware, GuestMiddleware, OnlyAdminMiddleware};

$router->get('/', 'HomeController@index');

$router->get("/users/register", [AuthController::class, 'showRegistration'], GuestMiddleware::class);
$router->post("/users/register", [AuthController::class, 'register'], GuestMiddleware::class, CSRFMiddleware::class);
$router->get("/users/login", [AuthController::class, 'showLogin'], GuestMiddleware::class);
$router->post("/users/login", [AuthController::class, 'login'], GuestMiddleware::class, CSRFMiddleware::class);
$router->get("/users/logout", [AuthController::class, 'logout'], AuthMiddleware::class);
$router->get("/users/activate", [AuthController::class, 'activate'], GuestMiddleware::class);

$router->get("/users/profile", [ProfileController::class, 'edit'], OnlyAdminMiddleware::class);
$router->post("/users/profile", [ProfileController::class, 'update'], AuthMiddleware::class, CSRFMiddleware::class);
$router->get("/users/password", [ProfileController::class, 'changePassword'], AuthMiddleware::class);
$router->post("/users/password", [ProfileController::class, 'updatePassword'], AuthMiddleware::class, CSRFMiddleware::class);


/**
 * Documentation
 */
$router->get("/tes2", [Testing::class ,'implemtationSessionFlass_tes2']);
$router->get("/tes3", [Testing::class ,'implemtationSessionFlass_tes3']);
$router->get("/tes4", [Testing::class ,'implemtationSessionFlass_tes4']);