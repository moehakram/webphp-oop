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

        // $validator->loadData($data);
        $validator->firstname = 'qwer';
        $validator->lastname = 'ujjl9';
        $validator->username = '';
        $validator->zipcode = 'ujjl9';
        $validator->email = 'akaaaaaa@d.m';
        $validator->password = 'test1236828*>L';
        $validator->password2 = 'test1236828*>L';
        $isError = $validator->validate();

        cetak($validator->getErrors());
    }
}