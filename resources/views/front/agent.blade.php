@extends('front.layouts.master')

@php
    use App\Models\Property;


    // الصورة
    $avatar = $agent->photo ? asset('uploads/'.$agent->photo) : asset('assets/images/users/default.png');

    // اسم المحافظة / المدينة إن متاح
    $locationText = trim(collect([
        'سوريا',
        optional($agent->location)->name ?? $agent->city,
        $agent->address
    ])->filter()->join(' - '));

    // تاريخ الانضمام (مع fallback لو ما عندك ar_date)
    $joined = function($dt){
        try {
            return function_exists('ar_date')
                ? ar_date($dt)
                : \Carbon\Carbon::parse($dt)->locale('ar')->translatedFormat('MMMM Y');
        } catch (\Throwable $e) {
            return (string)$dt;
        }
    };

    // عدّاد الإعلانات المنشورة
    $totalAds = $properties->total();

    // أدوات سوشال (نظهر فقط الموجود)
    $socials = [
        ['icon' => 'fa-facebook',  'class' => 'facebook-bg', 'url' => $agent->facebook ?? null],
        ['icon' => 'fa-twitter',   'class' => 'twitter-bg',  'url' => $agent->twitter  ?? null],
        ['icon' => 'fa-telegram',  'class' => 'google-bg',   'url' => $agent->telegram ?? null],
];



    // ترتيب حسب الفلتر الحالي (حتى يضل الشغل تبع الـ select)
    $sort = request('sort','newest');

    $q = Property::with(['type','location'])
        ->where('agent_id', $agent->id);

    switch ($sort) {
        case 'oldest':     $q->orderBy('id', 'asc'); break;
        case 'price_asc':  $q->orderBy('price', 'asc'); break;
        case 'price_desc': $q->orderBy('price', 'desc'); break;
        default:           $q->orderBy('id', 'desc'); // newest
    }

    // هنا بنجيب "كل" النتائج بدل paginate
    $properties = $q->get();

    // عداد الإعلانات
    $totalAds = $properties->count();

