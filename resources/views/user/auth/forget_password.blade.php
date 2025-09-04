@extends('front.layouts.master')

@section('main_content')
    <!--Sliders Section-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">استعادة كلمة المرور</h1>

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
                            <form id="login" class="card-body" tabindex="500" action="{{ route('forget_password_submit') }}" method="post">
                                @csrf
                                <h3>نسيت كلمة المرور</h3>
                                <div class="mail">
                                    <input type="email" name="email">
                                    <label>البريد الإلكتروني</label>
                                </div>

                                <div class="submit">
                                    <button type="submit" class="btn btn-primary btn-block">إرسال</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Login-Section-->

@endsection
