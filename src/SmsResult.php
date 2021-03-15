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
     * @var string|null
     */
    public $successType;

    /**
     * @var Exceptions
     */
    public $exceptions;

    public function __construct(Exceptions $exceptions, ?string $successType)
    {
        $this->exceptions = $exceptions;
        $this->successType = $successType;
    }
}