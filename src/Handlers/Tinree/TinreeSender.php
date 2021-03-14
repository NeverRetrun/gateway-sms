<?php declare(strict_types=1);

namespace Sms\Handlers\Tinree;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Sms\Exceptions\InvalidSmsMessage;
use Sms\Exceptions\SmsSendException;
use Sms\Handlers\SmsMessage;
use Sms\Handlers\SmsSender;
use Sms\Handlers\Tinree\Config\TinreeConfig;

class TinreeSender extends SmsSender
{
    const BASE_URI = 'http://api.tinree.com/api/v2/';

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var TinreeConfig 天瑞云配置项
     */
    protected $config;

    public function __construct(TinreeConfig $config)
    {
        $this->http = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 1,
        ]);

        $this->config = $config;
    }

    /**
     * 发送天瑞云短信
     * @param SmsMessage $smsMessage
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(SmsMessage $smsMessage): ResponseInterface
    {
        if ($smsMessage instanceof TinreeAble === false) {
            throw new InvalidSmsMessage('invalid tinree sms message');
        }

        $tinreeSmsMessage = $smsMessage->toTinreeSmsMessage();

        if ($smsMessage->isSingleMobile()) {
            $response = $this->sendSingleSms($smsMessage, $tinreeSmsMessage);
        } else {
            $response = $this->sendBatchSms($smsMessage, $tinreeSmsMessage);
        }

        return $response;
    }

    /**
     * 发送批量短信
     * @param SmsMessage $smsMessage
     * @param TinreeSmsMessage $tinreeSmsMessage
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function sendBatchSms(
        SmsMessage $smsMessage,
        TinreeSmsMessage $tinreeSmsMessage
    ): ResponseInterface
    {
        return $this->http->request(
            'POST',
            'send',
            [
                'form_params' => [
                    'accesskey' => $this->config->accessKey,
                    'secret' => $this->config->secret,
                    'sign' => $tinreeSmsMessage->sign,
                    'templateId' => $tinreeSmsMessage->templateId,
                    'mobile' => implode(',', $smsMessage->mobile),
                    'content' => implode('##', $tinreeSmsMessage->params),
                ],
            ]
        );
    }

    /**
     * 发送单条短信
     * @param SmsMessage $smsMessage
     * @param TinreeSmsMessage $tinreeSmsMessage
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function sendSingleSms(
        SmsMessage $smsMessage,
        TinreeSmsMessage $tinreeSmsMessage
    ): ResponseInterface
    {
        return $this->http->request(
            'POST',
            'single_send',
            [
                'form_params' => [
                    'accesskey' => $this->config->accessKey,
                    'secret' => $this->config->secret,
                    'sign' => $tinreeSmsMessage->sign,
                    'templateId' => $tinreeSmsMessage->templateId,
                    'mobile' => $smsMessage->getSingleMobile(),
                    'content' => implode('##', $tinreeSmsMessage->params),
                ],
            ]
        );
    }

    /**
     * 断言响应异常
     * @param ResponseInterface $response
     */
    protected function assertResponseException(ResponseInterface $response): void
    {
        $map                  = [
            '9001' => '签名格式不正确',
            '9002' => '参数未赋值',
            '9003' => '手机号码格式不正确',
            '9006' => '用户accesskey不正确',
            '9007' => 'IP白名单限制',
            '9009' => '短信内容参数不正确',
            '9010' => '用户短信余额不足',
            '9011' => '用户帐户异常',
            '9012' => '日期时间格式不正确',
            '9013' => '不合法的语音验证码，4~8位的数字',
            '9014' => '超出了最大手机号数量',
            '9015' => '不支持的国家短信',
            '9016' => '无效的签名或者签名ID',
            '9017' => '无效的模板ID',
            '9018' => '单个变量限制为1-20个字',
            '9019' => '内容不可以为空',
            '9021' => '主叫和被叫号码不能相同',
            '9022' => '手机号码不能为空',
            '9023' => '手机号码黑名单',
            '9024' => '手机号码超频',
            '10001' => '内容包含敏感词',
            '10002' => '内容包含屏蔽词',
            '10003' => '错误的定时时间',
            '10004' => '自定义扩展只能是数字且长度不能超过4位',
            '10005' => '模版类型不存在',
            '10006' => '模版和内容不匹配',
        ];
        $responseContentArray = json_decode($response->getBody()->getContents(), true);
        $errMessage           = $map[$responseContentArray['code']] ?? null;
        if ($errMessage === null) {
            return;
        }

        throw new SmsSendException(
            $this->getTypePhrase(),
            $errMessage,
            $response
        );
    }

    public function getTypePhrase(): string
    {
        return '天瑞云';
    }
}