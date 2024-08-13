<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ProfileController;

$router
    ->get('/', 'HomeController@index')

    ->get("/users/register", [AuthController::class, 'showRegistration'], 'guest')
    ->post("/users/register", [AuthController::class, 'register'], 'guest', 'csrf')
    ->get("/users/login", [AuthController::class, 'showLogin'], 'guest')
    ->post("/users/login", [AuthController::class, 'login'], 'guest', 'csrf')
    ->get("/users/logout", [AuthController::class, 'logout'], 'auth')
    ->get("/users/activate", [AuthController::class, 'activate'], 'guest')

    ->get("/users/profile", [ProfileController::class, 'edit'], 'admin')
    ->post("/users/profile", [ProfileController::class, 'update'], 'auth', 'csrf')
    ->get("/users/password", [ProfileController::class, 'changePassword'], 'auth')
    ->post("/users/password", [ProfileController::class, 'updatePassword'], 'auth', 'csrf');
