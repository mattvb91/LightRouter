<?php

namespace mattvb91\LightRouter\Tests\Files;

/**
 * Class TestController
 * @package mattvb91\LightRouter\Tests\Files
 */
class TestController
{

    public function view(User $user)
    {
        $test = 0;
    }

    public function intParam($int)
    {
        return $int;
    }

    public function index()
    {

    }

    public function random()
    {
    }

    public function list($user, $page)
    {
        return $user * $page;
    }

    /**
     * Method used for triggering the ArgumentMissingException
     * @param $param
     */
    public function missingRouteParam($param)
    {
    }

}