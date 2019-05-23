<?php


namespace App\Exceptions;


use Throwable;

class InvalidCommand extends BaseProjectException
{
    public function __construct($message = 'Invalid command format', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}