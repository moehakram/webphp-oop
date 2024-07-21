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

$inputHandler = new ExampleModel($inputs);
// $filter = [
//     'filter' => FILTER_CALLBACK,
//     'options' => fn($value) => trim(strip_tags($value)),
// ];

// $result = filter_var($inputs['firstname'], $filter['filter'], $filter['options']);
// $filter = [
//     'filter' => FILTER_SANITIZE_NUMBER_INT,
//     'flags' => FILTER_REQUIRE_SCALAR
// ];

// $result = filter_var($inputs['firstname'], $filter['filter'], ['flags' => $filter['flags']]);

// $filter = [
//     'filter' => FILTER_CALLBACK,
//     'options' => fn($value) => trim(strip_tags($value)),
// ];

// $result = filter_var($inputs['firstname'], FILTER_CALLBACK, ['options' => $filter['options']]);


// dd($inputHandler->firstname);
// $ruleSan = $inputHandler->getSanitizationRule();
// $ruleVal = $inputHandler->getValidationRules();
// echo 'rule sanitization' . PHP_EOL;
($inputHandler->filter());
echo ($inputHandler->firstname) . PHP_EOL;
echo ($inputHandler->lastname) . PHP_EOL;
echo ($inputHandler->address) . PHP_EOL;
echo ($inputHandler->zipcode) . PHP_EOL;
echo ($inputHandler->username) . PHP_EOL;
echo ($inputHandler->email) . PHP_EOL;
echo ($inputHandler->password) . PHP_EOL;
echo ($inputHandler->password2) . PHP_EOL;
cc($inputHandler->getErrors());
// echo 'rule validation' . PHP_EOL;
// cc($ruleVal);
// echo 'data sanitize' . PHP_EOL;
// var_dump($inputHandler->getInputs());
// echo 'data validate' . PHP_EOL;
// print_r($inputHandler->validate());
// echo 'data inputs' . PHP_EOL;
// print_r($inputHandler->getInputs());
// echo 'data filter' . PHP_EOL;
// dd($inputHandler->filter());
// ($inputHandler->sanitize());
// print_r($inputHandler->validate());
// echo 'data valid' . PHP_EOL;
// cc($inputHandler->getInputs());
// dd($inputHandler->getSanitizationRule());
// dd($inputHandler->getValidationRules());