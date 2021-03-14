<?php declare(strict_types=1);


namespace Sms\Handlers\Tinree;


/**
 * 天瑞云短信实体
 * @see http://cms.tinree.com/static/index.html#/home/developer/interface/info/2
 * @package Sms\Handle\Tinree
 */
class TinreeSmsMessage
{
    /**
     * @var string 签名
     */
    public $sign;

    /**
     * @var string 模版id
     */
    public $templateId;

    /**
     * @var array 参数 一维数组
     */
    public $params;


    public function __construct(
        string $sign,
        string $templateId,
        array $params = []
    )
    {
        $this->sign       = $sign;
        $this->templateId = $templateId;
        $this->params     = $params;
    }
}