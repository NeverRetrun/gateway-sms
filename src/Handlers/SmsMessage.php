<?php declare(strict_types=1);


namespace Sms\Handlers;


abstract class SmsMessage
{
    /**
     * @var string|string[] 接收短信的手机号码
     */
    public $mobile;

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

    /**
     * 获取数组形式mobile
     * @return array
     */
    public function getMobileForArray():array
    {
        return is_string($this->mobile)
            ? [$this->mobile]
            : $this->mobile;
    }

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