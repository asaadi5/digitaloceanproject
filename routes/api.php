<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\TypeController;
use App\Http\Controllers\Api\V1\AmenityController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\AgentController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\TestimonialController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\SubscriberController;
use App\Http\Controllers\Api\V1\Setting\LogoController;
use App\Http\Controllers\Api\V1\Setting\FaviconController;
use App\Http\Controllers\Api\V1\Setting\BannerController;
use App\Http\Controllers\Api\V1\Setting\FooterController;
use App\Http\Controllers\Api\V1\FrontController;
use App\Http\Controllers\Api\V1\UserController as ApiUserController;;


// صحّة
Route::get('/health', fn() => response()->json(['status' => 'ok']));

Route::prefix('v1')->group(function () {
    // عامة (بدون توكن)
    Route::post('/login',               [ApiUserController::class, 'login_submit']);
    Route::post('/registration',        [ApiUserController::class, 'registration_submit']);
    Route::get ('/registration-verify', [ApiUserController::class, 'registration_verify']);
    Route::post('/forget-password',     [ApiUserController::class, 'forget_password_submit']);
    Route::post('/reset-password',      [ApiUserController::class, 'reset_password_submit']);

    // محمية (تحتاج توكن)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard',  [ApiUserController::class, 'dashboard']);
        Route::get('/profile',    [ApiUserController::class, 'profile']);
        Route::post('/profile',   [ApiUserController::class, 'profile_submit']);
        // ...
        Route::post('/logout',    [ApiUserController::class, 'logout']);
    });
});
