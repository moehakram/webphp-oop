<?php



// $clean = fn($path) => ($path === '/') ? $path : str_replace(['%20', ' '], '-', rtrim($path, '/'));
$clean = fn($path) => str_replace(['%20', ' '], '-', rtrim($path, '/')) ?: '/';

var_dump($clean(''));