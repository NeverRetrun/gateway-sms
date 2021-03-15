<?php declare(strict_types=1);

namespace Sms;


use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsSender;

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
        $this->cache = $cache;
        $this->logger = $logger;
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
     * 创建发送短信链
     * @return SmsSenderChain
     */
    public function createChain(): SmsSenderChain
    {
        return new SmsSenderChain($this->smsSenders, $this->cache, $this->logger);
    }
}