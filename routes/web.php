<?php

use Illuminate\Support\Facades\Route;

// Front Controllers
use App\Http\Controllers\Front\FrontController;

// User Controllers
use App\Http\Controllers\User\UserController;

// Agent Controllers
use App\Http\Controllers\Agent\AgentController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminTypeController;
use App\Http\Controllers\Admin\AdminAmenityController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminAgentController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminSubscriberController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminSettingController;

/*
|--------------------------------------------------------------------------
| Front-End Routes
|--------------------------------------------------------------------------
| الغرض: صفحات الواجهة العامة (البيت، البحث، التدوينات، ...).
| ملاحظة: أبقينا نفس الأسماء والروابط بلا أي تغيير وظيفي.
| English: Preserve URLs and route names; only re-ordered for clarity and safety.
*/

// الصفحة الرئيسية + صفحات ثابتة
Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/contact', [FrontController::class, 'contact'])->name('contact');
Route::post('/contact-submit', [FrontController::class, 'contact_submit'])->name('contact_submit');
Route::get('/select-user', [FrontController::class, 'select_user'])->name('select_user');
Route::get('/pricing', [FrontController::class, 'pricing'])->name('pricing');

// تفاصيل عقار + مراسلة المعلن
Route::get('/property/{slug}', [FrontController::class, 'property_detail'])->name('property_detail');
Route::post('/property/message/{id}', [FrontController::class, 'property_send_message'])
    ->whereNumber('id') // English: Property IDs are numeric
    ->name('property_send_message');

// مواقع + وكلاء + تدوينات + صفحات معلومات
Route::get('/locations', [FrontController::class, 'locations'])->name('locations');
Route::get('/agents', [FrontController::class, 'agents'])->name('agents');
Route::get('/agent/detail/{id}', [FrontController::class, 'agent'])
    ->whereNumber('id')
    ->name('agent');

Route::get('/blog', [FrontController::class, 'blog'])->name('blog');
Route::get('/post/{slug}', [FrontController::class, 'post'])->name('post');
Route::post('/post/{post}/comment', [FrontController::class, 'commentStore'])->name('post.comment.store');

Route::get('/about_us', [FrontController::class, 'about_us'])->name('about_us');
Route::get('/faq', [FrontController::class, 'faq'])->name('faq');
Route::get('/terms', [FrontController::class, 'terms'])->name('terms');
Route::get('/privacy', [FrontController::class, 'privacy'])->name('privacy');

// المفضلة
Route::get('/wishlist-add/{id}', [FrontController::class, 'wishlist_add'])
    ->whereNumber('id')
    ->name('wishlist_add');

// البحث العام (واجهة قديمة/مباشرة)
Route::get('/property-search', [FrontController::class, 'property_search'])->name('property_search');

/*
|--------------------------------------------------------------------------
| Properties Search (Prefixed Group)
|--------------------------------------------------------------------------
| الغرض: تنظيم مسارات /properties لسلوك البحث المرن.
| ملاحظة مهمة: عرّفنا /properties/featured قبل /properties/{slug} لمنع التعارض.
*/

// Featured أولاً لمنع ابتلاعها من {slug}
Route::get('/properties/featured', [FrontController::class, 'property_search'])
    ->name('properties_featured');

// مجموعة /properties الأساسية
Route::prefix('properties')->name('properties.')->group(function () {
    // صفحة البحث العامة: /properties
    Route::get('/', [FrontController::class, 'property_search'])->name('index');

    // الغرض فقط: /properties/sale أو /properties/rent
    Route::get('/{purpose}', [FrontController::class, 'property_search'])
        ->where('purpose', 'sale|rent')
        ->name('purpose');

    // فئة رئيسية (+ نوع اختياري): /properties/residential أو /properties/residential/apartment
    Route::get('/{category}/{type?}', [FrontController::class, 'property_search'])
        ->where('category', 'residential|commercial|recreational|lands')
        ->name('category');

    // غرض + فئة + نوع (اختياري): /properties/sale/residential/apartment
    Route::get('/{purpose}/{category}/{type?}', [FrontController::class, 'property_search'])
        ->where([
            'purpose'  => 'sale|rent',
            'category' => 'residential|commercial|recreational|lands',
        ])
        ->name('purpose_category');
});

// ملاحظة: نضع /properties/{slug} في النهاية ليكون catch-all آمن للمواقع (Location Slug)
Route::get('/properties/{slug}', [FrontController::class, 'property_search'])->name('location');

