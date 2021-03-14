<?php declare(strict_types=1);


namespace Sms\Handlers\TencentSms;


use Sms\Handlers\SmsMessage;

/**
 * @see https://cloud.tencent.com/document/product/382/38778
 */
class TencentSmsMessage
{
    /**
     * @var string 短信SdkAppId在 添加应用后生成的实际SdkAppId，示例如1400006666。
     */
    public $smsSdkAppId;

    /**
     * @var string 签名
     */
    public $sign;

    /**
     * @var string 模版id
     */
    public $templateId;

    /**
     * @var array 短信参数
     */
    public $params;

    public function __construct(
        string $sign,
        string $templateId,
        string $smsSdkAppId,
        array $params = []
    )
    {
        $this->sign        = $sign;
        $this->templateId  = $templateId;
        $this->params      = $params;
        $this->smsSdkAppId = $smsSdkAppId;
    }
}