<?php declare(strict_types=1);


namespace Sms\Handlers\AliSms\Signature\Handlers;


use Sms\Handlers\AliSms\Signature\Signature;

class ShaHmac1Signature implements Signature
{
    public function getMethod(): string
    {
        return 'HMAC-SHA1';
    }

    public function sign(string $string, string $accessKeySecret): string
    {
        return base64_encode(
            hash_hmac('sha1', $string, $accessKeySecret, true)
        );
    }
}