/*
|--------------------------------------------------------------------------
| Subscriber (Newsletter) Routes
|--------------------------------------------------------------------------
| الغرض: اشتراك البريد والتحقق.
*/
Route::post('/subscriber/send-email', [FrontController::class, 'subscriber_send_email'])->name('subscriber_send_email');
Route::get('/subscriber/verify/{email}/{token}', [FrontController::class, 'subscriber_verify'])->name('subscriber_verify');

/*
|--------------------------------------------------------------------------
| User (Customer) Routes
|--------------------------------------------------------------------------
| الغرض: لوحة المستخدم ومراسلاته والمفضلة.. إلخ. تتطلب تسجيل الدخول.
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'profile_submit'])->name('profile_submit');

    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle', [UserController::class, 'toggle'])->name('wishlist_toggle');
    Route::get('/wishlist-delete/{id}', [UserController::class, 'wishlist_delete'])
        ->whereNumber('id')
        ->name('wishlist_delete');

    Route::get('/message/index', [UserController::class, 'message'])->name('message');
    Route::get('/message/create', [UserController::class, 'message_create'])->name('message_create');
    Route::post('/message/store', [UserController::class, 'message_store'])->name('message_store');
    Route::get('/message/reply/{id}', [UserController::class, 'message_reply'])
        ->whereNumber('id')
        ->name('message_reply');
    Route::post('/message/reply-submit/{message_id}/{agent_id}', [UserController::class, 'message_reply_submit'])
        ->whereNumber('message_id')
        ->whereNumber('agent_id')
        ->name('message_reply_submit');
    Route::get('/message/delete/{id}', [UserController::class, 'message_delete'])
        ->whereNumber('id')
        ->name('message_delete');
});

// تسجيل/دخول المستخدمين
Route::get('/registration', [UserController::class, 'registration'])->name('registration');
Route::post('/registration', [UserController::class, 'registration_submit'])->name('registration_submit');
Route::get('/registration-verify/{token}/{email}', [UserController::class, 'registration_verify'])->name('registration_verify');
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'login_submit'])->name('login_submit');
Route::get('/forget-password', [UserController::class, 'forget_password'])->name('forget_password');
Route::post('/forget-password', [UserController::class, 'forget_password_submit'])->name('forget_password_submit');
Route::get('/reset-password/{token}/{email}', [UserController::class, 'reset_password'])->name('reset_password');
Route::post('/reset-password/{token}/{email}', [UserController::class, 'reset_password_submit'])->name('reset_password_submit');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
| الغرض: لوحة وإدارة العقارات لوكيل العقار. تتطلب Middleware 'agent' للمحمية.
*/

