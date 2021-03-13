<?php


namespace Sms\Handlers;


use Psr\Http\Message\ResponseInterface;

interface SmsSenderInterface
{
    public function send(SmsMessage $smsMessage): ResponseInterface;
}