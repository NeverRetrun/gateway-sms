<?php declare(strict_types=1);


namespace Sms\Handlers\AliSms;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Sms\Exceptions\InvalidSmsMessage;
use Sms\Exceptions\SmsSendException;
use Sms\Handlers\AliSms\Config\AliSmsConfig;
use Sms\Handlers\AliSms\Signature\SignatureManager;
use Sms\Handlers\AliSms\Signature\SignatureUtil;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;
use Sms\Utils\Random;

class AliSmsSender extends SmsSender
{
    const SUCCESS = 'OK';
    const BASE_URI = 'http://dysmsapi.aliyuncs.com';

    /**
     * @var AliSmsConfig
     */
    protected $config;

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var SignatureManager
     */
    protected $signatureManager;

    public function __construct(AliSmsConfig $config)
    {
        $this->http             = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 1,
        ]);
        $this->signatureManager = new SignatureManager();
        $this->config           = $config;
    }

    protected function assertResponseException(ResponseInterface $response): void
    {
        $tmpResponse = json_decode($response->getBody()->getContents(), true);

        if ($tmpResponse['Message'] !== self::SUCCESS || $tmpResponse['Code'] !== self::SUCCESS) {
            throw new SmsSendException(
                $this->getTypePhrase(),
                $tmpResponse["Message"],
                $response
            );
        }
    }

    /**
     * 发送短信
     * @param SmsMessage $smsMessage
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(SmsMessage $smsMessage): ResponseInterface
    {
        if ($smsMessage instanceof AliAble === false) {
            throw new InvalidSmsMessage('invalid ali sms message');
        }

        $messageConfig = $smsMessage->toAliSmsMessage();

        $options = [
            'PhoneNumbers' => implode(',', $smsMessage->getMobileForArray()),
            'SignName' => $messageConfig->sign,
            'TemplateCode' => $messageConfig->templateCode,
            'TemplateParam' => $messageConfig->getParamsToJson(),
            'RegionId' => 'cn-hangzhou',
            'Format' => 'JSON',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => Random::nonce('SendSms'),
            'Timestamp' => Random::getISO8601Time(),
            'Action' => 'SendSms',
            'AccessKeyId' => $this->config->accessKeyId,
            'Version' => '2017-05-25',
        ];

        $options['Signature'] = $this->signatureManager
            ->get($options['SignatureMethod'])
            ->sign(
                SignatureUtil::convertString($options),
                $this->config->accessSecret . '&'
            );

        return $this->http->request(
            'POST',
            'send',
            [
                'form_params' => $options
            ]
        );
    }

    public function getTypePhrase(): string
    {
        return '阿里云';
    }
}