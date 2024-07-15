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

    public function testingValidationInput(){        
        
        $validator = new \App\Models\ValidateRequest();

        // $validator->loadData($data);
        $validator->firstname = 'muh';
        $validator->lastname = 'akram';
        $validator->address = 'btp';
        $validator->username = 't';
        $validator->zipcode = '29173';
        $validator->email = 'akramgmail.com';
        $validator->password = 'Muh.Akram#123';
        $validator->password2 = 'Muh.Akram#123';
        $isError = $validator->validate();

        cetak($validator->getErrors());
    }
}