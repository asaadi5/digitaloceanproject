{{--@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Logo</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_setting_logo_update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Logo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$setting->logo) }}" alt="" class="w_100">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Logo</label>
                                    <div><input type="file" name="logo"></div>
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
@endsection
--}}

@extends('admin.layouts.master')

@section('main_content')
    @include('admin.layouts.nav')
    @include('admin.layouts.sidebar')

    <div class="page-content">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">إعدادات الموقع</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 align-items-center">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin_dashboard') }}"><ion-icon name="home-outline"></ion-icon></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">الشعار</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-light">
                <h6 class="mb-0">تعديل شعار الموقع</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin_setting_logo_update') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4 d-flex flex-column gap-3 align-items-center justify-content-center">
                        <span class="badge rounded-pill bg-primary-subtle text-secondary px-4 py-2 fs-6">الشعار الحالي</span>

                        <figure class="figure m-0 text-end">
                            <img src="{{ asset('uploads/'.$setting->logo) }}"
                                 class="figure-img img-fluid img-thumbnail"
                                 alt="الشعار الحالي" style="max-height:220px">
                        </figure>

                        <div class="mb-3 w-100" style="max-width:420px">
                            <label for="formFileSm" class="form-label">اختر شعاراً</label>
                            <input class="form-control form-control-sm" id="formFileSm" type="file" name="logo" accept="image/*">
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary px-4">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

    </div> {{-- اغلاق page-content --}}
@endsection

