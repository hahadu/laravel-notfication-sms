<?php

namespace Hahadu\LaravelSms\Providers;

use Hahadu\LaravelSms\Channels\SmsChannel;
use Hahadu\LaravelSms\Client\SmsClient;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 注册短信客户端
        $this->app->singleton(SmsClient::class, function ($app) {
            return new SmsClient();
        });

        // 合并SMS配置
        $this->mergeConfigFrom(
            __DIR__.'/../../config/sms.php', 'sms'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 注册SMS通知通道
        Notification::extend('sms', function ($app, $signName = null) {
            if($signName==null)$signName=config('sms.sign_name');
            return new SmsChannel(
                $app->make(SmsClient::class),
                $signName
            );
        });

        // 发布配置文件
        $this->publishes([
            __DIR__.'/../../config/sms.php' => config_path('sms.php'),
        ], 'sms-config');
    }
}
