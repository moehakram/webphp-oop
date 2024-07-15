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
        $data = [
            'firstname' => '',
            'lastname' => '',
            'username' => '15',
            'address' => 'This kldlahkosodo',
            'zipcode' => '999',
            'email' => 'jo@ak.c',
            'password' => 'test1236828*>L',
            'password2' => 'test1236828*>L',
        ];

        $validator = new \App\Models\ValidateRequest();
        // $validator->loadData($data);
        $validator->firstname = '';
        $validator->lastname = 'ujjl9';
        // $validator->username = '';
        $validator->zipcode = 'ujjl9';
        $validator->email = 'akaaaaaa@d.m';
        $validator->password = 'akaaaaaa';
        $validator->password2 = '';
        $isError = $validator->validate();

        // cetak($validator->firstname);
        cetak($validator->getErrors());
    }
}