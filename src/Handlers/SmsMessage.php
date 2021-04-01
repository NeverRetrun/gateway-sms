<?php declare(strict_types=1);


namespace Sms\Handlers;


use App\Services\Sms\Exceptions\SmsException;
use Sms\Exceptions\InvalidMobileException;

abstract class SmsMessage
{
    /**
     * @var string|string[] 接收短信的手机号码
     */
    public $mobile;

    /**
     * @var string 短信名称
     */
    public $smsMessageName;

    /**
     * @param string|string[] $mobile
     */
    public function __construct($mobile)
    {
        if(is_int($mobile)) {
            $mobile = (string)$mobile;
        }

        $this->smsMessageName = $this->getDefaultSmsMessageName();
        $this->mobile = $this->conversionMobileType($mobile);

        if ($this->isMobileValid($this->getMobileForArray()) === false) {
            throw new InvalidMobileException();
        }
    }

    /**
     * 获取默认的短信名称
     * @return string
     */
    private function getDefaultSmsMessageName():string
    {
        $exploded = explode('\\', self::class);
        $className = array_pop($exploded);

        return "sms:{$className}";
    }

    /**
     * 转化手机号类型 只有string与string[]
     * @param mixed $mobile
     * @return string|string[]
     */
    private function conversionMobileType($mobile)
    {
        if (is_array($mobile)) {
            $result = [];

            foreach ($mobile as $item) {
                $result[] = (string)$item;
            }
            return $result;
        }

        if (is_int($mobile)) {
            return (string)$mobile;
        }

        return $mobile;
    }

    /**
     * 获取数组形式mobile
     * @return array
     */
    public function getMobileForArray(): array
    {
        return is_string($this->mobile)
            ? [$this->mobile]
            : $this->mobile;
    }

    /**
     * 判断是否是单个手机号码发送短信
     * @return bool
     */
    public function isSingleMobile(): bool
    {
        if (is_string($this->mobile)) {
            return true;
        }

        if (is_array($this->mobile) && count($this->mobile) === 1) {
            return true;
        }

        return false;
    }

    /**
     * 获取单个手机号
     * @return string
     */
    public function getSingleMobile(): string
    {
        if(is_array($this->mobile)) {
            return $this->mobile[0];
        }

        return $this->mobile;
    }

    /**
     * 判断手机号码
     * @param array $mobiles
     * @return bool
     */
    protected function isMobileValid(array $mobiles): bool
    {
        foreach ($mobiles as $mobile) {
            if (is_numeric($mobile) === false) {
                return false;
            }
        }

        return true;
    }
}