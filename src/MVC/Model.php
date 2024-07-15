<?php

namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Validator;

abstract class Model extends Validator
{
    abstract public function rules(): array;

    // public function errorMessages(): array
    // {
    //     return [
    //         'required' => 'Silakan masukkan %s',
    //         'email' => '%s bukan alamat email yang valid',
    //         'min' => '%s harus memiliki setidaknya %s karakter',
    //         'max' => '%s harus memiliki paling banyak %s karakter',
    //         'between' => '%s harus memiliki antara %d dan %d karakter',
    //         'same' => '%s harus sesuai dengan %s',
    //         'alphanumeric' => '%s hanya boleh berisi huruf dan angka',
    //         'secure' => '%s harus memiliki antara 8 hingga 64 karakter dan mengandung setidaknya satu angka, satu huruf besar, satu huruf kecil, dan satu karakter khusus',
    //         'unique' => '%s sudah ada'
    //     ];
    // }

    protected function is_numeric(string $field){
        if (!isset($this->{$field})) {
            return true;
        }
        
        return is_numeric($this->$field);
    }
}