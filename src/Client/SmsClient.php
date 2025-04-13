<?php

namespace Hahadu\LaravelSms\Client;

use Exception;
use Hahadu\Sms\Client\SmsClient as BaseSmsClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class SmsClient
{
    private string $accessSecret;
    private string $accessKey;
    private string $signName;
    private string $service;
    private ?BaseSmsClient $client = null;

    /**
     * SmsClient constructor
     */
    public function __construct()
    {
        $this->set_secret(Config::get('sms.access_secret'))
             ->set_accessKey(Config::get('sms.access_key'))
             ->set_signName(Config::get('sms.sign_name'))
             ->set_service(Config::get('sms.service', 'Aliyun'));
    }

    /**
     * 设置访问密钥
     *
     * @param string|null $secret 密钥
     * @return $this
     */
    public function set_secret(?string $secret): self
    {
        $this->accessSecret = $secret ?? Config::get('sms.access_secret');
        return $this;
    }

    /**
     * 设置访问Key
     *
     * @param string|null $accessKey 访问Key
     * @return $this
     */
    public function set_accessKey(?string $accessKey): self
    {
        $this->accessKey = $accessKey ?? Config::get('sms.access_key');
        return $this;
    }

    /**
     * 设置签名名称
     *
     * @param string|null $signName 签名名称
     * @return $this
     */
    public function set_signName(?string $signName): self
    {
        $this->signName = $signName ?? Config::get('sms.sign_name');
        return $this;
    }

    /**
     * 设置短信服务提供商
     *
     * @param string|null $service 服务提供商
     * @return $this
     */
    public function set_service(?string $service): self
    {
        $this->service = $service ? strtolower($service) : Config::get('sms.service', 'Aliyun');
        return $this;
    }

    /**
     * 获取SMS客户端实例
     *
     * @return BaseSmsClient
     */
    protected function getClient(): BaseSmsClient
    {
        if ($this->client === null) {
            $this->client = new BaseSmsClient(
                $this->accessSecret,
                $this->accessKey,
                $this->signName,
                $this->service
            );
        }
        return $this->client;
    }

    /**
     * 发送短信
     *
     * @param array $params 短信参数
     * @return array
     * @throws Exception
     */
    public function send(array $params): array
    {
        if (Arr::get($params, 'SignName',null)!=null){
            $this->set_signName($params['SignName']);
        }
        try {
            $client = $this->getClient();

            // 发送短信
            $result = $client->send_sms(
                $params['PhoneNumbers'],
                //$params['SignName'] ?? $this->signName,
                $params['TemplateParam'],
                $params['TemplateCode'],

            );

            // 检查发送结果
            if (isset($result['Code']) && $result['Code'] !== 'OK') {
                throw new Exception($result['Message'] ?? '短信发送失败', 500);
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception('短信发送失败：' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 魔术方法，将调用转发到BaseSmsClient
     *
     * @param string $name 方法名
     * @param array $arguments 参数
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->getClient()->{$name}(...$arguments);
    }
}
