<?php declare(strict_types=1);


namespace Sms\Handlers;


interface SmsConfig
{
    /**
     * 创建对应的发送短信类
     * @return SmsSender
     */
    public function createSender(): SmsSender;
}