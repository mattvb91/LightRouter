<?php

namespace mattvb91\LightRouter\Interfaces;

/**
 * Interface for setting up LightRoute routing to
 * custom model classes.
 *
 * Class LightRouteModelInterface
 */
interface LightRouteModelInterface
{

    /**
     * LightRouter will call this method & expect you to
     * return the associated record from the passed $routeParam.
     *
     * @param $routeParam
     * @return mixed
     */
    public static function getForLightRoute($routeParam): LightRouteModelInterface;
}