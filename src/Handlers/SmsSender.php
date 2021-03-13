<?php declare(strict_types=1);


namespace Sms\Handlers;


use Psr\Http\Message\ResponseInterface;
use Sms\Exceptions\SmsSendException;

abstract class SmsSender implements SmsSenderInterface
{
    const HTTP_OK = 200;

    /**
     * 验证请求是否异常
     * @param ResponseInterface $response
     * @throws SmsSendException
     */
    public function valid(ResponseInterface $response): void
    {
        $this->assertResponseHttpException($response);
        $this->assertResponseException($response);
    }

    /**
     * 断言请求异常
     * @param ResponseInterface $response
     */
    abstract protected function assertResponseException(ResponseInterface $response): void;

    /**
     * 获取类型短语
     * @return string
     */
    abstract public function getTypePhrase(): string;

    /**
     * 断言请求HTTP异常
     * @param ResponseInterface $response
     */
    protected function assertResponseHttpException(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== self::HTTP_OK) {
            throw new SmsSendException(
                $this->getTypePhrase(),
                $response->getReasonPhrase(),
                $response
            );
        }
    }
}