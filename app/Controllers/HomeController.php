<?php

namespace App\Controllers;

use App\Service\ServiceTrait;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\MVC\Controller;

class HomeController extends Controller
{
    use ServiceTrait;

    protected $layout = 'app';

    public function __construct()
    {
        $this->authService();        
    }

    public function index(Request $request)
    {
        response()->setNoCache();
        if (!$user = $this->sessionService->current()) {
            return view('welcome');
        } else {
            return $this->home($user->getName());
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