<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Throwable;

class ThrottleException extends \RuntimeException
{
    public function __construct($message = "")
    {
        parent::__construct($message, 0, null);
    }
}