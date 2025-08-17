{{-- resources/views/admin/index.blade.php --}}
{{-- resources/views/admin/index.blade.php --}}
@extends('admin.layouts.master')

@section('main_content')
    @include('admin.layouts.sidebar')
    @include('admin.layouts.nav')
    {{-- غلاف المحتوى --}}

    <!-- start page content wrapper-->
    <div class="page-content-wrapper">
        <!-- start page content-->
        <div class="page-content">

            <!--start breadcrumb-->
            @include('admin.layouts.breadcrumb', ['title' => 'لوحة التحكم'])
            <!--end breadcrumb-->

            <div class="row row-cols-1 row-cols-lg-2 row-cols-xxl-3">
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="pricetag-outline" class="text-primary"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي الباقات</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">1,037</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="checkmark-done-outline" class="text-success"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي الطلبات المكتملة</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">23,758</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="business-outline" class="text-info"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي العقارات النشطة</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">1,139</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="person-add-outline" class="text-warning"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي المشتركين النشطين</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">350</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="person-outline" class="text-bronze"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي العملاء النشطين</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">630</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-5">
                                    <ion-icon name="people-outline" class="text-danger"></ion-icon>
                                </div>
                                <div>
                                    <p class="mb-0">إجمالي الوكلاء النشطين</p>
                                </div>
                                <div class="fs-5 ms-auto">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h5 class="mb-0">80</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end row-->
        </div>
        <!-- end page content-->
    </div>
    <!--end page content wrapper-->

    @include('admin.layouts.switcher')

@endsection