// مساحة الوكيل المحمية
Route::middleware('agent')->prefix('agent')->group(function () {
    Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('agent_dashboard');
    Route::get('/profile', [AgentController::class, 'profile'])->name('agent_profile');
    Route::post('/profile', [AgentController::class, 'profile_submit'])->name('agent_profile_submit');

    Route::get('/order', [AgentController::class, 'order'])->name('agent_order');
    Route::get('/payment', [AgentController::class, 'payment'])->name('agent_payment');
    Route::get('/invoice/{order_id}', [AgentController::class, 'invoice'])
        ->whereNumber('order_id')
        ->name('agent_invoice');

    // مدفوعات
    Route::post('/paypal', [AgentController::class, 'paypal'])->name('agent_paypal');
    Route::get('/paypal-success', [AgentController::class, 'paypal_success'])->name('agent_paypal_success');
    Route::get('/paypal-cancel', [AgentController::class, 'paypal_cancel'])->name('agent_paypal_cancel');

    Route::post('/stripe', [AgentController::class, 'stripe'])->name('agent_stripe');
    Route::get('/stripe-success', [AgentController::class, 'stripe_success'])->name('agent_stripe_success');
    Route::get('/stripe-cancel', [AgentController::class, 'stripe_cancel'])->name('agent_stripe_cancel');

    // إدارة العقارات
    Route::get('/property/index', [AgentController::class, 'property_all'])->name('agent_property_index');
    Route::get('/property/create', [AgentController::class, 'property_create'])->name('agent_property_create');
    Route::post('/property/store', [AgentController::class, 'property_store'])->name('agent_property_store');
    Route::get('/property/edit/{id}', [AgentController::class, 'property_edit'])
        ->whereNumber('id')
        ->name('agent_property_edit');
    Route::get('/property/show/{id}', [AgentController::class, 'property_show'])
        ->whereNumber('id')
        ->name('agent_property_show');

    Route::post('/property/update/{id}', [AgentController::class, 'property_update'])
        ->whereNumber('id')
        ->name('agent_property_update');
    Route::get('/property/delete/{id}', [AgentController::class, 'property_delete'])
        ->whereNumber('id')
        ->name('agent_property_delete');

    Route::get('/property/photo-gallery/{property_id}', [AgentController::class, 'property_photo_gallery'])
        ->whereNumber('property_id')
        ->name('agent_property_photo_gallery');
    Route::post('/property/photo-gallery/{property_id}', [AgentController::class, 'property_photo_gallery_store'])
        ->whereNumber('property_id')
        ->name('agent_property_photo_gallery_store');
    Route::get('/property/photo-gallery-delete/{property_photo_id}', [AgentController::class, 'property_photo_gallery_delete'])
        ->whereNumber('property_photo_id')
        ->name('agent_property_photo_gallery_delete');

    Route::get('/property/video-gallery/{property_id}', [AgentController::class, 'property_video_gallery'])
        ->whereNumber('property_id')
        ->name('agent_property_video_gallery');
    Route::post('/property/video-gallery/{property_id}', [AgentController::class, 'property_video_gallery_store'])
        ->whereNumber('property_id')
        ->name('agent_property_video_gallery_store');
    Route::get('/property/video-gallery-delete/{property_video_id}', [AgentController::class, 'property_video_gallery_delete'])
        ->whereNumber('property_video_id')
        ->name('agent_property_video_gallery_delete');

    // رسائل الوكيل
    Route::get('/message/index', [AgentController::class, 'message'])->name('agent_message');
    Route::get('/message/reply/{id}', [AgentController::class, 'message_reply'])
        ->whereNumber('id')
        ->name('agent_message_reply');
    Route::post('/message/reply-submit/{message_id}/{agent_id}', [AgentController::class, 'message_reply_submit'])
        ->whereNumber('message_id')
        ->whereNumber('agent_id')
        ->name('agent_message_reply_submit');
    Route::delete('/message-reply/{replyId}', [AgentController::class, 'message_reply_delete'])
        ->whereNumber('replyId')
        ->name('agent_message_reply_delete');
    Route::delete('/message/{id}', [AgentController::class, 'message_delete'])
        ->whereNumber('id')
        ->name('agent_message_delete');
});

