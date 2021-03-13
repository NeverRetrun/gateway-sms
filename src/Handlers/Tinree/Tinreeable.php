<?php


namespace Sms\Handlers\Tinree;



interface Tinreeable
{
    public function toTinreeSmsMessage(): TinreeSmsMessage;
}