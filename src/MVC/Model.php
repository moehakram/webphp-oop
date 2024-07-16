<?php

namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Validation\Validator;

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

    public function clean(&$data)
    {
        if (is_array($data)) {
            array_walk($data, [$this, 'clean']);
        } else {
            $data = htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    protected function is_clean(string $field): bool
    {
        if (isset($this->{$field})) {
            $this->{$field} = htmlspecialchars(stripslashes(trim($this->{$field})), ENT_QUOTES, 'UTF-8');
        }
        return true;
    }

}