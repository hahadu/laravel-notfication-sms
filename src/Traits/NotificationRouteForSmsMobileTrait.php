<?php

namespace Hahadu\LaravelSms\Traits;

use Illuminate\Notifications\Notification;

trait NotificationRouteForSmsMobileTrait
{
    public function routeNotificationForSms(Notification $notification): string
    {
        return $this->phone;
    }
}
