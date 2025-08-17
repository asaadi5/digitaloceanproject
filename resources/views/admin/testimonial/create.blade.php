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
                        <li class="breadcrumb-item active" aria-current="page"> شهادات العملاء </li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة شهادة عميل</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between">
                    <h1>إضافة شهادة عميل</h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_testimonial_index') }}" class="btn btn-primary">عرض الكل<i class="fas fa-eye"></i></a>
                    </div>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('admin_testimonial_store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div>
                                            <label class="form-label">الصورة *</label>
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
                                            <!--
                                                <label> الصورة * </label>
                                                <div>
                                                    <input type="file" id="photo" name="photo" class="d-none" accept="image/*">

                                                    <label for="photo" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                                        <ion-icon name="image-sharp"></ion-icon>
                                                        تحميل الصورة
                                                    </label>
                                                </div>
                                                 -->

                                        </div>
                                        <div class="form-group mb-3">
                                            <label>الاسم *</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>الصفة *</label>
                                            <input type="text" class="form-control" name="designation" value="{{ old('designation') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>تعليق *</label>
                                            <textarea name="comment" class="form-control h_100" cols="30" rows="10">{{ old('comment') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">إضافة</button>
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
