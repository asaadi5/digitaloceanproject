@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">تفاصيل العقار</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <section class="sptb">
        <div class="container">
            <div class="row">
                <!-- يسار: المحتوى الرئيسي -->
                <div class="col-xl-8 col-lg-8 col-md-12">

                    <!--Classified Description-->
                    <div class="card overflow-hidden">
                        <div class="ribbon ribbon-top-right">
                            <span class="{{ $isRent ? 'bg-warning' : 'bg-success' }}">
                            {{ $isRent ? 'للإيجار' : 'للبيع' }}
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="item-det mb-4">
                                <a href="javascript:void(0);" class="text-dark">
                                    <h3 class="">{{ $property->name }}</h3>
                                </a>
                                <ul class="d-flex">
                                    <li class="me-5">
                                        <a href="javascript:void(0);" class="icons">
                                            <i class="icon icon-briefcase text-muted me-1"></i> {{ optional($property->type)->name }}
                                        </a>
                                    </li>
                                    <li class="me-5">
                                        <a href="javascript:void(0);" class="icons">
                                            <i class="icon icon-location-pin text-muted me-1"></i> {{ optional($property->location)->name }}
                                        </a>
                                    </li>
                                    <li class="me-5">
                                        <a href="javascript:void(0);" class="icons">
                                            <i class="icon icon-calendar text-muted me-1"></i> {{ optional($property->created_at)->diffForHumans() }}
                                        </a>
                                    </li>
                                    <li class="me-5">
                                        <a href="javascript:void(0);" class="icons">
                                            <i class="icon icon-eye text-muted me-1"></i> {{ num($property->total_views ?? 0) }}
                                        </a>
                                    </li>
                                    {{-- تقييم ثابت شكلي كما في القالب --}}

                                </ul>
                            </div>

                            {{-- المعرض بنفس الشكل --}}
                            <div class="product-slider carousel-slide-1">
                                <div id="carouselFade" class="carousel slide carousel-fade" data-bs-ride="carousel"
                                     data-bs-loop="false" data-bs-thumb="true" data-bs-dots="false">
                                    <div class="arrow-ribbon2 bg-primary">
                                        {{ num($property->price) }} $

                                    </div>
                                    <div class="carousel-inner slide-show-image" id="full-gallery">
                                        @php $first = true; @endphp
                                        @forelse($property->photos as $ph)
                                            <div class="carousel-item {{ $first ? 'active' : '' }}">
                                                <img src="{{ asset('uploads/'.$ph->photo) }}" alt="img">
                                            </div>
                                            @php $first = false; @endphp
                                        @empty
                                            <div class="carousel-item active">
                                                <img src="{{ asset('uploads/'.$property->featured_photo) }}" alt="img">
                                            </div>
                                        @endforelse
                                        <div class="thumbcarousel">
                                            <a class="carousel-control-prev" href="#carouselFade" role="button" data-bs-slide="prev">
                                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                                            </a>
                                            <a class="carousel-control-next" href="#carouselFade" role="button" data-bs-slide="next">
                                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- /المعرض --}}
                        </div>
                    </div>

                    <div class="border-0">
                        <div class="wideget-user-tab wideget-user-tab3">
                            <div class="tab-menu-heading">
                                <div class="tabs-menu1">
                                    <ul class="nav">
                                        <li class=""><a href="#tab-1" class="active" data-bs-toggle="tab">الوصف</a></li>
                                        <li><a href="#tab-3" data-bs-toggle="tab" class="">المواصفات</a></li>
                                        <li><a href="#tab-4" data-bs-toggle="tab" class="">معلومات إضافية</a></li>
                                        <li><a href="#tab-5" data-bs-toggle="tab" class="">فيديو</a></li>

                                        {{-- يظهران فقط عند الإيجار --}}
                                        @if(!$isRent && $property->documents->isNotEmpty())
                                            <li><a href="#tab-docs" data-bs-toggle="tab" class="">الوثائق</a></li>
                                        @endif
                                        @if($isRent )
                                            <li><a href="#tab-rules" data-bs-toggle="tab" class="">شروط الإيجار</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content border br-tr-3 br-tl-3 p-5 bg-white details-tab-content">
                            {{-- الوصف + جدول مواصفات مختصر (كما في الشكل الأصلي تمامًا) --}}
                            <div class="tab-pane active" id="tab-1">
                                <h3 class="card-title mb-3 font-weight-semibold">الوصف</h3>
                                <div class="mb-4">
                                    <p>{!! nl2br(e($property->description)) !!}</p>
                                </div>

                                <h4 class="mb-4">مواصفات مختصرة</h4>
                                <div class="row">
                                    <div class="col-xl-12 col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered border-top mb-0">
                                                <tbody>
                                                <tr>
                                                    <td>النوع</td>
                                                    <td><span class="font-weight-bold">{{ optional($property->type)->name ?? '-' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>الإدراج بواسطة</td>
                                                    <td><span class="font-weight-bold">{{ optional($property->agent)->name ?? 'مُعلن' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>غرف النوم</td>
                                                    <td><span class="font-weight-bold">{{ num($property->bedroom) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>الحمّامات</td>
                                                    <td><span class="font-weight-bold">{{ num($property->bathroom) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>التأثيث</td>
                                                    <td><span class="font-weight-bold">{{ $property->furnished ?? '-' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>مواقف سيارات</td>
                                                    <td><span class="font-weight-bold">{{ num($property->garage) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>عدد الطوابق</td>
                                                    <td><span class="font-weight-bold">{{ num($property->floor) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>الواجهة</td>
                                                    <td><span class="font-weight-bold">{{ $property->facing ?? '-' }}</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- المواصفات (أيقونات صح/غلط كما في الشكل) --}}
                            <div class="tab-pane" id="tab-3">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered border-top mb-0">
                                                <tbody>
                                                <tr><td>غرف النوم</td><td><i class="fa {{ $property->bedroom ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>الحمّامات</td><td><i class="fa {{ $property->bathroom ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>موقف سيارات</td><td><i class="fa {{ $property->garage ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>مفروش</td><td><i class="fa {{ ($property->furnished ?? null) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>واجهة</td><td><i class="fa {{ ($property->facing ?? null) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>مصعد</td><td><i class="fa {{ ($property->has_lift ?? null) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>مسبح</td><td><i class="fa {{ ($property->has_pool ?? null) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                <tr><td>حراسة</td><td><i class="fa {{ ($property->has_security ?? null) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($amenities))
                                    <hr>
                                    <h6 class="mb-3">المرافق</h6>
                                    <div class="row">
                                        @foreach($amenities as $am)
                                            <div class="col-md-4 mb-2"><i class="fa fa-check-circle text-success me-2"></i>{{ $am }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- معلومات إضافية --}}
                            <div class="tab-pane" id="tab-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered border-top mb-0">
                                                <tbody>
                                                <tr><td>النوع</td><td><span class="font-weight-bold">{{ optional($property->type)->name ?? '-' }}</span></td></tr>
                                                <tr><td>الحمّامات</td><td><span class="font-weight-bold">{{ num($property->bathroom) }}</span></td></tr>
                                                <tr><td>المساحة</td><td><span class="font-weight-bold">{{ num($property->size) }} م²</span></td></tr>
                                                <tr><td>الواجهة</td><td><span class="font-weight-bold">{{ $property->facing ?? '-' }}</span></td></tr>
                                                <tr><td>غرف النوم</td><td><span class="font-weight-bold">{{ num($property->bedroom) }}</span></td></tr>
                                                <tr><td>التأثيث</td><td><span class="font-weight-bold">{{ $property->furnished ?? '-' }}</span></td></tr>
                                                <tr><td>بلكونة</td><td><span class="font-weight-bold">{{ num($property->balcony) }}</span></td></tr>
                                                <tr><td>مصعد</td><td><span class="font-weight-bold">{{ ($property->has_lift ?? null) ? 'نعم' : 'لا' }}</span></td></tr>
                                                <tr><td>مسبح</td><td><span class="font-weight-bold">{{ ($property->has_pool ?? null) ? 'نعم' : 'لا' }}</span></td></tr>
                                                <tr><td>حديقة</td><td><span class="font-weight-bold">{{ ($property->has_garden ?? null) ? 'نعم' : 'لا' }}</span></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($property->map))
                                    <hr>
                                    <h6 class="mb-3">الموقع على الخريطة</h6>
                                    <div class="ratio ratio-16x9 br-5" id="map2">
                                        {!! $property->map !!}
                                    </div>
                                @endif
                            </div>

                            {{-- الفيديو (نفس الشكل: صورة مصغّرة تفتح الرابط) --}}
                            <div class="tab-pane" id="tab-5">
                                <ul class="list-unstyled video-list-thumbs row">
                                    @forelse($property->videos as $v)
                                        @php
                                            $raw  = $v->video ?? null;
                                            $url  = null; $file = null;
                                            if ($raw) {
                                                if (\Illuminate\Support\Str::startsWith($raw, ['http://','https://'])) {
                                                    $url = $raw;
                                                } elseif (preg_match('/^[A-Za-z0-9_-]{11}$/', $raw)) {
                                                    $url = 'https://www.youtube.com/watch?v='.$raw;
                                                } else {
                                                    $file = $raw;
                                                }
                                            }
                                            $thumb = '../assets/images/products/products/h1.jpg'; // صورة شكلية كما في القالب
                                        @endphp
                                        <li class="mb-0">
                                            @if($url)
                                                <a href="{{ $url }}" target="_blank">
                                                    <img src="{{ $thumb }}" alt="video" class="img-responsive">
                                                    <span class="mdi mdi-arrow-right-drop-circle-outline text-white"></span>
                                                </a>
                                            @elseif($file)
                                                <a href="{{ asset($file) }}" target="_blank">
                                                    <img src="{{ $thumb }}" alt="video" class="img-responsive">
                                                    <span class="mdi mdi-arrow-right-drop-circle-outline text-white"></span>
                                                </a>
                                            @else
                                                <img src="{{ $thumb }}" alt="video" class="img-responsive">
                                            @endif
                                        </li>
                                    @empty
                                        <li class="mb-0"><p class="text-muted">لا يوجد فيديو.</p></li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- الوثائق — يظهر فقط عند البيع --}}
                            @if(!$isRent && $property->documents->isNotEmpty())
                                <div class="tab-pane" id="tab-docs">
                                    <h5 class="mb-3">الوثائق</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>النوع</th>
                                                <th>الجهة</th>
                                                <th>الرقم</th>
                                                <th>تاريخ الإصدار</th>
                                                <th>تحميل</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($property->documents as $doc)
                                                @php
                                                    $raw = $doc->file_path ?? null;
                                                    $href = $raw
                                                        ? (\Illuminate\Support\Str::startsWith($raw,['http://','https://']) ? $raw : asset($raw))
                                                        : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $doc->doc_type }}</td>
                                                    <td>{{ $doc->issuer }}</td>
                                                    <td>{{ $doc->doc_no }}</td>
                                                    <td>{{ optional($doc->issued_at)->format('Y-m-d') }}</td>
                                                    <td>
                                                        @if($href)
                                                            <a href="{{ $href }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fa fa-download"></i> فتح
                                                            </a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- شروط الإيجار — يظهر فقط عند الإيجار --}}
                            @if($isRent )
                                <div class="tab-pane" id="tab-rules">
                                    <h5 class="mb-3">شروط الإيجار</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>البند</th>
                                                <th>القيمة</th>
                                                <th>إلزامي</th>
                                                <th>ملاحظات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($rentalRules as $r)
                                                <tr>
                                                    <td>{{ $r->rule_key }}</td>
                                                    <td>{{ $r->rule_value }}</td>
                                                    <td>{!! ($r->is_enforced ?? 0) ? '<span class="badge bg-success">نعم</span>' : '<span class="badge bg-secondary">اختياري</span>' !!}</td>
                                                    <td>{{ $r->notes }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="py-4 px-5 border border-top-0 border-bottom-0 bg-white">
                            <div class="list-id">
                                <div class="row">
                                    <div class="col">
                                        <a class="mb-0">رقم الإعلان : #{{ $property->id }}</a>
                                    </div>
                                    <div class="col col-auto">
                                        المُعلن: <a class="mb-0 font-weight-bold" href="{{ route('agent', $property->agent->id) }}">{{ optional($property->agent)->name }}</a> /
                                        {{ optional($property->created_at)->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white details-tab border">
                            <div class="icons">
                                <a href="javascript:void(0);" class="btn btn-info icons"><i class="icon icon-share me-1"></i> مشاركة</a>
                                <a href="javascript:void(0);" class="btn btn-primary icons"><i class="icon icon-heart me-1"></i> {{ num($property->favorites_count ?? 678) }}</a>
                                <a href="javascript:void(0);" class="btn btn-secondary icons"><i class="icon icon-printer me-1"></i> طباعة</a>
                            </div>
                        </div>
                    </div>

                    <h3 class="mb-5 mt-4">عقارات مشابهة</h3>
                    <div id="myCarousel5" class="owl-carousel owl-carousel-icons3">
                        @foreach($related as $rp)
                            <div class="item">
                                <div class="card">
                                    <div class="arrow-ribbon {{ (Str::lower($rp->purpose) === 'rent') ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ (Str::lower($rp->purpose) === 'rent') ? 'للإيجار' : 'للبيع' }}
                                    </div>
                                    <div class="item-card7-imgs">
                                        <a href="{{ route('property_detail', $rp->slug) }}"></a>
                                        <img src="{{ asset('uploads/'.$rp->featured_photo) }}" alt="img" class="cover-image">
                                    </div>
                                    <div class="item-card7-overlaytext">
                                        <a href="{{ route('property_detail', $rp->slug) }}" class="text-white">{{ optional($rp->type)->name }}</a>
                                        <h4 class="mb-0">{{ num($rp->price) }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <a href="{{ route('property_detail', $rp->slug) }}" class="text-dark"><h4 class="font-weight-semibold">{{ $rp->name }}</h4></a>
                                        </div>
                                        <div class="item-card7-text">
                                            <ul class="icon-card mb-0">
                                                <li><a href="javascript:void(0);" class="icons"><i class="icon icon-location-pin text-muted me-1"></i> {{ optional($rp->location)->name }}</a></li>
                                                <li><a href="javascript:void(0);" class="icons"><i class="icon icon-event text-muted me-1"></i> {{ optional($rp->created_at)->diffForHumans() }}</a></li>
                                                <li class="mb-0"><a href="javascript:void(0);" class="icons"><i class="icon icon-user text-muted me-1"></i> {{ optional($rp->agent)->name }}</a></li>
                                            </ul>
                                            <p class="mb-0"> المساحة {{ num($rp->size) }} م²</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!--/Related Posts-->
                </div>

                <!-- يمين: المُعلن + عناصر إضافية -->
                <div class="col-xl-4 col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">المُعلن</h3>
                        </div>
                        <div class="card-body item-user">
                            <div class="profile-pic mb-0">
                                <img src="{{ asset('uploads/'.(optional($property->agent)->photo)) }}" class="brround avatar-xxl" alt="user">
                                <div class="">
                                    <a href="{{ route('agent', $property->agent->id) }}" class="text-dark">
                                        <h4 class="mt-3 mb-1 font-weight-semibold">{{ optional($property->agent)->name }}</h4>
                                    </a>
                                    <p class="mb-0">وكيل عقاري</p>
                                    <span class="text-muted">الهاتف: {{ optional($property->agent)->phone }}</span>
                                    <h6 class="mt-2 mb-0">
                                        <a href="{{ route('agent', $property->agent->id) }}" class="btn btn-primary btn-sm">كل إعلاناته</a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body item-user">
                            <h4 class="mb-4">معلومات التواصل</h4>
                            <div>
                                <h6><span class="font-weight-semibold"><i class="fa fa-map-marker me-2 mb-2"></i></span><span class="text-body">{{ $property->address }}</span></h6>
                                <h6><span class="font-weight-semibold"><i class="fa fa-envelope me-2 mb-2"></i></span><span class="text-body">{{ optional($property->agent)->email }}</span></h6>
                                <h6><span class="font-weight-semibold"><i class="fa fa-phone me-2 mb-2"></i></span><span class="text-body">{{ optional($property->agent)->phone }}</span></h6>
                            </div>
                            <div class="item-user-icons mt-4">
                                <a href="javascript:void(0);" class="facebook-bg mt-0"><i class="fa fa-whatsapp"></i></a>
                                <a href="javascript:void(0);" class="twitter-bg"><i class="fa fa-twitter"></i></a>
                                <a href="javascript:void(0);" class="google-bg"><i class="fa fa-telegram"></i></a>
                                <a href="javascript:void(0);" class="dribbble-bg"><i class="fa fa-facebook"></i></a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-start">
                                <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contact"><i class="fa fa-user"></i> إرسال رسالة</a>
                            </div>
                        </div>
                    </div>

                    @if($agentLatest->count())
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">آخر إعلانات المُعلن</h3>
                            </div>
                            <div class="card-body pb-3">
                                <div class="rated-products">
                                    <ul class="vertical-scroll">
                                        @foreach($agentLatest as $ap)
                                            <li class="item">
                                                <div class="media p-5 mt-0">
                                                    <a href="{{ route('property_detail', $ap->slug) }}" class="me-4">
                                                        <img class="" src="{{ asset('uploads/'.$ap->featured_photo) }}" alt="img" style="width:64px;height:48px;object-fit:cover">
                                                    </a>
                                                    <div class="media-body">
                                                        <a href="{{ route('property_detail', $ap->slug) }}"><h4 class="mt-2 mb-1">{{ \Illuminate\Support\Str::limit($ap->name, 40) }}</h4></a>
                                                        <div class="h5 mb-0 font-weight-semibold mt-1">{{ num($ap->price) }} {{ $ap->currency ?? '$' }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">الموقع على الخريطة</h3>
                        </div>
                        <div class="card-body">
                            <div class="map-header">
                                <div class="map-header-layer" id="map2">
                                    {!! $property->map !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($latestProperties->count())
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">أحدث العقارات</h3>
                            </div>
                            <div class="card-body pb-3">
                                <ul class="vertical-scroll">
                                    @foreach($latestProperties as $lp)
                                        <li class="news-item">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('property_detail', $lp->slug) }}">
                                                            <img src="{{ asset('uploads/'.$lp->featured_photo) }}" alt="img" class="w-8 border" style="width:48px;height:48px;object-fit:cover"/>
                                                        </a>
                                                    </td>
                                                    <td class="ps-4">
                                                        <h5 class="mb-1 "><a class="btn-link" href="{{ route('property_detail', $lp->slug) }}">{{ \Illuminate\Support\Str::limit($lp->name, 28) }}</a></h5>
                                                        <span class="float-end font-weight-bold">{{ num($lp->price) }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                <!--/Right Side Content-->
            </div>
        </div>
    </section>
@endsection
