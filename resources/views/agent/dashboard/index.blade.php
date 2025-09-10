@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">لوحة التحكم الخاصة بي</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">لوحة التحكم الخاصة بي</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Breadcrumb-->

    <!--Dashboard-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row  g-0">

                <!-- الشريط الجانبي  -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي  -->

                <!-- المحتوى  -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">لمحة عن جميع الإحصائيات</h3>
                        </div>
                        <div class="card-body text-dark">
                            <div class="statistics-info">
                                <div class="row text-center">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="counter-status">
                                            <div class="counter-icon bg-transparent text-danger">
                                                <i class="icon icon-home"></i>
                                            </div>
                                            <h5>العقارات النشطة</h5>
                                            <h2 class="counter">{{ $total_active_properties }}</h2>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <div class="counter-status">
                                            <div class="counter-icon bg-transparent text-warning">
                                                <i class="icon icon-clock"></i>
                                            </div>
                                            <h5>العقارات المُعلّقة</h5>
                                            <h2 class="counter">{{ $total_pending_properties }}</h2>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <div class="counter-statuss">
                                            <div class="counter-icon bg-transparent text-primary">
                                                <i class="icon icon-star"></i>
                                            </div>
                                            <h5>العقارت المُميزة</h5>
                                            <h2 class="counter">{{ $total_featured_properties }}</h2>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <div class="card-header">
                                <h3 class="card-title">العقارات الأخيرة</h3>
                            </div>
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الموقع</th>
                                    <th>النوع</th>
                                    <th>الغرض</th>
                                    <th>السعر</th>
                                    <th>هل هو مميز؟</th>
                                    <th>تاريخ الإنشاء</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recent_properties as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->location->name }}</td>
                                        <td>{{ $item->type->name }}</td>
                                        <td>{{ $item->purpose }}</td>
                                        <td>${{ $item->price }}</td>
                                        <td>
                                            @if($item->is_featured == 'Yes')
                                                <span class="badge bg-success rounded-pill">Yes</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد بيانات لعرضها حالياً</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /المحتوى -->
                <!--/Dashboard-->
            </div>
        </div>
    </section>
@endsection
