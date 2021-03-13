<?php


namespace Sms\Handlers;


interface SmsSender
{
    public function send(SmsMessage $smsMessage): void;
}