<?php

namespace mattvb91\LightRouter\Tests;

use mattvb91\LightRouter\Router;
use mattvb91\LightRouter\Tests\Files\TestController;
use mattvb91\LightRouter\Tests\Files\User;
use PHPUnit\Framework\TestCase;

/**
 * Class TestRouter
 * @package mattvb91\LightRouter\Tests
 */
class RouterTest extends TestCase
{

    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        parent::setUp();

        $router = new Router();
        $router->addRoute('/user/view/:user', TestController::class, 'view');
        $router->addRoute('/random/:user', TestController::class, 'random');
        $router->addRoute('/usr/view/:user/:page', TestController::class, 'view');
        $router->addRoute('/', TestController::class);
        $router->addRoute('/testIntParam/:int', TestController::class, 'intParam');

        $this->router = $router;
    }

    public function testSetRoutes()
    {
        $_SERVER['PATH_INFO'] = '/random/3';

        $this->assertEquals(5, sizeof($this->router->getRoutes()));
    }


    public function testParamsMatching()
    {
        $_SERVER['PATH_INFO'] = '/user/view/3';
        $this->router->run();

        $this->assertEquals(['user' => 3], $this->router->getActiveRoute()->getOrigParams());

        $_SERVER['PATH_INFO'] = '/testIntParam/2';
        $this->assertEquals(2, $this->router->run());
    }

    public function testMethodParameterCasting()
    {
        $_SERVER['PATH_INFO'] = '/user/view/3';
        $this->router->run();

        $this->assertInstanceOf(User::class, $this->router->getActiveRoute()->getParams()['user']);
    }
}