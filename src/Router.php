<?php

namespace mattvb91\LightRouter;

use mattvb91\LightRouter\Exceptions\RouteNotFoundException;

/**
 * Class Router
 * @package mattvb91\LightRouter
 */
class Router
{

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var Route
     */
    private $activeRoute;

    /**
     * @param $route
     * @param $controller
     * @param $action
     */
    public function addRoute($route, $controller, $action = 'index')
    {
        if (! key_exists($route, $this->routes))
            $this->routes[$route] = new Route($route, $controller, $action);
    }

    /**
     * Get the correct Route and dispatch it.
     * @return mixed
     * @throws RouteNotFoundException
     */
    public function run()
    {
        if ($route = $this->matchRoute())
        {
            return $route->dispatch();
        }

        throw new RouteNotFoundException();
    }

    /**
     * @return Route
     */
    private function matchRoute(): ?Route
    {
        $path = $_SERVER['PATH_INFO'];

        foreach ($this->routes as $key => $route)
        {
            $pattern = '@^' . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($key)) . '$@D';
            $matches = [];
            $params = [];

            if (preg_match($pattern, $path, $matches))
            {
                $this->activeRoute = $route;

                array_shift($matches);
                preg_match('/:\S+/', $key, $params);
                foreach ($params as $key => $value)
                {
                    if ($replace = str_replace(':', '', $value))
                    {
                        $params[$replace] = $matches[$key];
                        unset($params[$key]);
                    }
                }

                $matches = array_combine(array_keys($params), $matches);
                $route->setParams($matches);

                return $route;
            }
        }

        return null;
    }

    /**
     * @return Route
     */
    public function getActiveRoute(): ?Route
    {
        return $this->activeRoute;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}