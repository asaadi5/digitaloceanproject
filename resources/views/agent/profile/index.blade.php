@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">لوحة التحكم الخاصة بي</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">تعديل الملف الشخصي</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    @php
        $agent = $agent ?? Auth::guard('agent')->user();
    @endphp

        <!--Edit-profile-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row g-0">

                <!-- الشريط الجانبي  -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي  -->

                <!-- المحتوى  -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">تعديل الملف الشخصي</h3>
                        </div>

                        <form action="{{ route('agent_profile_submit') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <!-- الصورة -->
                                    <div class="col-xl-4 col-lg-4 col-md-3">
                                        <div class="form-group">
                                            <label class="form-label d-block">الصورة الحالية</label>
                                            <img src="{{ $agent->photo ? asset('uploads/'.$agent->photo) : asset('assets/images/faces/male/25.jpg') }}"
                                                 alt="الصورة الحالية" class="brround"
                                                 style="width:95px;height:95px;object-fit:cover;">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">تغيير الصورة</label>
                                            <input type="file" class="form-control example-file-input-custom" name="photo">
                                        </div>
                                    </div>

                                    <!-- الحقول -->
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">الاسم *</label>
                                                    <input type="text" class="form-control" name="name"
                                                           value="{{ old('name', $agent->name) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">البريد الإلكتروني *</label>
                                                    <input type="email" class="form-control" name="email"
                                                           value="{{ old('email', $agent->email) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">الشركة *</label>
                                                    <input type="text" class="form-control" name="company"
                                                           value="{{ old('company', $agent->company) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">المسمى الوظيفي</label>
                                                    <input type="text" class="form-control" name="designation"
                                                           value="{{ old('designation', $agent->designation) }}">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">الهاتف</label>
                                                    <input type="text" class="form-control" name="phone"
                                                           value="{{ old('phone', $agent->phone) }}" placeholder="مثال: 05xxxxxxxx">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">العنوان</label>
                                                    <input type="text" class="form-control" name="address"
                                                           value="{{ old('address', $agent->address) }}" placeholder="العنوان بالتفصيل">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">المدينة</label>
                                                    <input type="text" class="form-control" name="city"
                                                           value="{{ old('city', $agent->city) }}" placeholder="المدينة">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">فيسبوك</label>
                                                    <input type="text" class="form-control" name="facebook"
                                                           value="{{ old('facebook', $agent->facebook) }}" placeholder="رابط حساب فيسبوك">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">تلغرام</label>
                                                    <input type="text" class="form-control" name="telegram"
                                                           value="{{ old('telegram', $agent->telegram) }}" placeholder="رابط أو معرف تلغرام">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">تويتر</label>
                                                    <input type="text" class="form-control" name="twitter"
                                                           value="{{ old('twitter', $agent->twitter) }}" placeholder="رابط تويتر (X)">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">إنستغرام</label>
                                                    <input type="text" class="form-control" name="instagram"
                                                           value="{{ old('instagram', $agent->instagram) }}" placeholder="رابط إنستغرام">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">الموقع الإلكتروني</label>
                                                    <input type="text" class="form-control" name="website"
                                                           value="{{ old('website', $agent->website) }}" placeholder="https://example.com">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">كلمة المرور</label>
                                                    <input type="password" class="form-control" name="password" placeholder="(اختياري) أدخل كلمة مرور جديدة">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">تأكيد كلمة المرور</label>
                                                    <input type="password" class="form-control" name="confirm_password" placeholder="أعد إدخال كلمة المرور">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">نبذة تعريفية</label>
                                                    <textarea rows="5" class="form-control" name="biography" placeholder="اكتب نبذة مختصرة عنك">{{ old('biography', $agent->biography) }}</textarea>
                                                </div>
                                            </div>

                                        </div> <!-- /row inner -->
                                    </div>
                                </div> <!-- /row -->
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Edit-profile-->
@endsection
