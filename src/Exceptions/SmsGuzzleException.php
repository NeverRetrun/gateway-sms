<?php declare(strict_types=1);


namespace Sms\Exceptions;


use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class SmsGuzzleException extends \Exception implements StringAbleInterface
{
    /**
     * @var GuzzleException
     */
    protected $exception;

    public function __construct(GuzzleException $exception)
    {
        parent::__construct(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }

    public function toString(): string
    {
        if ($this->exception instanceof ConnectException) {
            return sprintf(
                '短信发送失败，http请求失败。异常原因：%s',
                $this->exception->getMessage()
            );
        }

        if ($this->exception instanceof RequestException) {
            $response = $this->exception->getResponse();

            if ($response !== null) {
                $reason = $response->getReasonPhrase();
                $result = $response->getBody()->getContents();
            }

            return sprintf(
                '短信发送失败，http请求失败。异常原因：%s， 请求失败原因: %s, 请求结果：%s',
                $this->exception->getMessage(),
                $reason ?? '',
                $result ?? ''
            );
        }

        return '';
    }
}