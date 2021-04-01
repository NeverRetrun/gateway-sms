<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Throwable;

class InvalidMobileException extends InvalidSmsMessage
{
    public function __construct($message = "手机号码异常", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}