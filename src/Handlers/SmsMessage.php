<?php declare(strict_types=1);


namespace Sms\Handlers;


abstract class SmsMessage
{
    /**
     * @var string|string[] 接收短信的手机号码
     */
    public $mobile;

    /**
     * @var string 短信名称
     */
    public $smsMessageName;

    /**
     * 获取数组形式mobile
     * @return array
     */
    public function getMobileForArray(): array
    {
        return is_string($this->mobile)
            ? [$this->mobile]
            : $this->mobile;
    }

    /**
     * 判断是否是单个手机号码发送短信
     * @return bool
     */
    public function isSingleMobile(): bool
    {
        if (is_string($this->mobile)) {
            return true;
        }

        if (is_array($this->mobile) && count($this->mobile) === 1) {
            return true;
        }

        return false;
    }

    /**
     * 获取单个手机号
     * @return string
     */
    public function getSingleMobile(): string
    {
        if(is_array($this->mobile)) {
            return $this->mobile[0];
        }

        return $this->mobile;
    }
}