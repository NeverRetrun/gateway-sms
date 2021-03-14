<?php declare(strict_types=1);

namespace Sms;


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

    public function __construct(array $smsSenders, ?CacheInterface $cache)
    {
        $this->smsSenders = $smsSenders;
        $this->cache = $cache;
    }

    /**
     * 从配置中创建工厂实例
     * @param SmsConfig[] $smsSenderConfigs
     * @param CacheInterface|null $cache
     * @return SmsSenderFactory
     */
    public static function createFromConfigs(array $smsSenderConfigs, ?CacheInterface $cache): SmsSenderFactory
    {
        $senders = [];
        foreach ($smsSenderConfigs as $senderConfig) {
            $senders[] = $senderConfig->createSender();
        }

        return new static($senders, $cache);
    }

    /**
     * 创建发送短信链
     * @return SmsSenderChain
     */
    public function createChain(): SmsSenderChain
    {
        return new SmsSenderChain($this->smsSenders, $this->cache);
    }
}