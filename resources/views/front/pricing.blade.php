@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">أسعار الإشتراكات</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">أسعار الإشتراكات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Pricing Tables 1-->
    <div class="sptb">
        <div class="container">
            <div class="row">

                @foreach($packages as $package)
                    @php
                        // ستايلات البطاقات (نفس القالب)
                        $palette = [
                            ['box' => 'pink', 'btn' => 'btn-primary',  'plans_extra' => ''],
                            ['box' => 'blue', 'btn' => 'btn-secondary','plans_extra' => ''],
                            ['box' => '',     'btn' => 'btn-warning',  'plans_extra' => 'bg-warning'],
                        ];
                        $style = $palette[$loop->index % count($palette)];

                        // نص عدد العقارات بنفس فكرة “غير محدود / رقم”
                        if ($package->allowed_properties == -1) {
                            $allowedText = 'غير محدود';
                        } else {
                            $allowedText = (string) $package->allowed_properties;
                        }

                        // رابط الزر:
                        $isAgent = Auth::guard('agent')->check();

                        // صفحة الدفع (مرّر id الباقة)
                        $paymentUrl = \Illuminate\Support\Facades\Route::has('agent_payment')
                            ? route('agent_payment', $package->id)
                            : url('agent/payment/'.$package->id);

                        // لو مش وكيل -> إلى تسجيل الوكلاء مع redirect للعودة للدفع بعد الدخول
                        $registerUrl = \Illuminate\Support\Facades\Route::has('agent_registration')
                            ? route('agent_registration', ['redirect' => $paymentUrl])
                            : url('agent/registration?redirect='.urlencode($paymentUrl));

                        $goTo = $isAgent ? $paymentUrl : $registerUrl;
                    @endphp

                    <div class="col-xl-4 col-md-6 col-sm-12 col-lg-3">
                        <div class="pricingTable2 {{ $style['box'] }} mb-4 mb-xl-0">
                            <div class="pricingTable2-header">
                                <h3>{{ $package->name }}</h3>
                            </div>

                            <div class="pricing-plans {{ $style['plans_extra'] }}">
                                <span class="price-value1">
                                    <i class="fa fa-usd"></i>
                                    <span>{{ rtrim(rtrim(number_format((float)$package->price, 2, '.', ''), '0'), '.') }}</span>
                                </span>
                                <span class="month">/شهر</span>
                            </div>

                            <div class="pricingContent2">
                                <ul>
                                    <li>عدد العقارات: <b>{{ $allowedText }}</b></li>
                                </ul>
                            </div><!-- CONTENT BOX-->

                            <div class="pricingTable2-sign-up">
                                <a href="{{ $goTo }}" class="btn btn-block {{ $style['btn'] }}">اختر الخطة</a>
                            </div><!-- BUTTON BOX-->
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <!--/Pricing Tables 1-->
@endsection
