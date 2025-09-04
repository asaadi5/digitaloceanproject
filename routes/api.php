<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FrontController as ApiFront;
use App\Http\Controllers\Api\V1\UserController as ApiUser;
use App\Http\Controllers\Api\V1\AgentController as ApiAgent;

Route::prefix('v1')->group(function () {

    // Home (نفس بيانات الصفحة الرئيسية لكن JSON)
    Route::get('/home', [ApiFront::class, 'index']);

    // Blog
    Route::get('/blog',                  [ApiFront::class, 'blog']);
    Route::get('/blog/{slug}',           [ApiFront::class, 'post']);
    Route::post('/blog/{post}/comments', [ApiFront::class, 'commentStore']);

    // Properties (بحث/تفاصيل/رسالة)
    Route::get('/properties/search',     [ApiFront::class, 'property_search']);           // مطابق لمنطق web
    Route::get('/properties/featured',   [ApiFront::class, 'property_search']);           // مرّر ?featured=1 من Flutter
    Route::get('/property/{slug}',       [ApiFront::class, 'property_detail']);           // يماثل web: /property/{slug}
    Route::post('/property/{id}/message',[ApiFront::class, 'property_send_message']);

    // ملاحظة: لو تحب نفس أسلوب web للموقع:
    // web: GET /properties/{slug} => property_search للـ location
    // API: وفّر alias صريح لتقليل اللبس:
    Route::get('/locations/{slug}/properties', [ApiFront::class, 'property_search']);      // استخدم query: location_slug

    // Locations & Agents
    Route::get('/locations',        [ApiFront::class, 'locations']);
    Route::get('/locations/{slug}', [ApiFront::class, 'location']);
    Route::get('/agents',           [ApiFront::class, 'agents']);
    Route::get('/agents/{id}',      [ApiFront::class, 'agent']);

    // FAQs & Pages & Pricing
    Route::get('/faqs',          [ApiFront::class, 'faq']);
    Route::get('/pricing',       [ApiFront::class, 'pricing']);
    Route::get('/pages/terms',   [ApiFront::class, 'terms']);
    Route::get('/pages/privacy', [ApiFront::class, 'privacy']);

    // Contact & Subscriber
    Route::post('/contact', [ApiFront::class, 'contact_submit']);
    Route::post('/subscriber', [ApiFront::class, 'subscriber_send_email']);
    Route::get('/subscriber/verify/{email}/{token}', [ApiFront::class, 'subscriber_verify']);

    // Wishlist (محمية بـ Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/wishlist/{id}', [ApiFront::class, 'wishlist_add']);
    });
});

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/register', [ApiUser::class, 'register']);
    Route::get ('/auth/verify/{token}/{email}', [ApiUser::class, 'verify']); // يستعمل نفس رابط الويب
    Route::post('/auth/login',    [ApiUser::class, 'login']);
    Route::post('/auth/forgot',   [ApiUser::class, 'forgotPassword']);
    Route::post('/auth/reset',    [ApiUser::class, 'resetPassword']);

    // Protected (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [ApiUser::class, 'logout']);
        Route::get ('/user',        [ApiUser::class, 'me']);
        Route::post('/user/profile',[ApiUser::class, 'updateProfile']);

        // Wishlist
        Route::get   ('/user/wishlist',      [ApiUser::class, 'wishlistIndex']);
        Route::delete('/user/wishlist/{id}', [ApiUser::class, 'wishlistDelete']);

        // Messages
        Route::get   ('/user/messages',            [ApiUser::class, 'messages']);
        Route::post  ('/user/messages',            [ApiUser::class, 'messageStore']);
        Route::get   ('/user/messages/{id}',       [ApiUser::class, 'messageShow']);
        Route::post  ('/user/messages/{id}/reply', [ApiUser::class, 'messageReply']);
        Route::delete('/user/messages/{id}',       [ApiUser::class, 'messageDelete']);
    });
});

Route::prefix('v1/agent')->group(function () {
    // Auth
    Route::post('/auth/register', [ApiAgent::class, 'register']);
    Route::get ('/auth/verify/{token}/{email}', [ApiAgent::class, 'verify']);
    Route::post('/auth/login',    [ApiAgent::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [ApiAgent::class, 'logout']);

        // Profile & Dashboard
        Route::get ('/me',         [ApiAgent::class, 'me']);
        Route::post('/profile',    [ApiAgent::class, 'updateProfile']);
        Route::get ('/dashboard',  [ApiAgent::class, 'dashboard']);

        // Payments bootstrap (open approve/checkout URL in webview)
        Route::post('/payments/paypal', [ApiAgent::class, 'paypalCreate']);
        Route::post('/payments/stripe', [ApiAgent::class, 'stripeCreate']);

        // Properties
        Route::get   ('/properties',            [ApiAgent::class, 'properties']);
        Route::post  ('/properties',            [ApiAgent::class, 'propertyStore']);
        Route::post  ('/properties/{id}',       [ApiAgent::class, 'propertyUpdate']);
        Route::delete('/properties/{id}',       [ApiAgent::class, 'propertyDelete']);

        // Gallery
        Route::get   ('/properties/{id}/photos', [ApiAgent::class, 'photos']);
        Route::post  ('/properties/{id}/photos', [ApiAgent::class, 'photoStore']);
        Route::delete('/photos/{photo_id}',      [ApiAgent::class, 'photoDelete']);

        Route::get   ('/properties/{id}/videos', [ApiAgent::class, 'videos']);
        Route::post  ('/properties/{id}/videos', [ApiAgent::class, 'videoStore']);
        Route::delete('/videos/{video_id}',      [ApiAgent::class, 'videoDelete']);

        // Messages
        Route::get   ('/messages',               [ApiAgent::class, 'messages']);
        Route::get   ('/messages/{id}',          [ApiAgent::class, 'messageShow']);
        Route::post  ('/messages/{id}/reply',    [ApiAgent::class, 'messageReply']);
    });
});
