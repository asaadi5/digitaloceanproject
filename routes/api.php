<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate;

use App\Http\Controllers\Api\V1\FrontController as ApiFront;
use App\Http\Controllers\Api\V1\UserController  as ApiUser;
use App\Http\Controllers\Api\V1\AgentController as ApiAgent;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

/*
|--------------------------------------------------------------------------
| API V1 (Public + Protected)
|--------------------------------------------------------------------------
| ملاحظة:
| داخل الـ API استخدمنا الميدلوير الأصلي صراحةً:
|   [Authenticate::class . ':sanctum']
| بدل 'auth:sanctum' لتجنّب أي تصادم مع alias اسمه 'auth' في bootstrap/app.php
*/

Route::prefix('v1')->group(function () {
    // for pages header
    // Properties (مطابقة لمسارات الويب)
    Route::prefix('properties')->name('api.properties.')->group(function () {
        // /api/v1/properties
        Route::get('/', [ApiFront::class, 'property_search'])->name('index');

        // /api/v1/properties/featured  (لازم قبل الديناميكية)
        Route::get('/featured', [ApiFront::class, 'property_search'])->name('featured');

        // /api/v1/properties/sale | rent | wanted
        Route::get('/{purpose}', [ApiFront::class, 'property_search'])
            ->where('purpose', 'sale|rent|wanted')
            ->name('purpose');

        // /api/v1/properties/residential | commercial | recreational | lands [/type?]
        Route::get('/{category}/{type?}', [ApiFront::class, 'property_search'])
            ->where('category', 'residential|commercial|recreational|lands')
            ->name('category');

        // /api/v1/properties/sale/residential[/type?]
        Route::get('/{purpose}/{category}/{type?}', [ApiFront::class, 'property_search'])
            ->where([
                'purpose'  => 'sale|rent|wanted',
                'category' => 'residential|commercial|recreational|lands',
            ])->name('purpose_category');

        // أخيراً: /api/v1/properties/{slug} (موقع/محافظة...)
        Route::get('/{slug}', [ApiFront::class, 'property_search'])
            ->where('slug', '^(?!featured$|sale$|rent$|wanted$|residential$|commercial$|recreational$|lands$).+')
            ->name('location');
    });

    // Health
    Route::get('/test', fn () => response()->json(['status' => 'ok', 'message' => 'API working fine 🚀']));
    Route::get('/ping', fn () => response()->json(['ok' => true, 'env' => app()->environment()]));

    /* ---------- Front (public) ---------- */
    Route::get('/home', [ApiFront::class, 'index']);

    // Blog
    Route::get('/blog',                  [ApiFront::class, 'blog']);
    Route::get('/blog/{slug}',           [ApiFront::class, 'post']);
    Route::post('/blog/{post}/comments', [ApiFront::class, 'commentStore']);

    // Properties
    Route::get('/properties/search', [ApiFront::class, 'property_search']);

    // featured: نضمن دمج featured=1 حتى بدون كويري
    Route::get('/properties/featured', function (Request $request) {
        $request->merge(['featured' => 1, 'sort' => $request->query('sort', 'newest')]);
        return app(ApiFront::class)->property_search($request);
    });

    Route::get('/property/{slug}',        [ApiFront::class, 'property_detail']);
    Route::post('/property/{id}/message', [ApiFront::class, 'property_send_message']);

    // Locations & Agents
    Route::get('/locations',        [ApiFront::class, 'locations']);
    Route::get('/locations/{slug}', [ApiFront::class, 'location']);

    // تمرير slug كـ location_slug للبحث بدون تعديل الكنترولر
    Route::get('/locations/{slug}/properties', function (Request $request, $slug) {
        $request->merge(['location_slug' => $slug]);
        return app(ApiFront::class)->property_search($request);
    });

    Route::get('/agents',      [ApiFront::class, 'agents']);
    Route::get('/agents/{id}', [ApiFront::class, 'agent']);

    // Pages
    Route::get('/faqs',          [ApiFront::class, 'faq']);
    Route::get('/pricing',       [ApiFront::class, 'pricing']);
    Route::get('/pages/terms',   [ApiFront::class, 'terms']);
    Route::get('/pages/privacy', [ApiFront::class, 'privacy']);

    // Contact & Subscriber
    Route::post('/contact',                         [ApiFront::class, 'contact_submit']);
    Route::post('/subscriber',                      [ApiFront::class, 'subscriber_send_email']);
    Route::get ('/subscriber/verify/{email}/{token}', [ApiFront::class, 'subscriber_verify']);






    /* ---------- User Auth (public) ---------- */
    Route::post('/auth/register',              [ApiUser::class, 'register']);
    Route::get ('/auth/verify/{token}/{email}',[ApiUser::class, 'verify']);
    Route::post('/auth/login',                 [ApiUser::class, 'login']);
    Route::post('/auth/forgot',                [ApiUser::class, 'forgotPassword']);
    Route::post('/auth/reset',                 [ApiUser::class, 'resetPassword']);

    /* ---------- Protected (Sanctum) for User ---------- */
    Route::middleware([Authenticate::class . ':sanctum', CheckAbilities::class . ':user'])->group(function () {        // Session
        Route::post('/auth/logout', [ApiUser::class, 'logout']);
        Route::get ('/user',        [ApiUser::class, 'me']);
        Route::post('/user/profile',[ApiUser::class, 'updateProfile']);

        // Wishlist
        Route::post  ('/wishlist/{id}',      [ApiFront::class, 'wishlist_add']);
        Route::get   ('/user/wishlist',      [ApiUser::class, 'wishlistIndex']);
        Route::delete('/user/wishlist/{id}', [ApiUser::class, 'wishlistDelete']);

        // Messages
        Route::get   ('/user/messages',            [ApiUser::class, 'messages']);
        Route::post  ('/user/messages',            [ApiUser::class, 'messageStore']);
        Route::get   ('/user/messages/{id}',       [ApiUser::class, 'messageShow']);
        Route::post  ('/user/messages/{id}/reply', [ApiUser::class, 'messageReply']);
        Route::delete('/user/messages/{id}',       [ApiUser::class, 'messageDelete']);

        // Debug token
        Route::get('/token-check', function (Request $request) {
            return response()->json([
                'auth_user_id'  => optional($request->user())->id,
                'guard_user_id' => optional(auth()->user())->id,
                'ok'            => true,
            ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| API V1 (Agent)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/agent')->group(function () {
    // Agent Auth (public)
    Route::post('/auth/register',               [ApiAgent::class, 'register']);
    Route::get ('/auth/verify/{token}/{email}', [ApiAgent::class, 'verify']);
    Route::post('/auth/login',                  [ApiAgent::class, 'login']);

    // Protected (Sanctum) for Agent
    Route::middleware([Authenticate::class . ':sanctum', CheckAbilities::class . ':agent'])->group(function () {
        Route::post('/auth/logout', [ApiAgent::class, 'logout']);

        // Profile & Dashboard
        Route::get ('/me',        [ApiAgent::class, 'me']);
        Route::post('/profile',   [ApiAgent::class, 'updateProfile']);
        Route::get ('/dashboard', [ApiAgent::class, 'dashboard']);

        // Payments bootstrap (تجهيز جلسة الدفع)
        Route::post('/payments/paypal', [ApiAgent::class, 'paypalCreate']);
        Route::post('/payments/stripe', [ApiAgent::class, 'stripeCreate']);

        // Properties
        Route::get   ('/properties',      [ApiAgent::class, 'properties']);
        Route::post  ('/properties',      [ApiAgent::class, 'propertyStore']);
        Route::post  ('/properties/{id}', [ApiAgent::class, 'propertyUpdate']); // (يمكن إضافة PUT/PATCH أيضاً)
        Route::delete('/properties/{id}', [ApiAgent::class, 'propertyDelete']);

        // Gallery
        Route::get   ('/properties/{id}/photos', [ApiAgent::class, 'photos']);
        Route::post  ('/properties/{id}/photos', [ApiAgent::class, 'photoStore']);
        Route::delete('/photos/{photo_id}',      [ApiAgent::class, 'photoDelete']);

        Route::get   ('/properties/{id}/videos', [ApiAgent::class, 'videos']);
        Route::post  ('/properties/{id}/videos', [ApiAgent::class, 'videoStore']);
        Route::delete('/videos/{video_id}',      [ApiAgent::class, 'videoDelete']);

        // Messages
        Route::get ('/messages',            [ApiAgent::class, 'messages']);
        Route::get ('/messages/{id}',       [ApiAgent::class, 'messageShow']);
        Route::post('/messages/{id}/reply', [ApiAgent::class, 'messageReply']);
    });
});
