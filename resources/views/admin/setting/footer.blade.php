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
                <div class="breadcrumb-title pe-3">إعدادات الموقع</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 align-items-center">
                            <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">تذييل الموقع</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h6 class="mb-0">تعديل ذيل الصفحة</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="search" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" placeholder="example@gmail.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="search" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">فيسبوك</label>
                            <input type="url" class="form-control" placeholder="https://facebook.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">إكس</label>
                            <input type="url" class="form-control" placeholder="https://x.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">لينكد إن</label>
                            <input type="url" class="form-control" placeholder="https://linkedin.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">إنستغرام</label>
                            <input type="url" class="form-control" placeholder="https://instagram.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">حقوق الطبع والنشر*</label>
                            <input type="search" class="form-control">
                        </div>
                        <div class="text-start mt-3">
                            <button type="button" class="btn btn-primary px-4">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- end page content-->
    </div>
    <!--end page content wrapper-->
@endsection
