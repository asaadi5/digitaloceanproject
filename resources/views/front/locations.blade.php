@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">المحافظات</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->
    <!--Locations (المحافظات)-->
    <section class="sptb bg-white">
        <div class="container">
            <div class="section-title center-block text-center">
                <h2>المحافظات</h2>
                <p>اختر محافظة لعرض العقارات المتاحة فيها</p>
            </div>
            <div class="row">
                @forelse($locations as $item)
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="item-card">
                                <div class="item-card-desc">
                                    <a href="{{ route('location', $item->slug) }}"></a>
                                    <div class="item-card-img">
                                        <img src="{{ $item->photo ? asset('uploads/'.$item->photo) : asset('assets/images/products/co1.png') }}"
                                             alt="{{ $item->name }}"
                                             class="br-tr-7 br-tl-7">
                                    </div>
                                    <div class="item-card-text">
                                        <h4 class="mb-0">{{ $item->name }}</h4>
                                        <span class="badge rounded-pill badge-primary w-15">
                                            {{ $item->properties_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center mb-0">
                            لا توجد محافظات متاحة حالياً.
                        </div>
                    </div>
                @endforelse
                <div class="col-md-12 mt-4 d-flex justify-content-center">
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </section>
    <!--/Locations-->
@endsection
