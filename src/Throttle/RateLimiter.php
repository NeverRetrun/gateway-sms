<?php declare(strict_types=1);


namespace Sms\Throttle;


use Psr\SimpleCache\CacheInterface;
use Sms\Exceptions\RateLimitException;
use Sms\Handlers\SmsMessage;

class RateLimiter
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 限制发送短信速率
     * @param SmsMessage $smsMessage
     * @param int $duration
     * @param int $sendMaxNumber
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function limit(SmsMessage $smsMessage, int $duration, int $sendMaxNumber): void
    {
        if ($sendMaxNumber <= 0) {
            throw new \InvalidArgumentException('send max number not lt 0');
        }

        /**
         * 这里不解决并发问题 不采用原子计数
         */
        $cacheKeys = $this->getCacheKeys($smsMessage);
        $cacheKeyWithSendNumberMap = [];
        foreach ($cacheKeys as $cacheKey) {
            $sendNumber = $this->cache->get($cacheKey, 0);

            if ($sendNumber >= $sendMaxNumber) {
                throw new RateLimitException($smsMessage);
            }

            $cacheKeyWithSendNumberMap[$cacheKey] = $sendNumber;
        }

        /**
         * TODO 这里每次设置过期时间会重置过期时间 这里先简单实现
         */
        foreach ($cacheKeys as $cacheKey) {
            $sendNumber = $cacheKeyWithSendNumberMap[$cacheKey];
            $this->cache->set($cacheKey, ++$sendNumber, $duration);
        }
    }



    /**
     * 获取缓存Key
     * @param SmsMessage $smsMessage
     * @return array
     */
    protected function getCacheKeys(SmsMessage $smsMessage): array
    {
        if ($smsMessage->isSingleMobile()) {
            $mobiles = [$smsMessage->mobile];
        } else {
            $mobiles = $smsMessage->mobile;
        }

        $cacheKeys = [];
        foreach ($mobiles as $mobile) {
            $cacheKeys[] = "sms_rate_limit_{$mobile}_{$smsMessage->getSmsMessageName()}";
        }

        return $cacheKeys;
    }
}