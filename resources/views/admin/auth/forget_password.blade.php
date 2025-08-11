@extends('admin.layouts.master')

@section('main_content')
    <div class="container my-5">
        <div class="row justify-content-center m-0">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card radius-10 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h4 class="mb-1">إعادة تعيين كلمة المرور</h4>
                            <p class="text-muted mb-0">سيصلك رابط إعادة التعيين على بريدك الإلكتروني</p>
                        </div>
                        @if(session('success'))
                            <div class="alert alert-success py-2">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger py-2">{{ session('error') }}</div>
                        @endif

                        @error('email')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror


                        <form class="form-body row g-3" method="post" action="{{ route('admin_forget_password_submit') }}">
                            @csrf
                            <div class="col-12">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    placeholder="name@example.com"
                                    value="{{ old('email') }}"
                                    autofocus
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        إرسال رابط إعادة التعيين
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <a href="{{ route('admin_login') }}" class="text-decoration-none">
                                    الرجوع إلى صفحة الدخول
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
