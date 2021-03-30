<?php declare(strict_types=1);


namespace Sms\Utils;


class Random
{
    /**
     * 获取随机数
     * @param string $salt
     * @return string
     */
    public static function nonce(string $salt): string
    {
        return md5($salt . uniqid(md5( (string)microtime(true)), true)) . microtime();
    }

    /**
     * 获取ISO 8601时间
     * @return string
     */
    public static function getISO8601Time(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }
}