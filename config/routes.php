<?php

use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Middleware\{AuthMiddleware, CSRFMiddleware, GuestMiddleware, OnlyAdminMiddleware};

$app->get('/', 'HomeController@index');

$app->get("/user/register", [AuthController::class, 'showRegistration'], AuthMiddleware::class, GuestMiddleware::class);
$app->post("/user/register", [AuthController::class, 'register'], AuthMiddleware::class, GuestMiddleware::class, CSRFMiddleware::class);
$app->get("/user/login", [AuthController::class, 'showLogin'], GuestMiddleware::class);
$app->post("/user/login", [AuthController::class, 'login'], GuestMiddleware::class, CSRFMiddleware::class);
$app->get("/user/logout", [AuthController::class, 'logout'], AuthMiddleware::class);

$app->get("/user/profile", [ProfileController::class, 'edit'], AuthMiddleware::class, OnlyAdminMiddleware::class);
$app->post("/user/profile", [ProfileController::class, 'update'], AuthMiddleware::class, CSRFMiddleware::class);
$app->get("/user/password", [ProfileController::class, 'changePassword'], AuthMiddleware::class);
$app->post("/user/password", [ProfileController::class, 'updatePassword'], AuthMiddleware::class, CSRFMiddleware::class);
