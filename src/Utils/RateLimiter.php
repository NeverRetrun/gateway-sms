<?php declare(strict_types=1);


namespace Sms\Utils;


use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Sms\Exceptions\RateLimitException;
use Sms\Handlers\SmsMessage;

class RateLimiter
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SmsMessage
     */
    protected $smsMessage;

    /**
     * @var string|null
     */
    protected $cachePrefixName;

    public function __construct(
        CacheInterface $cache,
        SmsMessage $smsMessage,
        ?string $cachePrefixName = null
    )
    {
        $this->cache = $cache;
        $this->smsMessage = $smsMessage;
        $this->cachePrefixName = $cachePrefixName;
    }

    /**
     * 限制发送短信速率
     * @param int $sendMaxNumber
     * @throws InvalidArgumentException
     */
    public function check(int $sendMaxNumber): void
    {
        if ($sendMaxNumber <= 0) {
            throw new \InvalidArgumentException('send max number not lt 0');
        }

        /**
         * 这里不解决并发问题 不采用原子计数
         */
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            $sendNumber = $this->cache->get($cacheKey, 0);

            if ($sendNumber >= $sendMaxNumber) {
                throw new RateLimitException($this->smsMessage);
            }
        }
    }

    /**
     * 自增
     * @param int $duration
     * @throws InvalidArgumentException
     */
    public function incr(int $duration): void
    {
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            $sendNumber = $this->cache->get($cacheKey, 0);
            $this->cache->set($cacheKey, ++$sendNumber, $duration);
        }
    }

    /**
     * 获取缓存Key
     * @return array
     */
    protected function getCacheKeys(): array
    {
        if ($this->smsMessage->isSingleMobile()) {
            $mobiles = [$this->smsMessage->getSingleMobile()];
        } else {
            $mobiles = $this->smsMessage->mobile;
        }

        $cachePrefixName = $this->cachePrefixName ?? 'sms_rate_limit_';

        $cacheKeys = [];
        foreach ($mobiles as $mobile) {
            $cacheKeys[] = "{$cachePrefixName}{$mobile}_{$this->smsMessage->smsMessageName}";
        }

        return $cacheKeys;
    }
}