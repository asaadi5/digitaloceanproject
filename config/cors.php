<?php

return [

    // فعّل CORS على مسارات API فقط
    'paths' => ['api/*'],

    // اسمح بكل الطرق (GET/POST/PUT/DELETE/OPTIONS…)
    'allowed_methods' => ['*'],

    // اقرأ الدومينات المسموح بها من .env (استخدم * للتجارب فقط)
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '*')))),

    'allowed_origins_patterns' => [],

    // اسمح بكل الهيدرز
    'allowed_headers' => ['*'],

    // هيدرز معروضة للمتصفح (اتركها فاضية غالبًا)
    'exposed_headers' => [],

    // مدة كاش للـ preflight بالثواني
    'max_age' => 3600,

    // Bearer tokens لا تحتاج Cookies
    'supports_credentials' => false,
];
