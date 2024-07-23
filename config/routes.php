<?php

use App\Controllers\AuthController;
use App\Controllers\Testing;
use App\Controllers\ProfileController;
use App\Middleware\{AuthMiddleware, CSRFMiddleware, GuestMiddleware, OnlyAdminMiddleware};

$app->get('/', 'HomeController@index');

$app->get("/users/register", [AuthController::class, 'showRegistration'], GuestMiddleware::class);
$app->post("/users/register", [AuthController::class, 'register'], GuestMiddleware::class, CSRFMiddleware::class);
$app->get("/users/login", [AuthController::class, 'showLogin'], GuestMiddleware::class);
$app->post("/users/login", [AuthController::class, 'login'], GuestMiddleware::class, CSRFMiddleware::class);
$app->get("/users/logout", [AuthController::class, 'logout'], AuthMiddleware::class);
$app->get("/users/activate", [AuthController::class, 'activate'], GuestMiddleware::class);

$app->get("/users/profile", [ProfileController::class, 'edit'], AuthMiddleware::class, OnlyAdminMiddleware::class);
$app->post("/users/profile", [ProfileController::class, 'update'], AuthMiddleware::class, CSRFMiddleware::class);
$app->get("/users/password", [ProfileController::class, 'changePassword'], AuthMiddleware::class);
$app->post("/users/password", [ProfileController::class, 'updatePassword'], AuthMiddleware::class, CSRFMiddleware::class);


/**
 * Documentation
 */
$app->get("/tes2", [Testing::class ,'implemtationSessionFlass_tes2']);
$app->get("/tes3", [Testing::class ,'implemtationSessionFlass_tes3']);
$app->get("/tes4", [Testing::class ,'implemtationSessionFlass_tes4']);