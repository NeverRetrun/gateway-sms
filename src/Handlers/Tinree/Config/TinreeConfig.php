<?php declare(strict_types=1);

namespace Sms\Handlers\Tinree\Config;

use Sms\Handlers\SmsConfig;
use Sms\Handlers\SmsSender;
use Sms\Handlers\Tinree\TinreeSender;

class TinreeConfig implements SmsConfig
{
    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var string
     */
    public $secret;

    /**
     * @see http://cms.tinree.com/static/index.html#/home/developer/interface/info/1
     * @param string $accessKey 平台分配给用户的access key，登录系统首页可点击"我的秘钥"查看
     * @param string $secret  平台分配给用户的secret，登录系统首页可点击"我的秘钥"查看
     */
    public function __construct(string $accessKey, string $secret)
    {
        $this->accessKey = $accessKey;
        $this->secret = $secret;
    }

    /**
     * 创建天瑞云短信类
     * @return SmsSender
     */
    public function createSender(): SmsSender
    {
        return new TinreeSender($this);
    }
}