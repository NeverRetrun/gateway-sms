<?php


namespace Sms\Handlers\AliSms\Signature;


interface Signature
{
    public function getMethod(): string;

    public function sign(string $string, string $accessKeySecret): string;
}