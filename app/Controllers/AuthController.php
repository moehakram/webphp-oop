<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Interfaces\Request;
use App\Models\User\UserLoginRequest;
use App\Models\User\UserRegisterRequest;
use MA\PHPQUICK\Exception\ValidationException;
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
        ], 'app');
    }

    public function login(Request $request) // Proses login pengguna
    {
        $req = new UserLoginRequest();
        $req->username = $request->post('username');
        $req->password = $request->post('password');

        try {
            $user = app('userService')->login($req);
            app('sessionService')->create($user);
            return response()->redirect('/');
        } catch (ValidationException $ex) {
            return response()->back()->withMessage($ex->getMessage(), 'error');
        }
    }

    public function showRegistration(Request $request) // Menampilkan formulir registrasi
    {
        return View::auth_register()->withLayout('app')
        ->withData([
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
            app('userService')->register($req);
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
            app('userService')->activationAccount($data['activation_code']);
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
        app('sessionService')->destroy();
        return response()->redirect('/');
    }
}