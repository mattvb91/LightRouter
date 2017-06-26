<?php


namespace mattvb91\LightRouter;

use Closure;
use Exception;
use mattvb91\LightRouter\Exceptions\ArgumentMissingException;
use mattvb91\LightRouter\Exceptions\LightRouterException;
use mattvb91\LightRouter\Exceptions\MethodMissingException;
use mattvb91\LightRouter\Interfaces\LightRouterModelInterface;
use ReflectionClass;

/**
 * Class Route
 * @package mattvb91\LightRouter
 */
class Route
{

    /**
     * @var string
     */
    private $route = '';

    /**
     * @var string
     */
    private $controller = '';

    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    private static $availableMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
    ];

    /**
     * @var string
     */
    private $method = 'GET';

    /**
     * @var string
     */
    private $action = '';

    /**
     * Active params. These may have been modified.
     *
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $origParams = [];


    /**
     * Route constructor.
     * @param $route
     * @param $controller
     * @param $action
     */
    public function __construct($route, $controller, $action)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @param array $params
     */
    public function setParams($params = [])
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     * @throws ArgumentMissingException
     * @throws LightRouterException
     * @throws MethodMissingException
     */
    public function dispatch()
    {
        $this->origParams = $this->getParams();

        //Check we have an actually class not a closure
        if (! $this->controller instanceof Closure)
        {
            $controller = new $this->controller();
        } else
        {
            try
            {
                return call_user_func($this->controller);
            } catch (Exception $e)
            {
                throw new LightRouterException($e->getMessage(), $e->getCode());
            }
        }

        if (! method_exists($controller, $this->getAction()))
        {
            throw new MethodMissingException();
        }

        //Check do we need to prepare any of the method parameters
        $reflectionClass = new ReflectionClass($this->controller);
        $reflectionParams = $reflectionClass->getMethod($this->getAction())->getParameters();

        if (count($reflectionParams) !== count($this->params))
        {
            throw new ArgumentMissingException();
        }

        foreach ($reflectionParams as $param)
        {
            if ($param->getClass())
            {
                $paramClass = $param->getClass()->getName();
                if (class_exists($lightModel = 'mattvb91\LightModel\LightModel'))
                {
                    /* @var $param \ReflectionParameter */
                    if ($param->getClass()->isSubclassOf($lightModel))
                    {
                        $bindModel = $paramClass::getOneByKey($this->params[$param->getName()]);
                    }
                } else if ($param->getClass()->implementsInterface(LightRouterModelInterface::class))
                {
                    $bindModel = $paramClass::getForLightRoute($this->params[$param->getName()]);
                }

                $this->params[$param->getName()] = $bindModel;
            }
        }

        return call_user_func_array([$controller, $this->getAction()], $this->getParams());
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * These parameters could have been modified during
     * the routing process.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Unmodified params
     *
     * @return array
     */
    public function getOrigParams(): array
    {
        return $this->origParams;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        if (in_array($method, self::$availableMethods))
            $this->method = $method;
    }
}