<?php declare(strict_types=1);


namespace Sms;


use Sms\Exceptions\Exceptions;

class SmsResult
{
    /**
     * @var bool
     */
    public $isSuccess = false;

    /**
     * @var Exceptions
     */
    public $exceptions;

    public function __construct(Exceptions $exceptions)
    {
        $this->exceptions = $exceptions;
    }
}