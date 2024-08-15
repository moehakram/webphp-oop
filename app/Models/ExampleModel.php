<?php
namespace App\Models;

use MA\PHPQUICK\MVC\Model;
use MA\PHPQUICK\Traits\PropertyAccessor;

class ExampleModel extends Model
{
    use PropertyAccessor;
    
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
        return  [  // @ => filter sanitize
            'firstname ' => '|REQUIRED| @trim |max:255|min:10',
            'lastname' => 'requireD|@trim|max:255',
            'address' => '@string|reQuired|min:5|max:17',
            'zipcode' => 'between:5,6|numeric|@int',
            'username' => 'required|alphanumeric|@int|between:2,7',
            'email' => 'required|email',
            'password' => 'required|secure',
            'password2' => 'required|same:password'
        ];
    }
    

    public function messages(): array
    {
        return [
            'required' => '%s wajib diisi',
            'email' => '%s bukan alamat email yang valid',
            'min' => '%s harus memiliki setidaknya %s karakter',
            'max' => '%s harus memiliki paling banyak %s karakter',
            'between' => '%s harus memiliki antara %d dan %d karakter',
            'same' => '%s harus cocok dengan %s',
            'alphanumeric' => '%s hanya boleh terdiri dari huruf dan angka',
            'secure' => '%s harus memiliki antara 8 hingga 64 karakter dan mengandung setidaknya satu angka, satu huruf kapital, satu huruf kecil, dan satu karakter khusus',
            'unique' => '%s sudah ada',
            'password' => [
                'secure' => 'Password harus memiliki antara 8 hingga 64 karakter dan mengandung setidaknya satu angka, satu huruf kapital, satu huruf kecil, dan satu karakter khusus.'
            ],
            'password2' => [
                'required' => 'Harap konfirmasi password di isi',
                'same' => 'Password tidak sama!'
            ]
        ];
    }
}
