<?php

namespace App\Controllers;

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Http\RequestInterface as Request;

class HomeController extends Controller
{
    protected $layout = 'app';

    public function index(Request $request)
    {
        response()->setNoCache();
        if (!$user = $request->user()) {
            return View::welcome();
        } else {
            return $this->home($user->name);
        }
    }

    private function home($name)
    {
        return $this->view('home.index')->with([
            'title' => 'Dashboard',
            'user' => ['name' => $name]
        ]);
    }
}