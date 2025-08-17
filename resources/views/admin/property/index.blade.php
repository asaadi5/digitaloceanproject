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
                <div class="breadcrumb-title pe-3"> قسم العقارات </div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 align-items-center">
                            <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> العقارات </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="main-content">
                <div class="section-header d-flex justify-content-between">
                    <h1> العقارات </h1>
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
                                                <th> الرقم التسلسلي </th>
                                                <th> الصورة </th>
                                                <th> الاسم </th>
                                                <th> الوكيل </th>
                                                <th> الموقع </th>
                                                <th> النوع </th>
                                                <th> الغرض </th>
                                                <th> السعر </th>
                                                <th> الحالة </th>
                                                <th> الإجراء </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($properties as $item)
                                                <tr>
                                                    <td> {{ $loop->iteration }} </td>
                                                    <td>
                                                        @if($item->featured_photo != null)
                                                            <img src="assets/images/products/estate1.jpg" alt="" class="table-img">
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->agent->name }}</td>
                                                    <td>{{ $item->location->name }}</td>
                                                    <td>{{ $item->type->name }}</td>
                                                    <td>{{ $item->purpose }}</td>
                                                    <td>${{ $item->price }}</td>
                                                    <td>
                                                        @if($item->status == 'Pending')
                                                            <span class="badge bg-danger"> معلق </span>
                                                        @else
                                                            <span class="badge bg-success"> متاح </span>
                                                        @endif
                                                        <div><a href="{{ route('admin_property_change_status',$item->id) }}"> تغيير </a></div>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin_property_detail',$item->id) }}" class="btn btn-primary btn-sm"><ion-icon name="eye-outline"></ion-icon></a>
                                                        <a href="{{ route('admin_property_delete',$item->id) }}" class="btn btn-danger btn-sm delete-btn"><ion-icon name="trash-outline"></ion-icon></a>
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
            </div>

        </div>
        <!-- end page content-->
    </div>
    <!--end page content wrapper-->
@endsection
