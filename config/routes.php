<?php

use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Middleware\{AuthMiddleware, CSRFMiddleware, OnlyMemberMiddleware, OnlyGuestMiddleware, MustLoginAdmin};

$app->get('/', 'HomeController@index', AuthMiddleware::class);

$app->get("/user/register", [AuthController::class, 'showRegistration'], AuthMiddleware::class, OnlyGuestMiddleware::class);
$app->post("/user/register", [AuthController::class, 'register'], AuthMiddleware::class, OnlyGuestMiddleware::class, CSRFMiddleware::class);
$app->get("/user/login", [AuthController::class, 'showLogin'], AuthMiddleware::class, OnlyGuestMiddleware::class);
$app->post("/user/login", [AuthController::class, 'login'], AuthMiddleware::class, OnlyGuestMiddleware::class, CSRFMiddleware::class);
$app->get("/user/logout", [AuthController::class, 'logout'], AuthMiddleware::class, OnlyMemberMiddleware::class);

$app->get("/user/profile", [ProfileController::class, 'edit'],AuthMiddleware::class, OnlyMemberMiddleware::class, MustLoginAdmin::class);
$app->post("/user/profile", [ProfileController::class, 'update'], AuthMiddleware::class, OnlyMemberMiddleware::class, CSRFMiddleware::class);
$app->get("/user/password", [ProfileController::class, 'changePassword'], AuthMiddleware::class,  OnlyMemberMiddleware::class);
$app->post("/user/password", [ProfileController::class, 'updatePassword'], AuthMiddleware::class, OnlyMemberMiddleware::class, CSRFMiddleware::class);
