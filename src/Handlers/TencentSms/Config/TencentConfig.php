<?php declare(strict_types=1);


namespace Sms\Handlers\TencentSms\Config;


use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsSender;
use Sms\Handlers\TencentSms\TencentSender;

class TencentConfig implements SmsConfig
{
    /**
     * @var string
     */
    public $secretId;

    /**
     * @var string
     */
    public $secretKey;

    /**
     * @see https://cloud.tencent.com/document/product/382/38768
     * @param string $secretKey 用于标识 API 调用者身份，可以简单类比为用户名。
     * @param string $secretId 用于验证 API 调用者的身份，可以简单类比为密码。
     */
    public function __construct(string $secretKey, string $secretId)
    {
        $this->secretKey = $secretKey;
        $this->secretId = $secretId;
    }

    /**
     * 创建发送短信类
     * @return SmsSender
     */
    public function createSender(): SmsSender
    {
        return new TencentSender($this);
    }
}