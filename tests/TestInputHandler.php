<?php

use MA\PHPQUICK\Validation\Validator;

require __DIR__ . '/../vendor/autoload.php';

// Input data
$inputs = [
    // 'firstname' => ' <a>akram</a>455    ',
    'lastname' => ' <a>akram</a> klaLJ\'SGHJKLHHHH   ',
    'address' => 'addr     ',
    'username' => '11',
    'zipcode' => '83293khhh',
    'email' => 'eail@s.h',
    'password' => '0000000py#41Hl',
    'password2' => '0000000py#41Hl'
];

// Validation rules
$fields = [
    'firstname ' => 'REQUIRED| @trim |max:255|min:10',
    'lastname' => 'requireD|@trim|max:255',
    'address' => '@string|reQuired|min:5|max:17',
    'zipcode' => 'between:5,6|numeric|@int',
    'username' => 'required|alphanumeric|@int|between:2,7',
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


function displayRules(Validator $handler){
    echo 'Sanitization Rules: ' . PHP_EOL;
    print_r($handler->getSanitizationRule());
    echo 'Validation Rules: ' . PHP_EOL;
    print_r($handler->getValidationRules());
}

function displayResultsSanitize(Validator $handler) {
    echo 'Sanitized Inputs: ' . PHP_EOL;
    var_dump($handler->sanitize());
}

function displayResultsValidate(Validator $handler) {
    echo 'Validation Results: ' . PHP_EOL;
    var_dump($handler->validate());
}

function displayResultsFilter(Validator $handler) {
    echo 'Filtered Data: ' . PHP_EOL;
    var_dump($handler->filter());
}


$inputHandler = new Validator($inputs, $fields, $messages);
// displayResultsSanitize($inputHandler);
displayResultsValidate($inputHandler);
// displayResultsFilter($inputHandler);
// displayResultData($inputHandler);

