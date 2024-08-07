<?php

use App\Controllers\AuthController;
use App\Controllers\Testing;
use App\Controllers\ProfileController;
use App\Middleware\{AuthMiddleware, CSRFMiddleware, GuestMiddleware, OnlyAdminMiddleware};
use MA\PHPQUICK\Router\Router;

Router::get('/', 'HomeController@index');

Router::get("/users/register", [AuthController::class, 'showRegistration'], GuestMiddleware::class);
Router::post("/users/register", [AuthController::class, 'register'], GuestMiddleware::class, CSRFMiddleware::class);
Router::get("/users/login", [AuthController::class, 'showLogin'], GuestMiddleware::class);
Router::post("/users/login", [AuthController::class, 'login'], GuestMiddleware::class, CSRFMiddleware::class);
Router::get("/users/logout", [AuthController::class, 'logout'], AuthMiddleware::class);
Router::get("/users/activate", [AuthController::class, 'activate'], GuestMiddleware::class);

Router::get("/users/profile", [ProfileController::class, 'edit'], OnlyAdminMiddleware::class);
Router::post("/users/profile", [ProfileController::class, 'update'], AuthMiddleware::class, CSRFMiddleware::class);
Router::get("/users/password", [ProfileController::class, 'changePassword'], AuthMiddleware::class);
Router::post("/users/password", [ProfileController::class, 'updatePassword'], AuthMiddleware::class, CSRFMiddleware::class);


/**
 * Documentation
 */
Router::get("/tes2", [Testing::class ,'implemtationSessionFlass_tes2']);
Router::get("/tes3", [Testing::class ,'implemtationSessionFlass_tes3']);
Router::get("/tes4", [Testing::class ,'implemtationSessionFlass_tes4']);