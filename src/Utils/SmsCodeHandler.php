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

    /**
     * @var array
     */
    protected $testMobiles;

    public function __construct(
        CacheInterface $cache,
        SmsMessage $smsMessage,
        array $testMobiles = []
    )
    {
        $this->cache       = $cache;
        $this->smsMessage  = $smsMessage;
        $this->testMobiles = $testMobiles;
    }

    /**
     * @param string $code
     * @param bool $ifSuccessDeleted 是否如果验证成功就直接删除缓存
     * @return bool
     * @throws SmsCodeException|InvalidArgumentException
     */
    public function check(string $code, bool $ifSuccessDeleted = true): bool
    {
        if ($this->isTestMobile()) {
            return true;
        }

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

    public function sent(int $duration, string $code): void
    {
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            $this->cache->set($cacheKey, $code, $duration);
        }
    }

    /**
     * @return string[]
     */
    protected function getCacheKeys(): array
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

    /**
     * 是否是测试账号
     * @return bool
     */
    protected function isTestMobile(): bool
    {
        return in_array(
            $this->smsMessage->getSingleMobile(),
            $this->testMobiles
        );
    }
}