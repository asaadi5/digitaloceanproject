@extends('front.layouts.master')

@section('main_content')
    <!--Sliders Section-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">إعادة كلمة المرور للمستخدم</h1>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Sliders Section-->


    <!--reset-Section-->
    <section class="sptb">
        <div class="container customerpage">
            <div class="row">
                <div class="single-page" >
                    <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                        <div class="wrapper wrapper2">
                            <form id="login" class="card-body" tabindex="500" action="{{ route('reset_password_submit',[$token,$email]) }}" method="post">
                                @csrf
                                <h3>إعادة تعيين كلمة المرور</h3>

                                <div class="passwd">
                                    <input type="password" name="password">
                                    <label> كلمة السر الجديدة</label>
                                </div>
                                <div class="passwd">
                                    <input type="password" name="confirm_password">
                                    <label>أعد كتابة كلمة السر</label>
                                </div>
                                <div class="submit">
                                    <button type="submit" class="btn btn-primary btn-block">تأكيد</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--reset-Section-->
@endsection
