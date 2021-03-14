<?php declare(strict_types=1);

namespace Sms;


use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsSender;

class SmsSenderFactory
{
    /**
     * @var SmsSender[]
     */
    protected $smsSenders;

    public function __construct(array $smsSenders)
    {
        $this->smsSenders = $smsSenders;
    }

    /**
     * 从配置中创建工厂实例
     * @param SmsConfig[] $smsSenderConfigs
     * @return SmsSenderFactory
     */
    public static function createFromConfigs(array $smsSenderConfigs): SmsSenderFactory
    {
        $senders = [];
        foreach ($smsSenderConfigs as $senderConfig) {
            $senders[] = $senderConfig->createSender();
        }

        return new static($senders);
    }

    /**
     * 创建发送短信链
     * @return SmsSenderChain
     */
    public function createChain(): SmsSenderChain
    {
        return new SmsSenderChain($this->smsSenders);
    }
}