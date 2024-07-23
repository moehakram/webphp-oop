<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\ExampleModel;

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

function filter($value){
    $filterTrim = [
        'filter' => FILTER_CALLBACK,
        'options' => fn($value) => trim(strip_tags($value)),
    ];
    $filterInt = [
        'filter' => FILTER_SANITIZE_NUMBER_INT,
        'flags' => FILTER_REQUIRE_SCALAR
    ];
    
    $int = filter_var($value, $filterInt['filter'], ['flags' => $filterInt['flags']]);    
    $trim = filter_var($value, $filterTrim['filter'], ['options' => $filterTrim['options']]);
    var_dump([$int, $trim]);
}

function displayResultData(ExampleModel $inputs){
    $data = [
        'firstname' => $inputs->firstname,
        'lastname' => $inputs->lastname,
        'address' => $inputs->address,
        'username' => $inputs->username,
        'zipcode' =>$inputs->zipcode,
        'email' =>  $inputs->email,
        'password' => $inputs->password,
        'password2' => $inputs->password2
    ];
    echo 'data' . PHP_EOL;
    var_dump($data);
}

function displayRules(ExampleModel $handler){
    echo 'Sanitization Rules: ' . PHP_EOL;
    print_r($handler->getSanitizationRule());
    echo 'Validation Rules: ' . PHP_EOL;
    print_r($handler->getValidationRules());
}

function displayResultsSanitize(ExampleModel $handler) {
    echo 'Sanitized Inputs: ' . PHP_EOL;
    var_dump($handler->sanitize());
}

function displayResultsValidate(ExampleModel $handler) {
    echo 'Validation Results: ' . PHP_EOL;
    var_dump($handler->validate());
}

function displayResultsFilter(ExampleModel $handler) {
    echo 'Filtered Data: ' . PHP_EOL;
    var_dump($handler->filter());
}


$inputHandler = new ExampleModel($inputs);
displayResultsSanitize($inputHandler);
// displayResultsValidate($inputHandler);
// displayResultsFilter($inputHandler);
displayResultData($inputHandler);