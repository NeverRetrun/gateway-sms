<?php declare(strict_types=1);


namespace Sms\Handlers\Tinree;

use Sms\Handlers\SmsMessage;

/**
 * 天瑞云短信实体
 * @see http://cms.tinree.com/static/index.html#/home/developer/interface/info/2
 * @package Sms\Handle\Tinree
 */
class TinreeSmsMessage extends SmsMessage
{
    public function __construct(
        string $smsMessageName,
        string $sign,
        string $templateId,
        $mobile,
        array $params = []
    )
    {
        $this->smsMessageName = $smsMessageName;
        $this->sign = $sign;
        $this->templateId = $templateId;
        $this->mobile = $mobile;
        $this->params = $params;
    }
}