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

        // $validator->loadData($data);
        $validator->firstname = 'muh.';
        $validator->lastname = 'akram';
        $validator->address = 'btp    ';
        $validator->username = 't';
        $validator->zipcode = 'p';
        $validator->email = 0;
        $validator->password ='0000000pyJ#41';
        $validator->password2 = '0000000pyJ#41';
        $isError = $validator->validate();

        if(! $isError){
            return new \MA\PHPQUICK\Http\Responses\JsonResponse((array)$validator->getErrorsToArray(), 400);
        }
        
        return new \MA\PHPQUICK\Http\Responses\JsonResponse([
            'message' => 'success',
            'code' => '200'
        ], 200);
    }
}