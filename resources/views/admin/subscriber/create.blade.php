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
                        <li class="breadcrumb-item active" aria-current="page"> المشتركين </li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة مشترك</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between">
                    <h1>إضافة مشترك</h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_subscriber_index') }}" class="btn btn-primary"> عرض الكل<i class="fas fa-eye"></i></a>
                    </div>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('admin_subscriber_store') }}" method="post">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label>البريد الإلكتروني *</label>
                                            <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>الحالة *</label>
                                            <select name="status" class="form-select">
                                                <option value="1">نشط</option>
                                                <option value="0">غير نشط</option>
                                            </select>
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
