<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Exception\ValidationException;
use App\Models\User\UserProfileUpdateRequest;
use App\Models\User\UserPasswordUpdateRequest;
use App\Service\SessionService;
use App\Service\UserService;
use MA\PHPQUICK\Interfaces\Request;

class ProfileController extends Controller
{
    protected $layout = 'app';

    public function show() // Menampilkan profil pengguna
    {
        // implementation
    }

    public function edit(Request $request) // Menampilkan formulir pengeditan profil
    {
        $user = $request->user();
        return $this->view('profile/profile', [
            "title" => "Update user profile",
            "user" => app(UserService::class)->getUser($user->id)
        ]);
    }

    public function update(Request $request) // Menyimpan perubahan pada profil yang telah diedit
    {
        $user = $request->user();

        $req = new UserProfileUpdateRequest();
        $req->id = $user->id;
        $req->name = $request->post('name');

        try {
            $user = app(UserService::class)->updateProfile($req);
            app(SessionService::class)->create($user); //update cookie session setelah update profile
            return response()->redirect('/');
        } catch (ValidationException $exception) {
            return response()->redirect('/users/profile')->with([
                'inputs' => $request->post(),
                'errors' => $exception->getErrors()
            ]);
        }
    }

    public function changePassword(Request $request) // Menampilkan formulir penggantian kata sandi
    {
        $user = $request->user();
        return $this->view('profile/password', [
            "title" => "Update user password",
            "username" => app(UserService::class)->getUser($user->id)->username
        ]);
    }

    public function updatePassword(Request $request) // Menyimpan perubahan pada kata sandi
    {
        $user = $request->user();
        $req = new UserPasswordUpdateRequest();
        $req->id = $user->id;
        $req->oldPassword = $request->post('oldPassword');
        $req->newPassword = $request->post('newPassword');

        try {
            app(UserService::class)->updatePassword($req);
            return response()->redirect('/');
        } catch (ValidationException $exception) {
            return response()->back()->with([
                'inputs' => $request->post(),
                'errors' => $exception->getErrors()
            ]);
        }
    }
}