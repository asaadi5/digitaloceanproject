<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Wishlist;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer(['front.*','user.*'], function ($view) {
            $ids = [];
            if (Auth::check()) {
                $uid = Auth::id();
                $ids = Cache::remember("wishids:$uid", 300, function () use ($uid) {
                    return Wishlist::where('user_id',$uid)->pluck('property_id')->toArray();
                });
            }
            $view->with('wishIds', $ids); // متاحة في كل القوالب
        });

        // Share the setting data with all views
        $setting_data = Setting::where('id', 1)->first();
        view()->share('global_setting', $setting_data);
        app()->setLocale('ar');     // توحيد لغة التطبيق
        Carbon::setLocale('ar');    // للـ diffForHumans والترجمات
        Date::setLocale('ar');      // نفس Carbon (واجهة Laravel)
        // إن كنت تستعمل دوال PHP التقليدية للتواريخ:
        setlocale(LC_TIME, 'ar_AR.utf8', 'ar_SA.utf8', 'ar.utf8', 'ar');    }
}
