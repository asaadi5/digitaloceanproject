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
                            <li class="breadcrumb-item active text-white" aria-current="page">كل العقارات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Breadcrumb-->

    <!--All-properties-->
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
                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">عقاراتي</h3>
                        </div>
                        <div class="card-body">
                            <div class="ads-tabs">
                                @php
                                    $allCount      = $properties->count();
                                    $activeCount   = $properties->where('status','Active')->count();
                                    $featuredCount = $properties->filter(fn($p)=>strtolower($p->is_featured)==='yes')->count();
                                    $soldCount     = $properties->where('status','Sold')->count(); // إن وُجدت
                                    $saleCount     = $properties->filter(fn($p)=>strtolower($p->purpose)==='buy')->count();
                                    $rentCount     = $properties->filter(fn($p)=>strtolower($p->purpose)==='rent')->count();
                                @endphp

                                <div class="tabs-menus">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs">
                                        <li class=""><a href="#tab-all" class="active" data-bs-toggle="tab">كل العقارات ({{ $allCount }})</a></li>
                                        <li><a href="#tab-active" data-bs-toggle="tab">نشِطة ({{ $activeCount }})</a></li>
                                        <li><a href="#tab-featured" data-bs-toggle="tab">مميّزة ({{ $featuredCount }})</a></li>
                                        <li><a href="#tab-sold" data-bs-toggle="tab">مباعة ({{ $soldCount }})</a></li>
                                        <li><a href="#tab-sale" data-bs-toggle="tab">للبيع ({{ $saleCount }})</a></li>
                                        <li><a href="#tab-rent" data-bs-toggle="tab">للإيجار ({{ $rentCount }})</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">

                                    <!-- كل العقارات -->
                                    <div class="tab-pane active table-responsive userprof-tab" id="tab-all">
                                        @include('agent.property.partials.table', ['rows' => $properties])
                                    </div>

                                    <!-- نشِطة -->
                                    <div class="tab-pane table-responsive userprof-tab" id="tab-active">
                                        @include('agent.property.partials.table', [
                                            'rows' => $properties->where('status','Active')
                                        ])
                                    </div>

                                    <!-- مميّزة -->
                                    <div class="tab-pane table-responsive userprof-tab" id="tab-featured">
                                        @include('agent.property.partials.table', [
                                            'rows' => $properties->filter(fn($p)=>strtolower($p->is_featured)==='yes')
                                        ])
                                    </div>

                                    <!-- مباعة -->
                                    <div class="tab-pane table-responsive userprof-tab" id="tab-sold">
                                        @include('agent.property.partials.table', [
                                            'rows' => $properties->where('status','Sold')
                                        ])
                                    </div>

                                    <!-- للبيع -->
                                    <div class="tab-pane table-responsive userprof-tab" id="tab-sale">
                                        @include('agent.property.partials.table', [
                                            'rows' => $properties->filter(fn($p)=>strtolower($p->purpose)==='buy')
                                        ])
                                    </div>

                                    <!-- للإيجار -->
                                    <div class="tab-pane table-responsive userprof-tab" id="tab-rent">
                                        @include('agent.property.partials.table', [
                                            'rows' => $properties->filter(fn($p)=>strtolower($p->purpose)==='rent')
                                        ])
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/All-properties-->
@endsection
