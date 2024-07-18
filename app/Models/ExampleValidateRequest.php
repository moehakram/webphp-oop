<?php
namespace App\Models;

use MA\PHPQUICK\MVC\Model;

class ExampleValidateRequest extends Model
{
    public $firstname = null;
    public $lastname = null;
    public $username = null;
    public $address = null;
    public $zipcode = null;
    public $email = null;
    public $password = null;
    public $password2 = null;


    protected function rules(): array // filter sanitize
    {
        return [
            'firstname' => 'fs:string|REQUIRED|max:255|min:10',
            'lastname' => 'fs:string|required|max:255',
            'address' => 'fs:string|required|min:5|max:17',
            'zipcode' => 'fs:int|between:5,6|numeric',
            'username' => 'fs:int|required|alphanumeric|between:2,7',
            'email' => 'fs:email|required|email',
            'password' => 'fs:string|required|secure',
            'password2' => 'fs:string|required|same:password'
        ];
    }
    

    public function errorMessages(): array
    {
        return [
            'required' => 'Harap masukkan %s',
            'email' => 'Bukan alamat email yang valid',
            'min' => '%s harus memiliki setidaknya %s karakter',
            'max' => '%s harus memiliki paling banyak %s karakter',
            'between' => '%s harus memiliki antara %d dan %d karakter',
            'same' => '%s harus sesuai dengan %s',
            'alphanumeric' => '%s hanya boleh berisi huruf dan angka',
            'secure' => '%s harus memiliki antara 8 hingga 64 karakter dan mengandung setidaknya satu angka, satu huruf besar, satu huruf kecil, dan satu karakter khusus',
            'unique' => '%s sudah ada',
            'numeric' => '%s harus numerik',
            
            'username' => [
                'required'=>'tidak boleh kosong',
                'min' => 'minimal 20 karakter',
                'between' => '%s harus minimal %s atau maksimal %s karakter'
            ],
            'password2' => ['same'=> 'Please enter the same password again']
        ];
    }
}
