@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">إنشاء حساب جديد</h1>
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
                <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-md-12 register-right">
                            <ul class="nav nav-tabs nav-justified mb-5 p-2 border" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active m-1 border " id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">إنشاء كعميل</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link m-1 border " id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">إنشاء كمستخدم</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="agent_register" role="tabpanel" aria-labelledby="agent-tab">
                                    <div class="single-page  w-100  p-0">
                                        <div class="wrapper wrapper2">
                                            <form id="login" class="card-body" action="{{ route('agent_registration_submit') }}" tabindex="500">
                                                <div class="text">
                                                    <input type="email" name="name" value="{{ old('name') }}">
                                                    <label>الاسم</label>
                                                </div>
                                                <div class="text">
                                                    <input type="text" name="username" value="{{ old('username') }}">
                                                    <label>اسم المستخدم</label>
                                                </div>
                                                <div class="mail">
                                                    <input type="email" name="email" value="{{ old('email') }}">
                                                    <label>ايميل</label>
                                                </div>
                                                <div class="phone">
                                                    <input type="phone" name="phone" {{ old('phone') }}>
                                                    <label>رقم الهاتف</label>
                                                </div>
                                                <div class="text">
                                                    <input type="text" name="company_name" {{ old('company') }}>
                                                    <label>اسم الشركة</label>
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
                                                    <button type="submit" class="btn btn-primary btn-block">إرسال</button>
                                                </div>
                                                <p class="text-dark mb-0">لديك حساب بالفعل؟<a href="{{route('agent.login')}}" class="text-primary mx-1">سجل الدخول</a></p>
                                            </form>


                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="profile_register" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="single-page w-100 p-0">
                                        <div class="wrapper wrapper2">
                                            <form id="Register" class="card-body" action="{{ route('registration_submit') }}" method="POST" tabindex="500">
                                                @csrf

                                                <div class="text">
                                                    <input type="text" name="name" value="{{ old('name') }}">
                                                    <label>الاسم</label>
                                                </div>

                                                <div class="text">
                                                    <input type="text" name="username" value="{{ old('username') }}">
                                                    <label>اسم المستخدم</label>
                                                </div>

                                                <div class="mail">
                                                    <input type="email" name="email" value="{{ old('email') }}">
                                                    <label>ايميل</label>
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
                                                    <button type="submit" class="btn btn-primary btn-block">إرسال</button>
                                                </div>

                                                <p class="text-dark mb-0">
                                                    لديك حساب بالفعل؟
                                                    <a href="{{ route('login') }}" class="text-primary mx-1">سجّل الدخول</a>
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
