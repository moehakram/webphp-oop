<?php

use App\Controllers\Testing;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;

$router->get('/', 'HomeController@index');

$router->get("/users/register", [AuthController::class, 'showRegistration'], 'guest');
$router->post("/users/register", [AuthController::class, 'register'], 'guest', 'csrf');
$router->get("/users/login", [AuthController::class, 'showLogin'], 'guest');
$router->post("/users/login", [AuthController::class, 'login'], 'guest', 'csrf');
$router->get("/users/logout", [AuthController::class, 'logout'], 'auth');
$router->get("/users/activate", [AuthController::class, 'activate'], 'guest');

$router->get("/users/profile", [ProfileController::class, 'edit'], 'admin');
$router->post("/users/profile", [ProfileController::class, 'update'], 'auth', 'csrf');
$router->get("/users/password", [ProfileController::class, 'changePassword'], 'auth');
$router->post("/users/password", [ProfileController::class, 'updatePassword'], 'auth', 'csrf');

/**
 * Documentation
 */
$router->get("/tes2", [Testing::class, 'implemtationSessionFlass_tes2']);
$router->get("/tes3", [Testing::class, 'implemtationSessionFlass_tes3']);
$router->get("/tes4", [Testing::class, 'implemtationSessionFlass_tes4']);
