<?php declare(strict_types=1);


namespace Sms\Utils;


use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Sms\Exceptions\SmsCodeException;
use Sms\Handlers\SmsMessage;

class SmsCodeHandler
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SmsMessage
     */
    protected $smsMessage;

    public function __construct(CacheInterface $cache, SmsMessage $smsMessage)
    {
        $this->cache = $cache;
        $this->smsMessage = $smsMessage;
    }

    /**
     * @param string $code
     * @param bool $ifSuccessDeleted 是否如果验证成功就直接删除缓存
     * @return bool
     * @throws SmsCodeException|InvalidArgumentException
     */
    public function check(string $code, bool $ifSuccessDeleted = true): bool
    {
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            $verifyCode = $this->cache->get($cacheKey);
            if ($code !== $verifyCode) {
                throw new SmsCodeException();
            }
        }
        
        $ifSuccessDeleted && $this->cache->deleteMultiple($cacheKeys);

        return true;
    }

    public function sent(int $duration, string $code):void
    {
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            $this->cache->set($cacheKey, $code, $duration);
        }
    }

    /**
     * @return string[]
     */
    protected function getCacheKeys():array
    {
        if ($this->smsMessage->isSingleMobile()) {
            $mobiles = [$this->smsMessage->getSingleMobile()];
        } else {
            $mobiles = $this->smsMessage->mobile;
        }

        $cacheKeys = [];
        foreach ($mobiles as $mobile) {
            $cacheKeys[] = "sms_code_verify_{$mobile}_{$this->smsMessage->smsMessageName}";
        }

        return $cacheKeys;
    }
}