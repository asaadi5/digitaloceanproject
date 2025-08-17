@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<!-- start page content wrapper-->
<div class="page-content-wrapper">

    <!-- start page content-->
    <div class="page-content">

        <!--start breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">لوحة التحكم</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 align-items-center">
                        <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"> الوكلاء </li>
                        <li class="breadcrumb-item active" aria-current="page"> تعديل معلومات الوكيل </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>تعديل معلومات الوكيل</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_agent_index') }}" class="btn btn-primary">عرض الكل<i class="fas fa-eye"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_agent_update',$agent->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label> الصورة الحالية </label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$agent->photo) }}" alt="لا يوجد" class="table-img">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">تغيير الصورة</label>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="file" id="dash_photo" name="photo" class="d-none" accept="image/*">

                                        <label for="dash_photo"
                                               class="btn btn-outline-primary btn-sm rounded-pill px-4 d-inline-flex align-items-center gap-2">
                                            <ion-icon name="image-sharp"></ion-icon>
                                            تحميل صورة
                                        </label>

                                        <span id="dash_photoName"
                                              class="text-muted small text-truncate"
                                              style="max-width: 260px;"
                                              aria-live="polite">لم يتم اختيار ملف</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>الاسم *</label>
                                            <input type="text" class="form-control" name="name" value="{{ $agent->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>البريد الاإلكتروني *</label>
                                            <input type="text" class="form-control" name="email" value="{{ $agent->email }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>الشركة *</label>
                                            <input type="text" class="form-control" name="company" value="{{ $agent->company }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>الصفة *</label>
                                            <input type="text" class="form-control" name="designation" value="{{ $agent->designation }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>رقم الهاتف</label>
                                            <input type="text" class="form-control" name="phone" value="{{ $agent->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>العنوان</label>
                                            <input type="text" class="form-control" name="address" value="{{ $agent->address }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>الدولة</label>
                                            <input type="text" class="form-control" name="country" value="{{ $agent->country }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>المحافظة</label>
                                            <input type="text" class="form-control" name="state" value="{{ $agent->state }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>المدينة</label>
                                            <input type="text" class="form-control" name="city" value="{{ $agent->city }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>الرمز البريدي</label>
                                            <input type="text" class="form-control" name="zip" value="{{ $agent->zip }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>فيسبوك</label>
                                            <input type="text" class="form-control" name="facebook" value="{{ $agent->facebook }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>إكس</label>
                                            <input type="text" class="form-control" name="twitter" value="{{ $agent->twitter }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>لينكد إن</label>
                                            <input type="text" class="form-control" name="linkedin" value="{{ $agent->linkedin }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>إنستغرام</label>
                                            <input type="text" class="form-control" name="instagram" value="{{ $agent->instagram }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>موقع الويب</label>
                                    <input type="text" class="form-control" name="website" value="{{ $agent->website }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>كلمة المرور</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>أعد كتابة كلمة المرور</label>
                                            <input type="password" class="form-control" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>السيرة الذاتية</label>
                                    <textarea name="biography" class="form-control h_200" cols="30" rows="10">{{ $agent->biography }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label>الحالة *</label>
                                    <select name="status" class="form-select">
                                        <option value="0" {{ $agent->status == 0 ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="1" {{ $agent->status == 1 ? 'selected' : '' }}>نشط</option>
                                        <option value="2" {{ $agent->status == 2 ? 'selected' : '' }}>معلق</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">تحديث</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
    </div>
    <!-- end page content-->
</div>
<!--end page content wrapper-->

@endsection
