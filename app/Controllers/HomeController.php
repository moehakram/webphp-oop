<?php

namespace App\Controllers;

use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\MVC\Controller;

class HomeController extends Controller
{
    protected $layout = 'app';

    public function index(Request $request)
    {
        response()->setNoCache();
        if ($request->user() == null) {
            return view('welcome');
        } else {
            return $this->home($request->user());
        }
    }

    private function home($user)
    {
        return $this->view('home/index', [
            "title" => "Dashboard",
            "user" => [
                "name" => $user->name
            ]
        ]);
    }
}