// مسارات دخول/تسجيل الوكيل (بدون Middleware)
Route::prefix('agent')->group(function () {
    Route::get('/', fn () => redirect()->route('agent_login'));
    Route::get('/registration', [AgentController::class, 'registration'])->name('agent_registration');
    Route::post('/registration', [AgentController::class, 'registration_submit'])->name('agent_registration_submit');
    Route::get('/registration-verify/{token}/{email}', [AgentController::class, 'registration_verify'])->name('agent_registration_verify');

    Route::get('/login', [AgentController::class, 'login'])->name('agent_login');
    Route::post('/login', [AgentController::class, 'login_submit'])->name('agent_login_submit');

    Route::get('/forget-password', [AgentController::class, 'forget_password'])->name('agent_forget_password');
    Route::post('/forget-password', [AgentController::class, 'forget_password_submit'])->name('agent_forget_password_submit');

    Route::get('/reset-password/{token}/{email}', [AgentController::class, 'reset_password'])->name('agent_reset_password');
    Route::post('/reset-password/{token}/{email}', [AgentController::class, 'reset_password_submit'])->name('agent_reset_password_submit');

    Route::get('/logout', [AgentController::class, 'logout'])->name('agent_logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| الغرض: لوحة وإدارة النظام. مجموعة محمية بـ Middleware 'admin'.
*/

// مساحة الأدمن المحمية
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin_profile');
    Route::post('/profile', [AdminController::class, 'profile_submit'])->name('admin_profile_submit');

    // الحزم
    Route::get('/package/index', [AdminPackageController::class, 'index'])->name('admin_package_index');
    Route::get('/package/create', [AdminPackageController::class, 'create'])->name('admin_package_create');
    Route::post('/package/store', [AdminPackageController::class, 'store'])->name('admin_package_store');
    Route::get('/package/edit/{id}', [AdminPackageController::class, 'edit'])->name('admin_package_edit');
    Route::post('/package/update/{id}', [AdminPackageController::class, 'update'])->name('admin_package_update');
    Route::get('/package/delete/{id}', [AdminPackageController::class, 'delete'])->name('admin_package_delete');

    // المواقع
    Route::get('/location/index', [AdminLocationController::class, 'index'])->name('admin_location_index');
    Route::get('/location/create', [AdminLocationController::class, 'create'])->name('admin_location_create');
    Route::post('/location/store', [AdminLocationController::class, 'store'])->name('admin_location_store');
    Route::get('/location/edit/{id}', [AdminLocationController::class, 'edit'])->name('admin_location_edit');
    Route::post('/location/update/{id}', [AdminLocationController::class, 'update'])->name('admin_location_update');
    Route::get('/location/delete/{id}', [AdminLocationController::class, 'delete'])->name('admin_location_delete');

    // الأنواع/الفئات
    Route::get('/type/index', [AdminTypeController::class, 'index'])->name('admin_type_index');
    Route::get('/type/create', [AdminTypeController::class, 'create'])->name('admin_type_create');
    Route::post('/type/store', [AdminTypeController::class, 'store'])->name('admin_type_store');
    Route::get('/type/edit/{id}', [AdminTypeController::class, 'edit'])->name('admin_type_edit');
    Route::post('/type/update/{id}', [AdminTypeController::class, 'update'])->name('admin_type_update');
    Route::get('/type/delete/{id}', [AdminTypeController::class, 'delete'])->name('admin_type_delete');

    // المزايا
    Route::get('/amenity/index', [AdminAmenityController::class, 'index'])->name('admin_amenity_index');
    Route::get('/amenity/create', [AdminAmenityController::class, 'create'])->name('admin_amenity_create');
    Route::post('/amenity/store', [AdminAmenityController::class, 'store'])->name('admin_amenity_store');
    Route::get('/amenity/edit/{id}', [AdminAmenityController::class, 'edit'])->name('admin_amenity_edit');
    Route::post('/amenity/update/{id}', [AdminAmenityController::class, 'update'])->name('admin_amenity_update');
    Route::get('/amenity/delete/{id}', [AdminAmenityController::class, 'delete'])->name('admin_amenity_delete');

    // العملاء
    Route::get('/customer/index', [AdminCustomerController::class, 'index'])->name('admin_customer_index');
    Route::get('/customer/create', [AdminCustomerController::class, 'create'])->name('admin_customer_create');
    Route::post('/customer/store', [AdminCustomerController::class, 'store'])->name('admin_customer_store');
    Route::get('/customer/edit/{id}', [AdminCustomerController::class, 'edit'])->name('admin_customer_edit');
    Route::post('/customer/update/{id}', [AdminCustomerController::class, 'update'])->name('admin_customer_update');
    Route::get('/customer/delete/{id}', [AdminCustomerController::class, 'delete'])->name('admin_customer_delete');

    // الوكلاء
    Route::get('/agent/index', [AdminAgentController::class, 'index'])->name('admin_agent_index');
    Route::get('/agent/create', [AdminAgentController::class, 'create'])->name('admin_agent_create');
    Route::post('/agent/store', [AdminAgentController::class, 'store'])->name('admin_agent_store');
    Route::get('/agent/edit/{id}', [AdminAgentController::class, 'edit'])->name('admin_agent_edit');
    Route::post('/agent/update/{id}', [AdminAgentController::class, 'update'])->name('admin_agent_update');
    Route::get('/agent/delete/{id}', [AdminAgentController::class, 'delete'])->name('admin_agent_delete');

    // الطلبات
    Route::get('/order/index', [AdminOrderController::class, 'index'])->name('admin_order_index');
    Route::get('/order/invoice/{order_id}', [AdminOrderController::class, 'invoice'])
        ->whereNumber('order_id')
        ->name('admin_order_invoice');

    // العقارات
    Route::get('/property/index', [AdminPropertyController::class, 'index'])->name('admin_property_index');
    Route::get('/property/detail/{id}', [AdminPropertyController::class, 'detail'])
        ->whereNumber('id')
        ->name('admin_property_detail');
    Route::get('/property/change-status/{id}', [AdminPropertyController::class, 'change_status'])
        ->whereNumber('id')
        ->name('admin_property_change_status');
    Route::get('/property/delete/{id}', [AdminPropertyController::class, 'delete'])
        ->whereNumber('id')
        ->name('admin_property_delete');

    // الشهادات
    Route::get('/testimonial/index', [AdminTestimonialController::class, 'index'])->name('admin_testimonial_index');
    Route::get('/testimonial/create', [AdminTestimonialController::class, 'create'])->name('admin_testimonial_create');
    Route::post('/testimonial/store', [AdminTestimonialController::class, 'store'])->name('admin_testimonial_store');
    Route::get('/testimonial/edit/{id}', [AdminTestimonialController::class, 'edit'])->name('admin_testimonial_edit');
    Route::post('/testimonial/update/{id}', [AdminTestimonialController::class, 'update'])->name('admin_testimonial_update');
    Route::get('/testimonial/delete/{id}', [AdminTestimonialController::class, 'delete'])->name('admin_testimonial_delete');

    // التدوينات
    Route::get('/post/index', [AdminPostController::class, 'index'])->name('admin_post_index');
    Route::get('/post/create', [AdminPostController::class, 'create'])->name('admin_post_create');
    Route::post('/post/store', [AdminPostController::class, 'store'])->name('admin_post_store');
    Route::get('/post/edit/{id}', [AdminPostController::class, 'edit'])->name('admin_post_edit');
    Route::post('/post/update/{id}', [AdminPostController::class, 'update'])->name('admin_post_update');
    Route::get('/post/delete/{id}', [AdminPostController::class, 'delete'])->name('admin_post_delete');

    // الأسئلة الشائعة
    Route::get('/faq/index', [AdminFaqController::class, 'index'])->name('admin_faq_index');
    Route::get('/faq/create', [AdminFaqController::class, 'create'])->name('admin_faq_create');
    Route::post('/faq/store', [AdminFaqController::class, 'store'])->name('admin_faq_store');
    Route::get('/faq/edit/{id}', [AdminFaqController::class, 'edit'])->name('admin_faq_edit');
    Route::post('/faq/update/{id}', [AdminFaqController::class, 'update'])->name('admin_faq_update');
    Route::get('/faq/delete/{id}', [AdminFaqController::class, 'delete'])->name('admin_faq_delete');

    // المشتركين
    Route::get('/subscriber/index', [AdminSubscriberController::class, 'index'])->name('admin_subscriber_index');
    Route::get('/subscriber/create', [AdminSubscriberController::class, 'create'])->name('admin_subscriber_create');
    Route::post('/subscriber/store', [AdminSubscriberController::class, 'store'])->name('admin_subscriber_store');
    Route::get('/subscriber/edit/{id}', [AdminSubscriberController::class, 'edit'])->name('admin_subscriber_edit');
    Route::post('/subscriber/update/{id}', [AdminSubscriberController::class, 'update'])->name('admin_subscriber_update');
    Route::get('/subscriber/delete/{id}', [AdminSubscriberController::class, 'delete'])->name('admin_subscriber_delete');
    Route::get('/subscriber/export', [AdminSubscriberController::class, 'export'])->name('admin_subscriber_export');

    // الصفحات والإعدادات
    Route::get('/page/index', [AdminPageController::class, 'index'])->name('admin_page_index');
    Route::post('/page/update', [AdminPageController::class, 'update'])->name('admin_page_update');

    Route::get('/setting/logo/index', [AdminSettingController::class, 'logo'])->name('admin_setting_logo_index');
    Route::post('/setting/logo/update', [AdminSettingController::class, 'logo_update'])->name('admin_setting_logo_update');

    Route::get('/setting/favicon/index', [AdminSettingController::class, 'favicon'])->name('admin_setting_favicon_index');
    Route::post('/setting/favicon/update', [AdminSettingController::class, 'favicon_update'])->name('admin_setting_favicon_update');

    Route::get('/setting/banner/index', [AdminSettingController::class, 'banner'])->name('admin_setting_banner_index');
    Route::post('/setting/banner/update', [AdminSettingController::class, 'banner_update'])->name('admin_setting_banner_update');

    Route::get('/setting/footer/index', [AdminSettingController::class, 'footer'])->name('admin_setting_footer_index');
    Route::post('/setting/footer/update', [AdminSettingController::class, 'footer_update'])->name('admin_setting_footer_update');
});

// مسارات دخول الأدمن (خارج الـ middleware)
Route::prefix('admin')->group(function () {
    Route::get('/', fn () => redirect()->route('admin_login'));
    Route::get('/login', [AdminController::class, 'login'])->name('admin_login');
    Route::post('/login', [AdminController::class, 'login_submit'])->name('admin_login_submit');
    Route::get('/forget-password', [AdminController::class, 'forget_password'])->name('admin_forget_password');
    Route::post('/forget-password', [AdminController::class, 'forget_password_submit'])->name('admin_forget_password_submit');
    Route::get('/reset-password/{token}/{email}', [AdminController::class, 'reset_password'])->name('admin_reset_password');
    Route::post('/reset-password/{token}/{email}', [AdminController::class, 'reset_password_submit'])->name('admin_reset_password_submit');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin_logout');
});
