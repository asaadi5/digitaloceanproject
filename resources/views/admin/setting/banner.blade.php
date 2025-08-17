{{--@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Banner</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_setting_banner_update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Banner</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$setting->banner) }}" alt="" class="w_300">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Banner</label>
                                    <div><input type="file" name="banner"></div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection--}}
@extends('admin.layouts.master')

@section('main_content')
    @include('admin.layouts.nav')
    @include('admin.layouts.sidebar')
    <!-- start page content-->
    <div class="page-content">

        <div class="card">
            <div class="card-header bg-primary text-light">
                <h6 class="mb-0">تعديل غلاف الموقع</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin_setting_banner_update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                <div class="mb-4 d-flex flex-column gap-3 align-items-center justify-content-center">
                        <label class="badge rounded-pill bg-primary-subtle text-secondary px-4 py-2 fs-6">الغلاف الحالي</label>
                        <figure class="figure m-0 text-end rounded-circle">
                            <img src="{{ asset('uploads/'.$setting->banner) }}"
                                 class="figure-img img-fluid img-thumbnail"
                                 alt="الغلاف الحالي">
                        </figure>
                        <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-4"><ion-icon name="image-sharp"></ion-icon>اختيار ملف</button>

                    <div class="text-start mt-3">
                        <button type="button" class="btn btn-primary px-4">حفظ التغييرات</button>
                    </div>
                    </div>
                    <div class="text-start mt-3">
                        <button type="button" class="btn btn-primary px-4">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

        <!--end row-->

        <!-- end page content-->
@endsection
