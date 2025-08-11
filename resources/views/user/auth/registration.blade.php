@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">Register</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Register</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Register-section-->
    <section class="sptb">
        <div class="container customerpage">
            <div class="row">
                <div class="single-page" >
                    <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                        <div class="wrapper wrapper2">
                            <form id="Register" class="card-body" tabindex="500" action="{{ route('registration_submit') }}" method="post">
                                @csrf
                                <h3>Register</h3>
                                <div class="name">
                                    <input type="text" name="name">
                                    <label>الاسم</label>
                                </div>
                                <div class="name">
                                    <input type="text" name="username">
                                    <label>اسم المستخدم</label>
                                </div>
                                <div class="mail">
                                    <input type="email" name="email">
                                    <label>الإيميل</label>
                                </div>
                                <div class="passwd">
                                    <input type="password" name="password">
                                    <label>كلمة السر</label>
                                </div>
                                <div class="passwd">
                                    <input type="password" name="confirm_password">
                                    <label>تأكيد كلمة السر</label>
                                </div>
                                <div class="submit">
                                    <button type="submit" class="btn btn-primary btn-block">إنشاء حساب</button>
                                </div>
                                <p class="text-dark mb-0">لديك حساب بالفعل؟<a href="{{ route('login') }}" class="text-primary mx-1">تسجيل الدخول</a></p>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Register-section-->

@endsection
