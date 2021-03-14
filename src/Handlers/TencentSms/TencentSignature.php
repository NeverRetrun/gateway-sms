<?php declare(strict_types=1);


namespace Sms\Handlers\TencentSms;


use GuzzleHttp\Psr7\Request;
use Sms\Handlers\TencentSms\Config\TencentConfig;

class TencentSignature
{
    /**
     * @var Request 请求
     */
    protected $request;

    /**
     * @var TencentConfig 腾讯配置
     */
    protected $config;

    public function __construct(Request $request, TencentConfig $config)
    {
        $this->request = $request;
        $this->config  = $config;
    }

    /**
     * @return string
     */
    public function getAuthorization():string
    {
        $signatureParam = $this->signature();

        return $signatureParam['algo'] .
            " Credential=" . $this->config->secretId . "/" . $signatureParam['credentialScope'] .
            ", SignedHeaders=content-type;host, Signature=" . $signatureParam['signature'];
    }

    /**
     * @return array
     */
    public function signature():array
    {
        $canonicalHeaders = "content-type:" . $this->request->getHeaderLine("Content-Type") . "\n" .
            "host:" . $this->request->getHeaderLine("Host") . "\n";
        $signedHeaders    = "content-type;host";
        $canonicalRequest = $this->request->getMethod() . "\n" .
            $this->request->getUri()->getPath() . "\n" .
            '' . "\n" .
            $canonicalHeaders . "\n" .
            $signedHeaders . "\n" .
            hash("SHA256", (string)$this->request->getBody());

        $algo            = "TC3-HMAC-SHA256";
        $date            = gmdate("Y-m-d", (int)$this->request->getHeaderLine("X-TC-Timestamp"));
        $service         = explode(".", $this->request->getHeaderLine('Host'))[0];
        $credentialScope = $date . "/" . $service . "/tc3_request";
        $str2sign        = $algo . "\n" .
            $this->request->getHeaderLine("X-TC-Timestamp") . "\n" .
            $credentialScope . "\n" .
            hash("SHA256", $canonicalRequest);

        $signature = $this->signTC3($date, $service, $str2sign);

        return [
            'algo' => $algo,
            'credentialScope' => $credentialScope,
            'signature' => $signature,
        ];
    }

    /**
     * TC3 签名
     * @param string $date
     * @param string $service
     * @param string $str2sign
     * @return string
     */
    protected function signTC3(string $date, string $service, string $str2sign)
    {
        $dateKey    = hash_hmac("SHA256", $date, "TC3" . $this->config->secretKey, true);
        $serviceKey = hash_hmac("SHA256", $service, $dateKey, true);
        $reqKey     = hash_hmac("SHA256", "tc3_request", $serviceKey, true);
        return hash_hmac("SHA256", $str2sign, $reqKey);
    }
}