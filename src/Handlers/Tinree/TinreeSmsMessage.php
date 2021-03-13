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
    /**
     * @var string 平台上申请的接口短信签名或者签名ID
     */
    public $sign;

    /**
     * @var string 平台上申请的接口短信模板Id
     */
    public $templateId;

    /**
     * @var array 平台发送的短信内容是模板变量内容
     */
    public $params;

    public function __construct(
        string $sign,
        string $templateId,
        $mobile,
        array $params = []
    )
    {
        $this->sign = $sign;
        $this->templateId = $templateId;
        $this->mobile = $mobile;
        $this->params = $params;
    }
}