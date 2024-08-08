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
        if (!$user = $request->user()) {
            return view('welcome');
        } else {
            return $this->home($user->name);
        }
    }

    private function home($name)
    {
        return $this->view('home/index', [
            "title" => "Dashboard",
            "user" => [
                "name" => $name
            ]
        ]);
    }
}