<?php

namespace Hahadu\LaravelSms\Message;

use Hahadu\LaravelSms\Client\SmsClient;

class SmsMessage
{
    /**
     * 短信内容
     *
     * @var array|string
     */
    public $content;

    /**
     * 短信模板代码
     *
     * @var string
     */
    public $template;

    /**
     * 短信发送方的签名
     *
     * @var string|null
     */
    public $from = null;

    /**
     * 客户端引用
     *
     * @var string|null
     */
    public $clientReference = null;

    /**
     * 状态回调
     *
     * @var callable|null
     */
    public $statusCallback = null;

    /**
     * 错误回调
     *
     * @var callable|null
     */
    public $errorCallback = null;

    /**
     * 自定义客户端
     *
     * @var SmsClient|null
     */
    public $client = null;

    /**
     * 创建一个新的消息实例
     *
     * @param  string|array  $content
     * @return void
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * 设置消息内容
     *
     * @param  string|array  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * 设置消息模板代码
     *
     * @param  string  $template
     * @return $this
     */
    public function template($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * 设置消息发送方的签名
     *
     * @param  string  $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * 设置客户端引用
     *
     * @param  string  $clientReference
     * @return $this
     */
    public function clientReference($clientReference)
    {
        $this->clientReference = $clientReference;

        return $this;
    }

    /**
     * 设置状态回调
     *
     * @param  callable  $callback
     * @return $this
     */
    public function statusCallback($callback)
    {
        $this->statusCallback = $callback;

        return $this;
    }

    /**
     * 设置错误回调
     *
     * @param  callable  $callback
     * @return $this
     */
    public function errorCallback($callback)
    {
        $this->errorCallback = $callback;

        return $this;
    }

    /**
     * 设置自定义客户端
     *
     * @param  SmsClient  $client
     * @return $this
     */
    public function client(SmsClient $client)
    {
        $this->client = $client;

        return $this;
    }
}
