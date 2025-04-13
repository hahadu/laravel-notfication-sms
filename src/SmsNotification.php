<?php

namespace Hahadu\LaravelSms;

use Hahadu\LaravelSms\Message\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $content;
    protected $templateCode;

    /**
     * Create a new notification instance.
     *
     * @param string $content
     * @param string $templateCode
     */
    public function __construct(array|string $content, string $templateCode)
    {
        $this->content = $content;
        $this->templateCode = $templateCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['sms'];
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SmsMessage
     */
    public function toSms(mixed $notifiable):SmsMessage
    {
        return (new SmsMessage())
            ->content($this->content)
            ->template($this->templateCode);
    }
}
