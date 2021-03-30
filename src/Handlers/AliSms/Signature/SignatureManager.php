<?php declare(strict_types=1);


namespace Sms\Handlers\AliSms\Signature;


use Sms\Handlers\AliSms\Signature\Handlers\ShaHmac1Signature;

class SignatureManager
{
    /**
     * @var Signature[]
     */
    protected $handlers;

    public function __construct()
    {
        $this->handlers = [
            new ShaHmac1Signature()
        ];
    }

    /**
     * 获取签名类
     * @param string $signatureMethod
     * @return Signature
     */
    public function get(string $signatureMethod): Signature
    {
        foreach ($this->handlers as $handler) {
            if ( $handler->getMethod() === $signatureMethod ) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException('invalid method params');
    }
}