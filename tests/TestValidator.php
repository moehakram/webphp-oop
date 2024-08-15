<?php

use MA\PHPQUICK\Exceptions\ValidationException;
use MA\PHPQUICK\Validation\Validation;

require __DIR__ . '/../vendor/autoload.php';

// Input data
$inputs = [
    'firstname' => 'muhammad',
    'lastname' => ' <a>akram</a> #klaLJ\'SGHJKLHHHH   ',
    'address' => 'addr     ',
    'username' => 'tesSKA',
    'zipcode' => '45679',
    'email' => 'eail@s.h',
    'password' => '0000000py#41Hl',
    'password2' => '0000000py#41Hl'
];

// Validation rules
$fields = [
    'firstname ' => 'required|alpha:s|min:5',
    'lastname' => 'required|max:255|alpha',
    'address' => 'reQuired|min:5|max:17',
    'zipcode' => ['between:5,6', 'digit:5', 'numeric'],
    'username' => ['required','alnum','between:3,7'],
    'email' => 'required|email',
    'password' => 'required|secure',
    'password2' => 'required|same:password'
];

// Custom error messages
$messages = [
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
    ],
    'agree' => [
        'required' => 'Anda perlu menyetujui syarat layanan untuk mendaftar.'
    ]
];



try {
    $validator = new Validation($inputs, $fields, $messages);
    $data = $validator->validate();
    print_r($data->getAll());
} catch (ValidationException $ex) {
    echo 'errors';
    print_r($ex->getErrors()->getAll());
}