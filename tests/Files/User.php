<?php


namespace mattvb91\LightRouter\Tests\Files;

use mattvb91\LightRouter\Interfaces\LightRouteModelInterface;

class User implements LightRouteModelInterface
{


    /**
     * LightRouter will call this method & expect you to
     * return the associated record from the passed $routeParam.
     *
     * @param $routeParam
     * @return mixed
     */
    public static function getForLightRoute($routeParam): LightRouteModelInterface
    {
        return new self();
    }
}