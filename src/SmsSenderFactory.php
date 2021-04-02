<?php declare(strict_types=1);

namespace Sms;


use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;
use Sms\Utils\SmsCodeHandler;

class SmsSenderFactory
{
    /**
     * @var SmsSender[]
     */
    protected $smsSenders;

    /**
     * @var null|CacheInterface
     */
    protected $cache;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $testMobiles = [];

    /**
     * SmsSenderFactory constructor.
     * @param array $smsSenders
     * @param CacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        array $smsSenders,
        ?CacheInterface $cache,
        ?LoggerInterface $logger
    )
    {
        $this->smsSenders = $smsSenders;
        $this->cache      = $cache;
        $this->logger     = $logger;
    }

    /**
     * 从配置中创建工厂实例
     * @param SmsConfig[] $smsSenderConfigs
     * @param CacheInterface|null $cache
     * @param LoggerInterface|null $logger
     * @return SmsSenderFactory
     */
    public static function createFromConfigs(
        array $smsSenderConfigs,
        ?CacheInterface $cache,
        ?LoggerInterface $logger
    ): SmsSenderFactory
    {
        $senders = [];
        foreach ($smsSenderConfigs as $senderConfig) {
            $senders[] = $senderConfig->createSender();
        }

        return new static($senders, $cache, $logger);
    }

    /**
     * @param array $testMobiles
     * @return SmsSenderFactory
     */
    public function setTestMobiles(array $testMobiles): SmsSenderFactory
    {
        $this->testMobiles = $testMobiles;
        return $this;
    }

    /**
     * 创建发送短信链
     * @return SmsSenderChain
     */
    public function createChain(): SmsSenderChain
    {
        return new SmsSenderChain($this->smsSenders, $this->cache, $this->logger);
    }

    /**
     * 创建短信验证码帮助类
     * @param SmsMessage $smsMessage
     * @return SmsCodeHandler
     */
    public function createSmsCodeHandler(SmsMessage $smsMessage): SmsCodeHandler
    {
        return new SmsCodeHandler(
            $this->cache,
            $smsMessage,
            $this->testMobiles
        );
    }
}