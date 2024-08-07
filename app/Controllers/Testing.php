<?php
namespace App\Controllers;

use MA\PHPQUICK\Http\Responses\JsonResponse;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Session\Session;

class Testing{

    function implemtationSessionFlass_tes2(Request $request){
        $session = $request->session();
        $session->setFlash('login', [
            'message' => 'berhasil login',
            'type' => 'success'
        ]);
        $session->set('user', [
            'id' => '123',
            'name' => 'akram'
        ]);
        return response()->redirect('/tes3');
    }

    function implemtationSessionFlass_tes3(Request $request){
        $session = $request->session();
        return new JsonResponse([
            //  $session->getFlash('login')
            session('message', []),
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