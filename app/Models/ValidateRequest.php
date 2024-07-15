<?php
namespace App\Models;

use MA\PHPQUICK\MVC\Model;

class ValidateRequest extends Model
{
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $username = null;
    public ?string $address = null;
    public ?string $zipcode = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password2 = null;

    public function rules(): array
    {
        return [
            'firstname' => 'required | max:255',
            'lastname' => 'required| max: 255',
            'address' => '|required|min: 5|max:50',
            'zipcode' => 'between: 5,6',
            'username' => 'required | alphanumeric| between: 2,7| unique: users,id',
            'email' => 'required | email',
            'password' => 'required | secure',
            'password2' => 'required | same:password'
        ];
    }

    public function errorMessages(): array
    {
        return [
            'required' => 'Silakan masukkan %s',
            'email' => 'Bukan alamat email yang valid',
            'min' => '%s harus memiliki setidaknya %s karakter',
            'max' => '%s harus memiliki paling banyak %s karakter',
            'between' => '%s harus memiliki antara %d dan %d karakter',
            'same' => '%s harus sesuai dengan %s',
            'alphanumeric' => '%s hanya boleh berisi huruf dan angka',
            'secure' => '%s harus memiliki antara 8 hingga 64 karakter dan mengandung setidaknya satu angka, satu huruf besar, satu huruf kecil, dan satu karakter khusus',
            'unique' => '%s sudah ada',
            'username' => [
                'required'=>'tidak boleh kosong',
                'min' => 'minimal 20 karakter',
                'between' => '%s harus minimal %s dan maksimal %s karakter'
            ],
            'password2' => ['same'=> 'Please enter the same password again']
        ];
    }

}
