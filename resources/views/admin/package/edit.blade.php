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
                            <li class="breadcrumb-item active" aria-current="page"> الباقات </li>
                            <li class="breadcrumb-item active" aria-current="page"> تعديل الباقة </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="main-content">
                <section class="section">
                    <div class="section-header d-flex justify-content-between">
                        <h1>تعديل الباقة</h1>
                        <div class="ml-auto">
                            <a href="{{ route('admin_package_index') }}" class="btn btn-primary">عرض الكل<i class="fas fa-eye"></i></a>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="{{ route('admin_package_update',$package->id) }}" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>الاسم *</label>
                                                        <input type="text" class="form-control" name="name" value="{{ $package->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>السعر *</label>
                                                        <input type="text" class="form-control" name="price" value="{{ $package->price }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>الأيام المسموح بها *</label>
                                                        <input type="text" class="form-control" name="allowed_days" value="{{ $package->allowed_days }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>العقارات المسموح بها *</label>
                                                        <input type="text" class="form-control" name="allowed_properties" value="{{ $package->allowed_properties }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label>العقارات المميزة المسموح بها *</label>
                                                        <input type="text" class="form-control" name="allowed_featured_properties" value="{{ $package->allowed_featured_properties }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label>الصور المسموح بها *</label>
                                                        <input type="text" class="form-control" name="allowed_photos" value="{{ $package->allowed_photos }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label>مقاطع الفيديو المسموح بها *</label>
                                                        <input type="text" class="form-control" name="allowed_videos" value="{{ $package->allowed_videos }}">
                                                    </div>
                                                </div>
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
