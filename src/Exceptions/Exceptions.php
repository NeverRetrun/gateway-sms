<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Exception;
use Psr\Log\LoggerInterface;

class Exceptions extends Exception
{
    /**
     * @var Exception|StringAbleInterface[]
     */
    public $exceptions;

    /**
     * @var null|LoggerInterface
     */
    public $logger;

    public function __construct(?LoggerInterface $logger)
    {
        $this->exceptions = [];
        $this->logger     = $logger;
        parent::__construct('exceptions', 0, null);
    }

    /**
     * 获取第一个异常
     * @return Exception
     */
    public function first(): ?Exception
    {
        if (!isset($this->exceptions[0])) {
            return null;
        }

        return $this->exceptions[0];
    }

    /**
     * 获取所有异常
     * @return Exception[]
     */
    public function all(): array
    {
        return $this->exceptions;
    }

    /**
     * 追加异常
     * @param Exception $exception
     * @return $this
     */
    public function appendException(Exception $exception): Exceptions
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * 记录日志
     */
    public function log(): void
    {
        if ($this->logger === null) {
            return;
        }

        foreach ($this->exceptions as $exception) {
            $this->logger->error($exception->toString());
        }
    }
}