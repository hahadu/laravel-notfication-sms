<?php

namespace Hahadu\LaravelSms\Channels;

use Exception;
use Hahadu\LaravelSms\Client\SmsClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * SMS 客户端实例
     *
     * @var SmsClient
     */
    protected $client;

    /**
     * 默认的短信签名
     *
     * @var string
     */
    protected $signName;

    /**
     * 创建一个新的短信通道实例
     *
     * @param  SmsClient  $client
     * @param  string|null  $signName
     * @return void
     */
    public function __construct(SmsClient $client, ?string $signName = null)
    {
        $this->client = $client;
        $this->signName = $signName;
    }

    /**
     * 发送给定的通知
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|null
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            throw new Exception('Notification is missing toSms method.');
        }

        if (!$to = $notifiable->routeNotificationFor('sms', $notification)) {
            return null;
        }

        $message = $notification->toSms($notifiable);

        return $this->sendMessage($to, $message, $notification);
    }

    /**
     * 发送短信消息
     *
     * @param  string  $to
     * @param  mixed  $message
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function sendMessage($to, $message, $notification)
    {
        try {
            $params = [
                'PhoneNumbers' => $to,
                'SignName' => $message->from ?? $this->signName,
                'TemplateCode' => $message->template,
                'TemplateParam' => is_array($message->content)
                    ? json_encode($message->content, JSON_UNESCAPED_UNICODE)
                    : json_encode(['content' => $message->content], JSON_UNESCAPED_UNICODE),
            ];

            $result = $this->client->send($params);

            if ($message->statusCallback) {
                call_user_func($message->statusCallback, $result);
            }

            return $result;
        } catch (Exception $exception) {
            Log::error('SMS sending failed: ' . $exception->getMessage(), [
                'to' => $to,
                'template' => $message->template,
                'content' => $message->content,
                'exception' => $exception,
            ]);

            if ($message->errorCallback) {
                call_user_func($message->errorCallback, $exception);
            }

            throw $exception;
        }
    }
}
