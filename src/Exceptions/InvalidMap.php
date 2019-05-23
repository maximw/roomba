<?php


namespace App\Exceptions;


use Throwable;

class InvalidMap extends BaseProjectException
{
    public function __construct($message = 'Invalid format', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}