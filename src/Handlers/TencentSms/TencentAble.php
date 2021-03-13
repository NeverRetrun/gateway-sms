<?php


namespace Sms\Handlers\TencentSms;


interface TencentAble
{
    public function toTencentSmsMessage(): TencentSmsMessage;
}