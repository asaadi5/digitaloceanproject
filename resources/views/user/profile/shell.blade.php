@extends('front.layouts.master')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Route;

    $u = auth()->user();
    $avatar = $u?->photo ? asset('uploads/'.$u->photo) : asset('assets/images/users/default.png');

    // تحديد التاب النشط اعتماداً على اسم الراوت والـ edit
    $active =
        (Route::is('profile') && request()->boolean('edit')) ? 'profile_edit' :
        (Route::is('profile') ? 'profile' : (
            (Route::is('message') || Route::is('message_*') || Route::is('message.*')) ? 'messages' :
            (Route::is('wishlist') ? 'wishlist' :
            (Route::is('dashboard') ? 'profile' : 'profile'))
        ));

    // عدّادات؛ لو الكنترولر ما بعث counts نعمل fallback هنا
    if (!isset($counts)) {
        $uid = auth()->id();
        $counts = [
            'messages' => \App\Models\Message::where('user_id',$uid)->count(),
            'wishlist' => \App\Models\Wishlist::where('user_id',$uid)->count(),
        ];



    };
@endphp

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">واجهة المستخدم</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الصفحات</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">واجهة المستخدم</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--User Profile-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    {{-- هيدر البروفايل --}}
                    <div class="card">
                        <div class="card-body pattern-1">
                            <div class="wideget-user">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="wideget-user-desc text-center">
                                            <div class="wideget-user-img">
                                                <img class="brround" src="{{ $avatar }}" alt="img"
                                                     style="width:86px;height:86px;object-fit:cover;">
                                            </div>
                                            <div class="user-wrap wideget-user-info">
                                                <h4 class="text-white font-weight-semibold mt-2">{{ $u?->name }}</h4>
                                                <span class="text-white">مستخدم منذ {{ ar_date($u?->created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /wideget-user -->
                        </div>

                        {{-- شريط التابات (روابط للراوت) --}}
                        <div class="card-footer">
                            <div class="wideget-user-tab">
                                <div class="tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <ul class="nav">
                                            <li class="{{ $active==='profile' ? 'active' : '' }}">
                                                <a href="{{ route('profile') }}" class="{{ $active==='profile' ? 'active' : '' }}">
                                                    الملف الشخصي
                                                </a>
                                            </li>
                                            <li class="{{ $active==='profile_edit' ? 'active' : '' }}">
                                                <a href="{{ route('profile', ['edit'=>1]) }}" class="{{ $active==='profile_edit' ? 'active' : '' }}">
                                                    تعديل الملف الشخصي
                                                </a>
                                            </li>
                                            <li class="{{ $active==='messages' ? 'active' : '' }}">
                                                <a href="{{ route('message') }}" class="{{ $active==='messages' ? 'active' : '' }}">
                                                    الرسائل
                                                    <span class="badge badge-primary rounded-pill">{{ $counts['messages'] ?? 0 }}</span>
                                                </a>
                                            </li>
                                            <li class="{{ $active==='wishlist' ? 'active' : '' }}">
                                                <a href="{{ route('wishlist') }}" class="{{ $active==='wishlist' ? 'active' : '' }}">
                                                    العقارات المفضلة
                                                    <span class="badge badge-primary rounded-pill" data-wishlist-count>
                                                        {{ $counts['wishlist'] ?? 0 }}
                                                    </span>

                                                </a>
                                            </li>
                                        </ul>
                                    </div><!-- /tabs-menu1 -->
                                </div>
                            </div>
                        </div>
                    </div><!-- /card (header + tabs) -->

                    {{-- محتوى التاب الحالي (تُدخله الصفحات الفرعية) --}}
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="border-0">
                                @yield('profile_tab_content')
                            </div>
                        </div>
                    </div>

                </div><!-- /col -->
            </div>
        </div>
    </section>
    <!--/User Profile-->
@endsection
