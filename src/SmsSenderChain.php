<?php declare(strict_types=1);


namespace Sms;


use Sms\Exceptions\Exceptions;
use Sms\Exceptions\SmsSendException;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;

class SmsSenderChain
{
    /**
     * @var SmsSender[]
     */
    protected $smsSenders;

    public function __construct(array $smsSenders)
    {
        shuffle($smsSenders);
        $this->smsSenders = $smsSenders;
    }

    /**
     * 根据网关发送短信
     * @param SmsMessage $smsMessage
     * @return SmsResult
     * @throws Exceptions
     */
    public function send(SmsMessage $smsMessage): SmsResult
    {
        $exceptions = new Exceptions();
        $result = new SmsResult($exceptions);
        while (count($this->smsSenders) !== 0) {
            try {
                $this->sendBySender($smsMessage);
            } catch (SmsSendException $sendException) {
                $exceptions->appendException($sendException);
            }

            $result->isSuccess = true;
            break;
        }

        if ($result->isSuccess === false) {
            throw $exceptions;
        }

        return $result;
    }

    /**
     * 发送短信
     * @param SmsMessage $smsMessage
     */
    public function sendBySender(SmsMessage $smsMessage): void
    {
        $sender   = array_shift($this->smsSenders);
        $response = $sender->send($smsMessage);
        $sender->valid($response);
    }
}