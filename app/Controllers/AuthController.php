<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Interfaces\Request;
use App\Models\User\UserLoginRequest;
use App\Models\User\UserRegisterRequest;
use App\Service\ServiceTrait;
use MA\PHPQUICK\Exception\ValidationException;

class AuthController extends Controller
{
    use ServiceTrait;

    protected $layout = 'app';

    public function __construct()
    {
        $this->authService();        
    }

    public function showLogin() // Menampilkan formulir login
    {
        response()->setNoCache();
        return $this->view('auth/login', [
            'title' => 'Login User',
            'csrf_token' => set_CSRF('/user/login')
        ]);
    }

    public function login(Request $request) // Proses login pengguna
    {
        $req = new UserLoginRequest();
        $req->id = $request->post('id');
        $req->password = $request->post('password');

        try {
            $user = $this->userService->login($req);
            $this->sessionService->create($user);
            response()->redirect('/');
        } catch (ValidationException $exception) {

            return $this->view('auth/login', [
                'title' => 'Login User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function showRegistration() // Menampilkan formulir registrasi
    {
        return $this->view('auth/register', [
            'title' => 'Register New User',
            'csrf_token' => set_CSRF('/user/register')
        ]);
    }

    public function register(Request $request)  // Proses registrasi pengguna
    {
        $req = new UserRegisterRequest();
        $req->id = $request->post('id');
        $req->name = $request->post('name');
        $req->password = $request->post('password');

        try {
            $this->userService->register($req);
            response()->redirect('/user/login');
        } catch (ValidationException $exception) {

            return $this->view('auth/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function showResetPassword() // Menampilkan formulir reset password
    {
        // Implementation
    }

    public function resetPassword() // Proses reset password
    {
        // Implementation
    }

    public function logout() // Proses logout pengguna
    {
        $this->sessionService->destroy();
        response()->redirect('/');
    }
}