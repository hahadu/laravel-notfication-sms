# Laravel SMS Notification 使用指南

这个包提供了一个简单的方式在 Laravel 应用中发送短信通知。

## 快速开始

### 1. 配置短信服务

在 `config/sms.php` 中配置您的短信服务参数，或在 `.env` 文件中设置：

```env
# 短信服务配置
SMS_SERVICE=Aliyun
SMS_ACCESS_SECRET=your_access_secret
SMS_ACCESS_KEY=your_access_key
SMS_SIGN_NAME=your_sign_name
```

### 2. 创建通知类

```php
<?php

namespace App\Notifications;

use Hahadu\LaravelSms\SmsNotification;
use Hahadu\LaravelSms\Message\SmsMessage;

class VerificationCodeNotification extends SmsNotification
{
    private $code;

    public function __construct(string $code)
    {
        // 传入模板参数和模板代码
        parent::__construct(
            ['code' => $code],  // 模板参数
            'SMS_TEMPLATE_CODE' // 模板代码
        );

        $this->code = $code;
    }

    // 可选：自定义短信内容
    public function toSms($notifiable): SmsMessage
    {
        return (new SmsMessage())
            ->content(['code' => $this->code])
            ->template('SMS_TEMPLATE_CODE')
            ->from('YourApp'); // 可选：自定义签名
    }
}
```

### 3. 配置模型

在需要接收短信通知的模型中（如 User 模型）：

```php
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // 添加路由方法指定接收短信的手机号
    public function routeNotificationForSms($notification)
    {
        return $this->phone; // 返回手机号字段
    }
}
```

### 4. 发送通知

```php
// 方式1：直接发送
$user->notify(new VerificationCodeNotification('123456'));

// 方式2：延迟发送
$user->notify((new VerificationCodeNotification('123456'))->delay(now()->addMinutes(10)));
```

## 高级用法

### 自定义短信内容和回调

```php
use Hahadu\LaravelSms\SmsNotification;
use Hahadu\LaravelSms\Message\SmsMessage;

class OrderShippedNotification extends SmsNotification
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct(
            [
                'order_no' => $order->number,
                'shipping_company' => $order->shipping_company,
                'tracking_number' => $order->tracking_number
            ],
            'SMS_ORDER_SHIPPED'
        );

        $this->order = $order;
    }

    public function toSms($notifiable): SmsMessage
    {
        return (new SmsMessage())
            ->content([
                'order_no' => $this->order->number,
                'shipping_company' => $this->order->shipping_company,
                'tracking_number' => $this->order->tracking_number
            ])
            ->template('SMS_ORDER_SHIPPED')
            ->statusCallback(function ($result) {
                // 发送成功的回调
                Log::info('SMS sent successfully', [
                    'order_id' => $this->order->id,
                    'result' => $result
                ]);
            })
            ->errorCallback(function ($exception) {
                // 发送失败的回调
                Log::error('SMS sending failed', [
                    'order_id' => $this->order->id,
                    'error' => $exception->getMessage()
                ]);
            });
    }
}
```

### 批量发送

```php
use Illuminate\Support\Facades\Notification;

// 发送给多个用户
$users = User::whereIn('id', [1, 2, 3])->get();
Notification::send($users, new VerificationCodeNotification('123456'));
```

### 自定义短信客户端

如果需要在运行时修改短信配置：

```php
use Hahadu\LaravelSms\Client\SmsClient;

$smsClient = new SmsClient();
$smsClient->set_accessKey('new_access_key')
         ->set_secret('new_secret')
         ->set_signName('new_sign_name')
         ->set_service('Aliyun');

// 在发送短信时使用自定义客户端
(new SmsMessage())
    ->client($smsClient)
    ->content(['code' => '123456'])
    ->template('SMS_TEMPLATE_CODE');
```

## 常见问题

1. **模板参数格式**
   - 参数必须是数组形式
   - 键名必须与短信模板中的变量名一致

2. **错误处理**
   - 所有的发送错误都会抛出异常
   - 建议使用 try-catch 捕获并处理异常

```php
try {
    $user->notify(new VerificationCodeNotification('123456'));
} catch (\Exception $e) {
    // 处理发送失败
    Log::error('SMS sending failed', [
        'error' => $e->getMessage(),
        'user_id' => $user->id
    ]);
}
```

3. **队列配置**
   - 通知默认使用队列发送
   - 确保已配置队列驱动
   - 可以通过 `php artisan queue:work` 处理队列

## 调试建议

1. 开发环境中查看日志：
```bash
tail -f storage/logs/laravel.log
```

2. 测试短信发送：
```php
// 创建测试路由
Route::get('/test/sms', function () {
    $user = App\Models\User::find(1);
    $user->notify(new VerificationCodeNotification('123456'));
    return 'SMS sent';
});
```

## 注意事项

1. 确保配置了正确的短信服务凭证
2. 注意短信模板的参数匹配
3. 建议使用队列发送短信
4. 注意处理发送失败的情况
5. 在生产环境中妥善保护短信配置信息
