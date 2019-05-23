<?php


namespace App\Exceptions;


use Throwable;

class CommandsQueueEmpty extends BaseProjectException
{
    public function __construct($message = 'Commands queue is empty', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}