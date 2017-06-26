<?php

namespace mattvb91\LightRouter\Tests;

use mattvb91\LightRouter\Exceptions\ArgumentMissingException;
use mattvb91\LightRouter\Exceptions\LightRouterException;
use mattvb91\LightRouter\Exceptions\MethodMissingException;
use mattvb91\LightRouter\Exceptions\RouteNotFoundException;
use mattvb91\LightRouter\Route;
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
        $router->addRoute('/user/list/:user/:page', TestController::class, 'list');
        $router->addRoute('/', TestController::class);
        $router->addRoute('/testIntParam/:int', TestController::class, 'intParam');
        $router->addRoute('/testClosure', function ()
        {
            return 'inside closure';
        });

        $router->addRoute('/testClosureException', function ()
        {
            sdsd;
        });

        $router->addRoute('/missingMethod', TestController::class, 'noneExistent');
        $router->addRoute('/missingArgument', TestController::class, 'missingRouteParam');

        $this->router = $router;
    }

    public function testSetRoutes()
    {
        $_SERVER['PATH_INFO'] = '/random/3';
        $this->assertEquals(9, sizeof($this->router->getRoutes()));
    }

    /**
     * Test closures
     */
    public function testClosure()
    {
        $_SERVER['PATH_INFO'] = '/testClosure';
        $this->assertEquals('inside closure', $this->router->run());

        $this->expectException(LightRouterException::class);
        $_SERVER['PATH_INFO'] = '/testClosureException';
        $this->router->run();
    }

    public function testParamsMatching()
    {
        $_SERVER['PATH_INFO'] = '/user/view/3';
        $this->router->run();

        $this->assertEquals(['user' => 3], $this->router->getActiveRoute()->getOrigParams());

        $_SERVER['PATH_INFO'] = '/testIntParam/2';
        $this->assertEquals(2, $this->router->run());

        $random1 = rand(1, 10000);
        $random2 = rand(1, 10000);

        $_SERVER['PATH_INFO'] = "/user/list/$random1/$random2";
        $this->assertEquals($random1 * $random2, $this->router->run());
    }

    public function testMethodParameterCasting()
    {
        $_SERVER['PATH_INFO'] = '/user/view/3';
        $this->router->run();

        $this->assertInstanceOf(User::class, $this->router->getActiveRoute()->getParams()['user']);
    }

    public function testMissingControllerAction()
    {
        $this->expectException(MethodMissingException::class);

        $_SERVER['PATH_INFO'] = '/missingMethod';
        $this->router->run();
    }

    public function testRouteNotFoundException()
    {
        $this->expectException(RouteNotFoundException::class);

        $_SERVER['PATH_INFO'] = '/' . rand(1, 100);
        $this->router->run();
    }

    public function testArgumentMissing()
    {
        $this->expectException(ArgumentMissingException::class);

        $_SERVER['PATH_INFO'] = '/missingArgument';
        $this->router->run();
    }

    public function testRouteSetup()
    {
        /* @var $route Route */
        $route = $this->router->getRoutes()['/random/:user'];

        $this->assertEquals(Route::METHOD_GET, $route->getMethod());
        $route->setMethod(Route::METHOD_POST);
        $this->assertEquals(Route::METHOD_POST, $route->getMethod());

        $this->assertEquals($route->getRoute(), '/random/:user');
        $this->assertEquals($route->getController(), TestController::class);
    }
}