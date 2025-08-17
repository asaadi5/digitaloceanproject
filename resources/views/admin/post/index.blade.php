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
                        <li class="breadcrumb-item active" aria-current="page"> منشورات المدونة </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between">
                    <h1>المنشورات</h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_post_create') }}" class="btn btn-primary">إضافة منشور<i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="example1">
                                            <thead>
                                            <tr>
                                                <th>الرقم التسلسلي</th>
                                                <th>الصورة</th>
                                                <th>العنوان</th>
                                                <th>المعرّف النصّي</th>
                                                <th>إجمالي المشاهدات</th>
                                                <th class="w_100">الإجراء</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($posts as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <img src="{{ asset('uploads/'.$item->photo) }}" alt="" class="w_200">
                                                    </td>
                                                    <td>{{ $item->title }}</td>
                                                    <td>{{ $item->slug }}</td>
                                                    <td>{{ $item->total_views }}</td>
                                                    <td>
                                                        <a href="{{ route('admin_post_edit',$item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                        <a href="{{ route('admin_post_delete',$item->id) }}" class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
