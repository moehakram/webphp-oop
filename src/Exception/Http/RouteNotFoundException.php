<?php

namespace MA\PHPQUICK\Exception\Http;

class RouteNotFoundException extends HttpException{
    public function __construct($path){
        parent::__construct(404, sprintf('Route Not Found "{ %s }"', $path));
    }
}