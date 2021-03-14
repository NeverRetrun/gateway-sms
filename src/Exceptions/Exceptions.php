<?php declare(strict_types=1);


namespace Sms\Exceptions;


use Exception;
use Throwable;

class Exceptions extends Exception
{
    /**
     * @var Exception[]
     */
    public $exceptions;

    public function __construct()
    {
        $this->exceptions = [];
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
}