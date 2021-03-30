<?php


namespace Sms\Handlers\AliSms;


interface AliAble
{
    public function toAliSmsMessage(): AliSmsMessage;
}