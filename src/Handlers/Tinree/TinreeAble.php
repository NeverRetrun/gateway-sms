<?php


namespace Sms\Handlers\Tinree;


interface TinreeAble
{
    public function toTinreeSmsMessage(): TinreeSmsMessage;
}