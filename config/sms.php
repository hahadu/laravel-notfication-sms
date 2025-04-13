<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS 服务配置
    |--------------------------------------------------------------------------
    |
    | 这里可以配置短信服务的相关设置，包括访问密钥、访问键、签名名称等
    |
    */

    // 默认短信服务提供商
    'service' => env('SMS_SERVICE', 'Aliyun'),

    // 访问密钥
    'access_secret' => env('SMS_ACCESS_SECRET', ''),

    // 访问键
    'access_key' => env('SMS_ACCESS_KEY', ''),

    // 签名名称
    'sign_name' => env('SMS_SIGN_NAME', ''),

    // 短信模板
    'templates' => [
        'verify_code' => env('SMS_TEMPLATE_VERIFY_CODE', ''),
        'notification' => env('SMS_TEMPLATE_NOTIFICATION', ''),
        // 可以添加更多模板
    ],
];
