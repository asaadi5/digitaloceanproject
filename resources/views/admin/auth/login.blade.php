@extends('admin.layouts.master')

@section('main_content')

    {{-- خلفية القالب الجديد (لن تؤذي إن لم تكن أنماطها موجودة) --}}
    <div class="login-bg-overlay au-sign-in-basic"></div>


    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card radius-10 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h4 class="mb-1">تسجيل الدخول</h4>
                            <p class="text-muted mb-0">سجّل دخولك إلى لوحة التحكم</p>
                        </div>

                        <form method="post" action="{{ route('admin_login_submit') }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="name@example.com"
                                    autofocus
                                >
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                >
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="remember"
                                        name="remember"
                                        {{ old('remember') ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="remember">تذكّرني</label>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6 text-start text-lg-end">
                                <a href="{{ route('admin_forget_password') }}">نسيت كلمة المرور؟</a>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">دخول</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- يمكن إضافة فوتر بسيط هنا إذا رغبت --}}
            </div>
        </div>
    </div>

@endsection
