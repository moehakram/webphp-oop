<?php

namespace App\Controllers;

use App\Service\ServiceTrait;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\MVC\Controller;
use MA\PHPQUICK\Validation\Validation;

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
        
        $request = new \App\Models\ExampleValidateRequest([
            'firstname' => ' <a>akram</a>    ',
            'lastname' => ' <a>akram</a>    ',
            'address' => 'address',
            'username' => '11',
            'zipcode' => 192384,
            'email' =>  'example@email.sh',
            'password' => '0000000pyJ#41',
            'password2' => '0000000pyJ#41'
        ]);

        $errors = $request->validate();

        if(!$errors->isEmpty()){
            return new \MA\PHPQUICK\Http\Responses\JsonResponse((array)$errors->getAll(), 400);
        }

        return new \MA\PHPQUICK\Http\Responses\JsonResponse([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'username' => $request->username,
            'zipcode' => $request->zipcode,
            'email' => $request->email,
            'password' => $request->password,
            'password2' => $request->password2
        ], 200);
    }

    public function testingValidationInput2(){

        $validator = new Validation([
            'firstname' => ' <a>akram</a>    ',
            'lastname' => ' <a>akram</a> klaLJSGHJKLHHHH   ',
            'address' => 'address    ',
            'username' => '11',
            'zipcode' => '83293',
            'email' =>  'eail@s.h',
            'password' => '0000000py#41Hl',
            'password2' => '0000000py#41Hl'
        ]);

        $data = $validator->validate([  // fs => filter sanitize
            'firstname' => 'fs:string|REQUIRED|max:255|min:10',
            'lastname' => 'fs:string|requireD|max:255',
            'address' => 'fs:string|reQuired|min:5|max:17',
            'zipcode' => 'fs:int|between:5,6|numeric',
            'username' => 'fs:int|required|alphanumeric|between:2,7',
            'email' => 'fs:email|required|email',
            'password' => 'fs:string|required|secure',
            'password2' => 'fs:string|required|same:password'
        ]);
        
        $errors = $validator->getErrors();
        if(!$errors->isEmpty()){
            return new \MA\PHPQUICK\Http\Responses\JsonResponse((array)$errors->getAll(), 400);
        }

        // return new \MA\PHPQUICK\Http\Responses\JsonResponse($data->getAll());

        return new \MA\PHPQUICK\Http\Responses\JsonResponse([
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'address' => $data->address,
            'username' => $data['username'],
            'zipcode' => $data->zipcode,
            'email' => $data->email,
            'password' => $data->password,
            'password2' => $data->password2
        ], 200);
    }
}