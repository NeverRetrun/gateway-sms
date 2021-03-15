<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Psr\Http\Message\ResponseInterface;


class SmsSendException extends \RuntimeException implements StringAbleInterface
{
    /**
     * @var string 短信类型
     */
    public $type;

    /**
     * @var string 错误消息
     */
    public $message;

    /**
     * @var ResponseInterface 错误响应
     */
    public $response;

    public function __construct(string $type, string $message, ResponseInterface $response)
    {
        $this->type     = $type;
        $this->response = $response;
        $this->message = $message;
        parent::__construct($message, 0, null);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '短信发送失败：%s, 错误原因：%s, http响应：%s',
            $this->type,
            $this->message,
            $this->response->getBody()->getContents()
        );
    }

    public function toString(): string
    {
        return (string)$this;
    }
}