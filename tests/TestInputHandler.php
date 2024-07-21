<?php
require __DIR__ . '/../vendor/autoload.php';

use MA\PHPQUICK\InputHandler;

$inputs = [
    'firstname' => ' <a>akram</a>455    ',
    'lastname' => ' <a>akram</a> klaLJ\'SGHJKLHHHH   ',
    'address' => 'addr     ',
    'username' => '11',
    'zipcode' => '83293khhh',
    'email' =>  'eail@s.h',
    'password' => '0000000py#41Hl',
    'password2' => '0000000py#41Hl'
];

$fields = [  // @ => filter sanitize
    'firstname ' => '|REQUIRED| @trim |max:255|min:10',
    'lastname' => 'requireD|@trim|max:255',
    'address' => '@string|reQuired|min:5|max:17',
    'zipcode' => 'between:5,6|numeric|@int',
    'username' => 'required|alphanumeric|@int|between:2,7',
    'email' => 'required|email',
    'password' => 'required|secure',
    'password2' => 'required|same:password'
];

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

$inputHandler = new InputHandler($inputs, $fields, $messages);

$ruleSan = $inputHandler->getSanitizationRule();
$ruleVal = $inputHandler->getValidationRules();
// echo 'rule sanitization' . PHP_EOL;
// print_r($ruleSan);
// echo 'rule validation' . PHP_EOL;
// print_r($ruleVal);
// echo 'data sanitize' . PHP_EOL;
// var_dump($inputHandler->getInputs());
// var_dump($inputHandler->sanitize());
// // echo 'data validate' . PHP_EOL;
// print_r($inputHandler->validate());
// echo 'data clean' . PHP_EOL;
// print_r($inputHandler->getData());
// echo 'data inputs' . PHP_EOL;
// print_r($inputHandler->getInputs());
// echo 'data filter' . PHP_EOL;
dd($inputHandler->filter());
// ($inputHandler->sanitize());
// print_r($inputHandler->validate());
// echo 'data valid' . PHP_EOL;
// cc($inputHandler->getInputs());
// dd($inputHandler->getSanitizationRule());
// cc($inputHandler->getValidationRules());