@endphp

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3 sptb-2" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">تفاصيل الوكيل</h1>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    {{-- ===== الهيدر كما في القالب ===== --}}
    <section class="sptb">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    {{-- بطاقة رأس الصفحة (الصورة + الاسم + سوشال + التابات) --}}
                    <div class="card">
                        <div class="card-body pattern-1">
                            <div class="wideget-user">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="wideget-user-desc text-center">
                                            <style>
                                                .agent-avatar{
                                                    width:120px;
                                                    height:120px;
                                                    object-fit:cover;
                                                    border-radius:50%;
                                                }
                                            </style>

                                            <div class="wideget-user-img">
                                                <img src="{{ $agent->photo ? asset('uploads/'.$agent->photo) : asset('assets/images/users/default.png') }}"
                                                     alt="img" class="brround agent-avatar">
                                            </div>

                                            <div class="user-wrap wideget-user-info">
                                                <a href="javascript:void(0);" class="text-white">
                                                    <h4 class="font-weight-semibold">{{ $agent->name }}</h4>
                                                </a>
                                                <span class="text-white">عضو منذ {{ $joined($agent->created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- أيقونات السوشال (نظهر فقط الروابط المتاحة) --}}
                                    <div class="col-lg-12 col-md-12 text-center">
                                        <div class="wideget-user-info">
                                            <div class="wideget-user-icons mt-2">
                                                @forelse($socials as $s)
                                                    @if($s['url'])
                                                        <a href="{{ $s['url'] }}" target="_blank" rel="noopener" class="{{ $s['class'] }} mt-0"
                                                           data-bs-toggle="tooltip" data-bs-placement="top" title="">
                                                            <i class="fa {{ $s['icon'] }}"></i>
                                                        </a>
                                                    @endif
                                                @empty
                                                    {{-- لا شيء --}}
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- /row --}}
                            </div> {{-- /wideget-user --}}
                        </div>

                        {{-- التابات --}}
                        <div class="card-footer">
                            <div class="wideget-user-tab">
                                <div class="tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <ul class="nav">
                                            <li class=""><a href="#tab-1" class="active" data-bs-toggle="tab">الملف الشخصي</a></li>
                                            <li>
                                                <a href="#tab-2" data-bs-toggle="tab" class="">
                                                    الإعلانات المنشورة
                                                    <span class="badge badge-primary rounded-pill">{{ $totalAds }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div> {{-- /tabs-menu1 --}}
                                </div>
                            </div>
                        </div>
                    </div> {{-- /card header --}}

                    {{-- محتوى التابات --}}
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="border-0">
                                <div class="tab-content">

                                    {{-- تبويب 1: الملف الشخصي --}}
                                    <div class="tab-pane active" id="tab-1">
                                        <div class="profile-log-switch">
                                            <div class="media-heading">
                                                <h3 class="card-title mb-3 font-weight-bold">البيانات شخصية</h3>
                                            </div>
                                            <ul class="usertab-list mb-0">
                                                @if($agent->name)
                                                    <li><a class="text-dark"><span class="font-weight-semibold">الاسم الكامل : </span> {{ $agent->name }}</a></li>
                                                @endif
                                                @if($locationText)
                                                    <li><a class="text-dark"><span class="font-weight-semibold">الموقع : </span> {{ $locationText }}</a></li>
                                                @endif
                                                @if($agent->languages ?? null)
                                                    <li><a class="text-dark"><span class="font-weight-semibold">اللغات : </span> {{ $agent->languages }}</a></li>
                                                @endif
                                                @if($agent->email)
                                                    <li><a class="text-dark"><span class="font-weight-semibold">البريد الإلكتروني : </span> {{ $agent->email }}</a></li>
                                                @endif
                                                @if($agent->phone)
                                                    <li><a class="text-dark"><span class="font-weight-semibold">الهاتف : </span> {{ $agent->phone }}</a></li>
                                                @endif
                                            </ul>

                                            {{-- نبذة تعريفية --}}
                                            @if($agent->biography ?? $agent->about)
                                                <div class="row profie-img mt-4">
                                                    <div class="col-md-12">
                                                        <div class="media-heading">
                                                            <h3 class="card-title mb-3 font-weight-bold">نبذة تعريفية</h3>
                                                        </div>
                                                        <p class="mb-0">{{ $agent->biography ?? $agent->about }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- تبويب 2: الإعلانات المنشورة — نفس منطق عرض البحث (List / Grid + Sort + Pagination) --}}
                                    <div class="tab-pane userprof-tab" id="tab-2">
                                        <div class="table-responsive">

                                            {{-- شريط التحكم (عرض/ترتيب) --}}
                                            <div class="mb-0">
                                                <div class="">
                                                    <div class="p-5 bg-white item2-gl-nav d-flex border br-5">
                                                        <h6 class="mb-0 mt-2">
                                                            عرض {{ $properties->count() }} من {{ $properties->count() }}
                                                        </h6>

                                                        <ul class="nav item2-gl-menu ms-auto mt-2">
                                                            <li class="">
                                                                <a href="#tab-11" class="" data-bs-toggle="tab" title="List style"><i class="fa fa-list"></i></a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab-12" data-bs-toggle="tab" class="active show" title="Grid"><i class="fa fa-th"></i></a>
                                                            </li>
                                                        </ul>

                                                        <div class="d-flex">
                                                            <label class="me-2 mt-1 mb-sm-1 pt-2">ترتيب حسب : </label>
                                                            <form method="get" action="{{ url()->current() }}" class="d-flex">
                                                                @foreach(request()->except('sort') as $k=>$v)
                                                                    @if(is_array($v))
                                                                        @foreach($v as $vv)
                                                                            <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                                                        @endforeach
                                                                    @else
                                                                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                                                    @endif
                                                                @endforeach
                                                                <select name="sort" class="form-control select-sm w-75 select2" onchange="this.form.submit()">
                                                                    <option value="newest"     {{ request('sort','newest')==='newest' ? 'selected' : '' }}>الأحدث</option>
                                                                    <option value="oldest"     {{ request('sort')==='oldest' ? 'selected' : '' }}>الأقدم</option>
                                                                    <option value="price_asc"  {{ request('sort')==='price_asc' ? 'selected' : '' }}>السعر : من الأدنى للأعلى</option>
                                                                    <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>السعر : من الأعلى للأدنى</option>
                                                                </select>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- المحتوى (List / Grid) --}}
                                            <div class="tab-content">

                                                {{-- تبويب القائمة (List) --}}
                                                <div class="tab-pane" id="tab-11">
                                                    @forelse($properties as $p)
                                                        @php
                                                            $type     = optional($p->type);
                                                            $parentId = (int) ($type->parent_id ?? 0);

                                                            $isResi   = ($parentId === 1); // سكني
                                                            $isRecre  = ($parentId === 3); // ترفيهي
                                                            $typeName = trim((string) ($type->name ?? ''));

                                                            // الغرف/الحمامات فقط للسكني + الترفيهي
                                                            $showRooms = $isResi || $isRecre;

                                                            // المواقف فقط لبعض الأنواع
                                                            $garageAllowed = ['فيلا','مكتب','محل','محل تجاري','دكان'];
                                                            $showGarage = in_array($typeName,$garageAllowed,true);
                                                        @endphp

                                                        <div class="card overflow-hidden">
                                                            <div class="d-md-flex">
                                                                <div class="item-card9-img">
                                                                    <div class="arrow-ribbon {{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }}">
                                                                        {{ number_format($p->price) }}
                                                                    </div>
                                                                    <div class="item-card9-imgs">
                                                                        <a href="{{ route('property_detail',$p->slug) }}"></a>
                                                                        <img src="{{ asset('uploads/'.$p->featured_photo) }}" alt="{{ $p->name }}" class="cover-image">
                                                                    </div>
                                                                    <div class="item-tags">
                                                                        <div class="{{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }} tag-option">
                                                                            {{ in_array($p->purpose,['rent','إيجار']) ? 'للإيجار' : 'للبيع' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card border-0 mb-0">
                                                                    <div class="card-body ">
                                                                        <div class="item-card9">
                                                                            <a href="{{ route('property_detail',$p->slug) }}" class="text-dark">
                                                                                <h4 class="font-weight-bold mt-1">{{ $p->name }}</h4>
                                                                            </a>
                                                                            <ul class="item-card2-list">
                                                                                @if($p->size)<li><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</li>@endif
                                                                                @if($showRooms && $p->bedroom)<li><i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }} غرف</li>@endif
                                                                                @if($showRooms && $p->bathroom)<li><i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }} حمام</li>@endif
                                                                                @if($showGarage && $p->garage)<li><i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} {{ $p->garage==1?'موقف':'مواقف' }}</li>@endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer pt-4 pb-4">
                                                                        <div class="item-card9-footer d-flex">
                                                                            <div class="item-card9-cost">
                                                                                <span><i class="fa fa-map-marker text-muted me-1"></i> {{ $p->address }}</span>
                                                                            </div>
                                                                            <div class="ms-auto">
                                                                                <span><i class="fa fa-calendar-o text-muted me-1"></i> {{ optional($p->created_at)->diffForHumans() }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="card"><div class="card-body text-center py-6">لا توجد إعلانات لهذا الوكيل.</div></div>
                                                    @endforelse
                                                </div>

                                                {{-- تبويب الشبكة (Grid) --}}
                                                <div class="tab-pane active" id="tab-12">
                                                    <div class="row">
                                                        @forelse ($properties as $p)
                                                            @php
                                                                $type     = optional($p->type);
                                                                $parentId = (int) ($type->parent_id ?? 0);

                                                                $isResi   = ($parentId === 1);
                                                                $isRecre  = ($parentId === 3);
                                                                $typeName = trim((string) ($type->name ?? ''));

                                                                $showRooms = $isResi || $isRecre;
                                                                $garageAllowed = ['فيلا','مكتب','محل','محل تجاري','دكان'];
                                                                $showGarage = in_array($typeName,$garageAllowed,true);
                                                            @endphp

                                                            <div class="col-lg-6 col-md-12 col-xl-4">
                                                                <div class="card overflow-hidden">
                                                                    <div class="item-card9-img">
                                                                        <div class="arrow-ribbon {{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }}">{{ number_format($p->price) }}</div>
                                                                        <div class="item-card9-imgs">
                                                                            <a href="{{ route('property_detail',$p->slug) }}"></a>
                                                                            <img src="{{ asset('uploads/'.$p->featured_photo) }}" alt="{{ $p->name }}" class="cover-image">
                                                                        </div>

                                                                        {{-- زر المفضلة (القلب) أعلى اليمين - يستعمل كومبوننتك) --}}
                                                                        <div class="item-card2-icons">
                                                                            @include('components.wish.btn', [
                                                                                'propertyId' => $p->id,
                                                                                'wished'     => (bool)($p->wishlisted ?? 0)
                                                                            ])
                                                                        </div>

                                                                        <div class="item-tags">
                                                                            <div class="{{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }} tag-option">
                                                                                {{ in_array($p->purpose,['rent','إيجار']) ? 'للإيجار' : 'للبيع' }}
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card-body">
                                                                        <div class="item-card9">
                                                                            <a href="{{ route('property_detail',$p->slug) }}" class="text-dark">
                                                                                <h4 class="font-weight-bold mt-1">{{ $p->name }}</h4>
                                                                            </a>
                                                                            <ul class="item-card2-list">
                                                                                @if($p->size)<li><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</li>@endif
                                                                                @if($showRooms && $p->bedroom)<li><i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }} غرف</li>@endif
                                                                                @if($showRooms && $p->bathroom)<li><i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }} حمام</li>@endif
                                                                                @if($showGarage && $p->garage)<li><i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} {{ $p->garage==1?'موقف':'مواقف' }}</li>@endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card-footer">
                                                                        <div class="item-card9-footer d-flex">
                                                                            <div class="item-card9-cost"><span><i class="fa fa-map-marker text-muted me-1"></i> {{ $p->address }}</span></div>
                                                                            <div class="ms-auto"><span><i class="fa fa-calendar-o text-muted me-1"></i> {{ optional($p->created_at)->diffForHumans() }}</span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12">
                                                                <div class="card"><div class="card-body text-center py-6">لا توجد إعلانات لهذا الوكيل.</div></div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                            </div>{{-- /tab-content (داخل الإعلانات) --}}

                                        </div> {{-- /table-responsive --}}


                                    </div> {{-- /tab-2 --}}

                                </div>{{-- /tab-content --}}
                            </div>
                        </div>
                    </div>{{-- /card محتوى --}}
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // مفتاح التخزين
            var KEY = 'agentPageActiveTab';

            // دالة تفعيل تبويب بالـ href (مثل "#tab-2")
            function activateTab(hash) {
                if (!hash) return;
                var trigger = document.querySelector('a[data-bs-toggle="tab"][href="' + hash + '"]');
                if (trigger && window.bootstrap && bootstrap.Tab) {
                    new bootstrap.Tab(trigger).show();
                }
            }

            // 1) عند التحميل: جرّب تفعيل آخر تبويب محفوظ
            var saved = sessionStorage.getItem(KEY);
            if (saved) {
                activateTab(saved);
            } else if (location.hash) {
                // اختياري: لو في hash بالـ URL، فعّله أول مرة فقط
                activateTab(location.hash);
            }

            // 2) كل ما يتغير التاب بالنقر: خزّنه
            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (el) {
                el.addEventListener('shown.bs.tab', function (e) {
                    var current = e.target.getAttribute('href'); // مثال: "#tab-2"
                    sessionStorage.setItem(KEY, current);

                    // اختياري: حدّث الـ URL بدون ما يسبب تنقّل
                    history.replaceState(null, '', current);
                });
            });
        });
    </script>

@endsection

