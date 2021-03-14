<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Throwable;

class SmsCodeException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('短信验证码无效');
    }
}