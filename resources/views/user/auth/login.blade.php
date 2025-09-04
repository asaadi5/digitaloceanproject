@extends('front.layouts.master')

@section('main_content')
    <!--Sliders Section-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">تسجيل الدخول للمستخدم</h1>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Sliders Section-->


        <!--login-section-->
        <section class="sptb">
            <div class="container customerpage">
                <div class="row">
                    <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-md-12 register-right">
                                <ul class="nav nav-tabs nav-justified mb-5 p-2 border" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active m-1 border " id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">دخول كعميل </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link m-1 border " id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">دخول كمستخدم</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="admin" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="single-page  w-100  p-0">
                                            <div class="wrapper wrapper2">
                                                <form id="login" class="card-body" action="{{ route('agent_login_submit') }}" tabindex="500">
                                                    @csrf

                                                    <div class="text">
                                                        <input type="text" value="{{ old('email') }}" name="email">
                                                        <label>اسم المستخدم أو الإيميل</label>
                                                    </div>

                                                    <div class="passwd">
                                                        <input type="password" name="password">
                                                        <label>كلمة السر</label>
                                                    </div>
                                                    <div class="submit">
                                                        <button type="submit" class="btn btn-primary btn-block">تسجيل الدخول</button>
                                                    </div>
                                                    <p class="mb-2"><a href="{{ route('agent_forget_password') }}" >نسيت كلمة المرور؟</a></p>
                                                    <p class="text-dark mb-0">ليس لديك حساب؟<a href="{{ route('agent_registration') }}" class="text-primary mx-1">سجل الآن</a></p>
                                                </form>


                                            </div>
                                        </div>
                                    </div>
                                    {{-- تبويب "المستخدم" --}}
                                    <div class="tab-pane fade show" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <div class="single-page w-100 p-0">
                                            <div class="wrapper wrapper2">
                                                <form id="login" class="card-body" action="{{ route('login_submit') }}" method="POST" tabindex="500">
                                                    @csrf


                                                    <div class="text">
                                                        <input type="text" name="email" value="{{ old('email') }}">
                                                        <label>اسم المستخدم أو الإيميل</label>
                                                    </div>

                                                    <div class="passwd">
                                                        <input type="password" name="password">
                                                        <label>كلمة السر</label>
                                                    </div>

                                                    <div class="submit">
                                                        <button type="submit" class="btn btn-primary btn-block">تسجيل الدخول</button>
                                                    </div>

                                                    <p class="mb-2">
                                                        <a href="{{ route('forget_password') }}">نسيت كلمة المرور؟</a>
                                                    </p>
                                                    <p class="text-dark mb-0">
                                                        ليس لديك حساب؟
                                                        <a href="{{ route('registration') }}" class="text-primary mx-1">سجّل الآن</a>
                                                    </p>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
        <!--Register-section-->

    @endsection


