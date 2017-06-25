<?php

namespace mattvb91\LightRouter\Exceptions;

use Exception;
use Throwable;

/**
 * Class ArgumentMissingException
 * @package mattvb91\LightRouter\Exceptions
 */
class ArgumentMissingException extends Exception
{

    /**
     * ArgumentMissingException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Your controller action is expecting a route parameter which was not passed. Check your routes declaration.';

        parent::__construct($message, $code, $previous);
    }
}