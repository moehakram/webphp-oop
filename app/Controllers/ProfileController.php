<?php

namespace App\Controllers;

use App\Service\UserService;
use App\Service\SessionService;
use MA\PHPQUICK\MVC\Controller;
use App\Models\User\UserProfileUpdateRequest;
use MA\PHPQUICK\Contracts\RequestInterface as Request;
use App\Models\User\UserPasswordUpdateRequest;
use MA\PHPQUICK\Exceptions\ValidationException;

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
            "user" => $this->make(UserService::class)->getUser($user->id)
        ]);
    }

    public function update(Request $request) // Menyimpan perubahan pada profil yang telah diedit
    {
        $user = $request->user();

        $req = new UserProfileUpdateRequest($request->post());
        $req->id = $user->id;

        try {
            $req->validate();
            $user = $this->make(UserService::class)->updateProfile($req);
            $this->make(SessionService::class)->create($user); //update cookie session setelah update profile
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
        return $this->view('profile/password', [
            "title" => "Update user password",
            "username" => $request->user()->getAuthIdentifier()
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
            $this->make(UserService::class)->updatePassword($req);
            return response()->redirect('/');
        } catch (ValidationException $exception) {
            return response()->back()->with([
                'inputs' => $request->post(),
                'errors' => $exception->getErrors()
            ]);
        }
    }
}