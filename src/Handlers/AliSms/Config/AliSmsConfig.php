<?php declare(strict_types=1);

namespace Sms\Handlers\AliSms\Config;


use Sms\Handlers\AliSms\AliSmsSender;
use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsSender;

class AliSmsConfig implements SmsConfig
{
    /**
     * @var string
     */
    public $accessKeyId;

    /**
     * @var string
     */
    public $accessSecret;

    /**
     * @see https://help.aliyun.com/document_detail/101339.html?spm=a2c4g.11186623.6.617.4eb21a23OBq7E6
     * @param string $accessKeyId 用于标识 API 调用者身份，可以简单类比为用户名。
     * @param string $accessSecret 用于验证 API 调用者的身份，可以简单类比为密码。
     */
    public function __construct(string $accessKeyId, string $accessSecret)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessSecret = $accessSecret;
    }

    public function createSender(): SmsSender
    {
        return new AliSmsSender($this);
    }
}