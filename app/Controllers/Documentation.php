<?php
namespace App\Controllers;

use MA\PHPQUICK\Http\Responses\JsonResponse;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Session\Session;

class Documentation{
    public function validateAndSanitizeUsingModel(){
        
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

        /**
         * Tidak perlu set validation rules jika sudah diset di dalam class
         *  @return collection errors 
         */
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

    public function validateAndSanitizeUsingValidationClass(){

        $validator = new \MA\PHPQUICK\Validation\Validation([
            'firstname' => ' <a>akram</a>    ',
            'lastname' => ' <a>akram</a> klaLJSGHJKLHHHH   ',
            'address' => 'address    ',
            'username' => '11',
            'zipcode' => '83293',
            'email' =>  'eail@s.h',
            'password' => '0000000py#41Hl',
            'password2' => '0000000py#41Hl'
        ]);

        /**
         *  @return collection data 
         */
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

        // return new \MA\PHPQUICK\Http\Responses\JsonResponse([
        //     'firstname' => $data->firstname,
        //     'lastname' => $data->lastname,
        //     'address' => $data->address,
        //     'username' => $data['username'],
        //     'zipcode' => $data->zipcode,
        //     'email' => $data->email,
        //     'password' => $data->password,
        //     'password2' => $data->password2
        // ], 200);

        return new \MA\PHPQUICK\Http\Responses\JsonResponse($data->getAll());
    }

    function implemtationSessionFlass_tes2(Request $request){
        $session = $request->session();
        $session->setFlash('login', 'berhasil login', Session::FLASH_SUCCESS);
        $session->set('user', [
            'id' => '123',
            'name' => 'akram'
        ]);        
        return response()->redirect('/tes3');
    }

    function implemtationSessionFlass_tes3(Request $request){
        $session = $request->session();
        return new JsonResponse([
             $session->getAll()
        ]);
    }

    function implemtationSessionFlass_tes4(Request $request){
        $session = $request->session();
        $session->clear();
        return new JsonResponse([
             $session->getAll()
        ]);
    }
}