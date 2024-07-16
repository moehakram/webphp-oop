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
        
        $validator = new \App\Models\ExampleValidateRequest();

        $data = [
            'firstname' => ' <a>akram</a>    ',
            'lastname' => ' <a>akram</a>    ',
            'address' => 'address    ',
            'username' => '11',
            'zipcode' => 192384,
            'email' =>  'example@email.sh',
            'password' => '0000000pyJ#41',
            'password2' => '0000000pyJ#41'
        ];
        
        $validator->loadData($data);
        
        $isError = $validator->validate();
        if($isError){
            return new \MA\PHPQUICK\Http\Responses\JsonResponse((array)$validator->getErrorsToArray(), 400);
        }
        
        return new \MA\PHPQUICK\Http\Responses\JsonResponse([
            'firstname' => $validator->firstname,
            'lastname' => $validator->lastname,
            'address' => $validator->address,
            'username' => $validator->username,
            'zipcode' => $validator->zipcode,
            'email' => $validator->email,
            'password' => $validator->password,
            'password2' => $validator->password2
        ], 200);
    }
}