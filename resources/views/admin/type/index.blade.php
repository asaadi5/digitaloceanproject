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
                            <li class="breadcrumb-item active" aria-current="page"> النوع </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="main-content">
                <div class="section-header d-flex justify-content-between">
                    <h1> الأنواع </h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_type_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة نوع </a>
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
                                                <th> الرقم التسلسلي </th>
                                                <th> الاسم </th>
                                                <th class="w_100"> الإجراء </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($types as $type)
                                                <tr>
                                                    <td> {{ $loop->iteration }} </td>
                                                    <td> {{ $type->name }} </td>
                                                    <td>
                                                        <a href="{{ route('admin_type_edit',$type->id) }}" class="btn btn-primary btn-sm"><ion-icon name="pencil-outline"></ion-icon></a>
                                                        <a href="{{ route('admin_type_delete',$type->id) }}" class="btn btn-danger btn-sm delete-btn"><ion-icon name="trash-outline"></ion-icon></a>
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
