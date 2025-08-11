@extends('admin.layouts.master')

@section('main_content')
    <div class="container my-5">
        <div class="row justify-content-center m-0">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card radius-10 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h4 class="mb-1">إعادة تعيين كلمة المرور</h4>
                            <p class="text-muted mb-0">أدخل كلمة المرور الجديدة ثم أكدها</p>
                        </div>

                        {{-- رسائل الأخطاء/التنبيهات --}}
                        @if(session('status'))
                            <div class="alert alert-success py-2">{{ session('status') }}</div>
                        @endif
                        @error('password')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror
                        @error('confirm_password')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <form class="form-body row g-3"
                              method="post"
                              action="{{ route('admin_reset_password_submit', [$token, $email]) }}">
                            @csrf

                            <div class="col-12">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="••••••••"
                                    autocomplete="new-password"
                                    autofocus
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                                <input
                                    id="confirm_password"
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    placeholder="أعد إدخال كلمة المرور"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        تأكيد التغيير
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <a href="{{ route('admin_login') }}" class="text-decoration-none">
                                    الرجوع إلى تسجيل الدخول
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
