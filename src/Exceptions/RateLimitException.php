<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Sms\Handlers\SmsMessage;

class RateLimitException extends ThrottleException
{
    /**
     * @var SmsMessage
     */
    public $smsMessage;

    public function __construct(SmsMessage $smsMessage)
    {
        parent::__construct('发送消息超出最大速率');
    }
}