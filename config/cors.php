<?php

return [

    // فعّل CORS على كل مسارات الـ API
    'paths' => ['api/*'],

    // اسمح بكل الطرق (GET/POST/PUT/DELETE/OPTIONS…)
    'allowed_methods' => ['*'],

    // الأصول (الدومينات) المسموح لها – نقرأها من .env
    // للتجربة استخدم *، وللإنتاج حدّد الدومينات
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '*')))),

    'allowed_origins_patterns' => [],

    // اسمح بكل الهيدرز
    'allowed_headers' => ['*'],

    // هيدرز معروضة للمتصفح (اتركها فاضية غالبًا)
    'exposed_headers' => [],

    // مدة كاش للـ preflight بالثواني
    'max_age' => 3600,

    // طالما سنستخدم Bearer Token حالياً، خَلِّيه false (بدون كوكيز)
    'supports_credentials' => false,
];
