<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Interfaces\Request;
use App\Models\User\UserLoginRequest;
use App\Models\User\UserRegisterRequest;
use App\Service\ServiceTrait;
use MA\PHPQUICK\Exception\ValidationException;
use MA\PHPQUICK\Session\Session;
use MA\PHPQUICK\Validation\InputHandler;
use MA\PHPQUICK\Validation\Validation;

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
            'title' => 'Login User'
        ]);
    }

    public function login(Request $request) // Proses login pengguna
    {
        $req = new UserLoginRequest();
        // $req->id = $request->post('id');
        // $req->password = $request->post('password');

        try {
            $user = $this->userService->login($req);
            $this->sessionService->create($user);
            return response()->redirect('/');
        } catch (ValidationException $exception) {
            return $this->view('auth/login', [
                'title' => 'Login User',
                'errors' => $exception->getMessage()
            ]);
        }
    }

    public function showRegistration(Request $request) // Menampilkan formulir registrasi
    {
        return $this->view('auth/register', [
            'title' => 'Register New User',
            'inputs' => $request->session()->getFlash('inputs'),
            'errors' => $request->session()->getFlash('errors')
        ]);
    }

    public function register(Request $request)  // Proses registrasi pengguna
    {
        $req = new UserRegisterRequest($request->post());
        try {
            $this->userService->register($req);
            return response()->redirect('/users/login');
        } catch (ValidationException $exception) {
            return response()->redirect('/users/register')->with([
                'inputs' => $request->post(),
                'errors' => $exception->getErrors()->getAll()
            ]);
        }
    }

    public function activate(Request $req){
        $handler = new InputHandler($req->get(), [
            'activation_code' => 'required|@string'
        ]);
        
        $data = $handler->sanitize();
        if($handler->validate()){
            return response()->redirect('/users/login')
            ->withMessage('Tautan aktivasi tidak valid, silakan daftarkan kembali.');
        }
        
        
        if($this->userService->activationAccount($data['activation_code'])){
            return response()->redirect('/users/login')
            ->withMessage('Akun sudah aktif silakan login');
        }

        return response()->redirect('/users/register')
        ->withMessage('Tautan aktivasi tidak valid, silakan daftarkan kembali.',
        Session::FLASH_ERROR);
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
        return response()->redirect('/');
    }
}