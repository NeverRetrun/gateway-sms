<?php declare(strict_types=1);


namespace Sms\Handlers;


abstract class SmsMessage
{
    /**
     * @var string|string[] 接收短信的手机号码
     */
    public $mobile;

    /**
     * 判断是否是单个手机号码发送短信
     * @return bool
     */
    public function isSingleMobile():bool
    {
        if (is_string($this->mobile)) {
            return true;
        }

        if (is_array($this->mobile) && count($this->mobile) === 1) {
            return true;
        }

        return false;
    }
}