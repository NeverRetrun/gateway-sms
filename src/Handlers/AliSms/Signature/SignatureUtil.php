<?php declare(strict_types=1);


namespace Sms\Handlers\AliSms\Signature;


class SignatureUtil
{
    /**
     * @param string $string
     *
     * @return null|string
     */
    private static function percentEncode(string $string): string
    {
        $result = urlencode($string);
        $result = str_replace(['+', '*'], ['%20', '%2A'], $result);
        $result = preg_replace('/%7E/', '~', $result);

        return $result;
    }

    /**
     * @param array $parameters
     * @param string $method
     * @return string
     */
    public static function convertString(array $parameters, string $method = 'POST'): string
    {
        ksort($parameters);
        $canonicalized = '';
        foreach ($parameters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $canonicalized .= '&' . self::percentEncode($key) . '=' . self::percentEncode($value);
        }

        return $method . '&%2F&' . self::percentEncode(substr($canonicalized, 1));
    }
}