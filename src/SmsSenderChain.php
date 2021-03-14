<?php declare(strict_types=1);


namespace Sms;


use Psr\SimpleCache\CacheInterface;
use Sms\Exceptions\Exceptions;
use Sms\Exceptions\SmsSendException;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;
use Sms\Utils\RateLimiter;
use Sms\Utils\SmsCodeHandler;

class SmsSenderChain
{
    /**
     * @var SmsSender[]
     */
    protected $smsSenders;

    /**
     * @var callable[]
     */
    protected $middlewares;

    /**
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(array $smsSenders, CacheInterface $cache)
    {
        shuffle($smsSenders);
        $this->smsSenders = $smsSenders;
        $this->cache      = $cache;
    }

    /**
     * 根据网关发送短信
     * @param SmsMessage $smsMessage
     * @return SmsResult
     * @throws Exceptions
     */
    public function send(SmsMessage $smsMessage): SmsResult
    {
        $sendCallback = $this->getSendSmsCallback($smsMessage);

        $handle = array_reduce(
            $this->middlewares,
            function ($carry, callable $item) use($smsMessage) {
                return function () use($item, $carry, $smsMessage) {
                    return $item($carry, $smsMessage);
                };
            },
            $sendCallback
        );

        return $handle();
    }

    protected function getSendSmsCallback(SmsMessage $smsMessage): callable
    {
        return function () use ($smsMessage) {
            $exceptions = new Exceptions();
            $result     = new SmsResult($exceptions);
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
        };
    }

    /**
     * @param int $duration 时长 单位秒
     * @param int $sendMaxNumber 发送的最大限制
     * @return $this
     */
    public function rateLimit(int $duration, int $sendMaxNumber): SmsSenderChain
    {
        $this->middlewares[] = function (callable $sendSms, SmsMessage $smsMessage) use ($duration, $sendMaxNumber) {
            $rateLimiter = new RateLimiter($this->cache, $smsMessage);
            $rateLimiter->check($sendMaxNumber);
            $result = $sendSms();
            $rateLimiter->incr($duration);

            return $result;
        };

        return $this;
    }

    /**
     * 发送短信验证码缓存
     * @param int $duration
     * @param string $code
     */
    public function smsCode(int $duration, string $code): SmsSenderChain
    {
        $this->middlewares[] = function (callable $sendSms, SmsMessage $smsMessage) use ($duration, $code) {
            $rateLimiter = new SmsCodeHandler($this->cache, $smsMessage);
            $result      = $sendSms();
            $rateLimiter->sent($duration, $code);

            return $result;
        };

        return $this;
    }

    /**
     * 发送短信
     * @param SmsMessage $smsMessage
     */
    protected function sendBySender(SmsMessage $smsMessage): void
    {
        $sender   = array_shift($this->smsSenders);
        $response = $sender->send($smsMessage);
        $sender->valid($response);
    }
}