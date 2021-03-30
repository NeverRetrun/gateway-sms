<?php declare(strict_types=1);


namespace Sms\Handlers\TencentSms;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Sms\Exceptions\InvalidSmsMessage;
use Sms\Exceptions\SmsSendException;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;
use Sms\Handlers\TencentSms\Config\TencentConfig;

class TencentSender extends SmsSender
{
    const HOST = 'sms.tencentcloudapi.com';
    const BASE_URI = 'https://sms.tencentcloudapi.com';

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var TencentConfig 腾讯云配置项
     */
    protected $config;

    public function __construct(TencentConfig $config)
    {
        $this->http = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 1,
        ]);

        $this->config = $config;
    }


    public function send(SmsMessage $smsMessage): ResponseInterface
    {
        if ($smsMessage instanceof TencentAble === false) {
            throw new InvalidSmsMessage('invalid tencent sms message');
        }

        return $this->sendSms(
            $smsMessage,
            $smsMessage->toTencentSmsMessage()
        );
    }

    protected function sendSms(SmsMessage $smsMessage, TencentSmsMessage $tencentSmsMessage): ResponseInterface
    {
        $request = new Request(
            'POST',
            '/',
            [
                'Host' => self::HOST,
                'X-TC-Action' => 'SendSms',
                'X-TC-Version' => '2019-07-11',
                'X-TC-Region' => 'ap-shanghai',
                'X-TC-Timestamp' => time(),
                'Content-Type' => 'application/json'
            ],
            json_encode(
                [
                    'PhoneNumberSet' => $this->getAppendedAreaCodeMobiles($smsMessage->getMobileForArray()),
                    'TemplateID' => $tencentSmsMessage->templateId,
                    'SmsSdkAppid' => $tencentSmsMessage->smsSdkAppId,
                    'Sign' => $tencentSmsMessage->sign,
                    'TemplateParamSet' => $tencentSmsMessage->params,
                ]
                , JSON_UNESCAPED_UNICODE
            )
        );

        $authorization = (new TencentSignature($request, $this->config))
            ->getAuthorization();

        $request = $request->withHeader('Authorization', $authorization);

        return $this->http->send($request);
    }

    /**
     * @param ResponseInterface $response
     */
    protected function assertResponseException(ResponseInterface $response): void
    {
        $tmpResponse = json_decode($response->getBody()->getContents(), true)["Response"];
        if (array_key_exists("Error", $tmpResponse)) {
            throw new SmsSendException(
                $this->getTypePhrase(),
                $tmpResponse["Error"]["Message"],
                $response
            );
        }
    }

    /**
     * 获取追加地区码的手机号
     * @param array $mobiles
     * @return array
     */
    protected function getAppendedAreaCodeMobiles(array $mobiles): array
    {
        $result = [];
        foreach ($mobiles as $mobile) {
            $result[] = "+86$mobile";
        }

        return $result;
    }

    public function getTypePhrase(): string
    {
        return '腾讯云';
    }
}