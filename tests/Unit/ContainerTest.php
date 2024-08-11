<?php

use App\Domain\User;
use MA\PHPQUICK\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase{
    protected Container $container;
    protected function setUp(): void{
        Container::$instance = new Container;
        $bootstrap = require base_path('bootstrap.php');

        $this->container = $bootstrap->boot(Container::$instance);

    }

    public function testGlobalFunctionCall(){
        function greet($name){
            return 'Hallo ' . $name;
        }

        $result = $this->container->call('greet',['name' => 'akram']);
        $this->assertEquals('Hallo akram', $result);
    }

    public function testMethodFromClass(){

        $this->container->bind(User::class, fn() => new User);

        $result = $this->container->call([User::class, 'getAuthIdentifierName']);
        $this->assertEquals('username', $result);
    }

    public function testStaticMethodFromClass()
    {
         $result = $this->container->call([User::class, 'get'], ['id' => '123']);
        $this->assertEquals('123', $result);
    }
}