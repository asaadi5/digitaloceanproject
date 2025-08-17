@extends('front.layouts.master')

@section('main_content')
    <!--Sliders Section-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">Customer Login</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Login</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Sliders Section-->

    <!--Login-Section-->
    <section class="sptb">
        <div class="container customerpage">
            <div class="row">
                <div class="single-page" >
                    <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                        <div class="wrapper wrapper2">
                            <form id="login" class="card-body" tabindex="500" action="{{ route('login_submit') }}" method="post">
                                @csrf
                                <h3>Login</h3>
                                <div class="mail">
                                    <input type="email" name="email">
                                    <label>اسم المستخدم أو البريد الإلكتروني</label>
                                </div>
                                <div class="passwd">
                                    <input type="password" name="password">
                                    <label>كلمة السر</label>
                                </div>
                                <div class="submit">
                                    <button type="submit" class="btn btn-primary btn-block">تسجيل الدخول</button>
                                </div>
                                <p class="text-primary mx-1"><a href="{{ route('forget_password') }}" >نسيت كلمة السر؟</a></p>
                                <p class="text-dark mb-0">لا تملك حساباً؟<a href="{{ route('registration') }}" class="text-primary mx-1">سجل الآن</a></p>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Login-Section-->
@endsection
