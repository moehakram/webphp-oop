<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Contracts\RequestInterface as Request;
use App\Models\User\{UserLoginRequest, UserRegisterRequest};
use App\Service\{SessionService, UserService};
use MA\PHPQUICK\Exceptions\ValidationException;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Validation\Validator;

class AuthController extends Controller
{
    protected $layout = 'app';

    public function showLogin() // Menampilkan formulir login
    {
        response()->setNoCache();
        return View::auth_login([
            'title' => 'Login User',
            'errors' => session()->getFlash('message')
        ])->extend('app');
    }

    public function login(Request $request) // Proses login pengguna
    {
        $req = new UserLoginRequest();
        $req->username = $request->post('username');
        $req->password = $request->post('password');

        try {
            $user = $this->make(UserService::class)->login($req);
            $session = $this->make(SessionService::class)->create($user);
            write_log("login : $user->username, session_id : $session->id ");
            return response()->redirect('/');
        } catch (ValidationException $ex) {
            write_log(sprintf('login username : "%s"; password : "%s"; error : "%s"', $req->username, $req->username, $ex->getMessage()));
            return response()->back()->withMessage($ex->getMessage(), 'error');
        }
    }

    public function showRegistration(Request $request) // Menampilkan formulir registrasi
    {
        return View::auth_register()->extend('app')
        ->with([
            'title' => 'Register New User',
            'errors' => $request->session()->getFlash('message')
        ]);

        // return $this->view('auth/register', [
        //     'title' => 'Register New User',
        //     'errors' => $request->session()->getFlash('message')
        // ]);
    }

    public function register(Request $request)  // Proses registrasi pengguna
    {
        $req = new UserRegisterRequest($request->post());
        try {
            $this->make(UserService::class)->register($req);
            write_log(['username' => $req->username, 'password' => $req->password]);
            return response()->redirect('/users/login')
            ->withMessage('Silakan buka email untuk aktivasi akun!');
        } catch (ValidationException $exception) {
            return response()->back()->withErrors($exception->getErrors())->withInputs();
        }
    }

    public function activate(Request $req){
        try{
            $handler = new Validator($req->query(), [
                'activation_code' => 'required|@string'
            ], ['required' => 'Tautan aktivasi tidak valid']);
            
            $data = $handler->filter();
            $this->make(UserService::class)->activationAccount($data['activation_code']);
            return response()->redirect('/users/login')
            ->withMessage('login', 'Akun sudah aktif silakan login');
        }catch(ValidationException $val){
            return response()->redirect('/users/register')
            ->withMessage($val->getMessage(), 'error');
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
        $this->make(SessionService::class)->destroy();
        return response()->redirect('/');
    }
}