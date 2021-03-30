<?php declare(strict_types=1);


namespace Sms\Handlers\AliSms;


class AliSmsMessage
{
    /**
     * @var string 签名
     */
    public $sign;

    /**
     * @var string 模版code
     */
    public $templateCode;

    /**
     * @var array 短信参数
     */
    public $params;

    public function __construct(
        string $sign,
        string $templateCode,
        array $params = []
    )
    {
        $this->sign         = $sign;
        $this->templateCode = $templateCode;
        $this->params       = $params;
    }

    public function getParamsToJson(): string
    {
        if (empty($this->params)) {
            return json_encode(new \stdClass());
        }

        return json_encode($this->params);
    }
}