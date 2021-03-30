<?php declare(strict_types=1);


namespace Sms;


use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Sms\Exceptions\Exceptions;
use Sms\Exceptions\SmsGuzzleException;
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
    protected $middlewares = [];

    /**
     * @var null|CacheInterface
     */
    protected $cache;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    public function __construct(
        array $smsSenders,
        ?CacheInterface $cache,
        ?LoggerInterface $logger
    )
    {
        shuffle($smsSenders);
        $this->smsSenders = $smsSenders;
        $this->cache      = $cache;
        $this->logger     = $logger;
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
            function ($carry, callable $item) use ($smsMessage) {
                return function () use ($item, $carry, $smsMessage) {
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
            $exceptions = new Exceptions($this->logger);
            $result     = new SmsResult($exceptions, null);
            while (count($this->smsSenders) !== 0) {
                try {
                    [
                        'response' => $response,
                        'type' => $type
                    ] = $this->sendBySender($smsMessage);
                } catch (SmsSendException $sendException) {
                    $exceptions->appendException($sendException);
                    continue;
                } catch (GuzzleException $exception) {
                    $exceptions->appendException(new SmsGuzzleException($exception));
                    continue;
                }

                $result->isSuccess   = true;
                $result->successType = $type;
                break;
            }

            if ($result->isSuccess === false) {
                $exceptions->log();
                throw $exceptions;
            }

            $exceptions->log();
            return $result;
        };
    }

    /**
     * @param int $duration 时长 单位秒
     * @param int $sendMaxNumber 发送的最大限制
     * @param string $cachePrefixName
     * @return $this
     */
    public function rateLimit(int $duration, int $sendMaxNumber, string $cachePrefixName): SmsSenderChain
    {
        $this->middlewares[] =
            function (callable $sendSms, SmsMessage $smsMessage)
            use ($duration, $sendMaxNumber, $cachePrefixName) {
                $rateLimiter = new RateLimiter($this->cache, $smsMessage, $cachePrefixName);
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
     * @return array
     */
    protected function sendBySender(SmsMessage $smsMessage): array
    {
        $sender   = array_shift($this->smsSenders);
        $response = $sender->send($smsMessage);

        $sender->valid($response);

        return [
            'type' => $sender->getTypePhrase(),
            'response' => $response
        ];
    }
}