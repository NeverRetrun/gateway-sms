<?php declare(strict_types=1);


namespace Sms\Handlers\TencentSms;


use Sms\Handlers\SmsMessage;

/**
 * @see https://cloud.tencent.com/document/product/382/38778
 */
class TencentSmsMessage extends SmsMessage
{
    /**
     * @var string 短信SdkAppId在 添加应用后生成的实际SdkAppId，示例如1400006666。
     */
    public $smsSdkAppId;

    public function __construct(
        string $sign,
        string $templateId,
        $mobile,
        string $smsSdkAppId,
        array $params = []
    )
    {
        $this->sign = $sign;
        $this->templateId = $templateId;
        $this->mobile = $mobile;
        $this->params = $params;
        $this->smsSdkAppId = $smsSdkAppId;
    }
}