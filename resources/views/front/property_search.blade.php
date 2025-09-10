{{--
    صفحة نتائج البحث للعقارات (Blade View)
    - الحفاظ على الشكل والسلوك كما هو 100%
    - إضافة تعليقات عربية على مستوى الأقسام
    - إضافة تعليقات داخلية بالإنجليزية عند الحاجة
--}}

@extends('front.layouts.master')

@section('main_content')

    {{-- ───────────────────────────── Breadcrumb / عنوان الصفحة ─────────────────────────────
        الغرض: إظهار البنر العلوي مع عنوان الصفحة الديناميكي ($pageTitle) أو الافتراضي "نتائج البحث"
        الملاحظة: لا تعديل على الهيكل أو الكلاسات لضمان نفس التصميم
    ─────────────────────────────────────────────────────────────────────────────── --}}
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">{{ $pageTitle ?? 'نتائج البحث' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    {{-- ───────────────────────────ـ قسم النتائج + الفلاتر (Layout) ───────────────────────────
        الغرض: توزيع العمودين (الفلاتر يساراً + النتائج يميناً)
        الملاحظة: المحافظة التامة على الشبكة (Bootstrap) والكلاسات
    ─────────────────────────────────────────────────────────────────────────────── --}}
    <section class="sptb">
        <div class="container">
            <div class="row">

                {{-- ───────────────────────── العمود الأيسر: صندوق البحث + الفلاتر ─────────────────────────
                    يتضمن:
                    - مربع بحث نصّي سريع (يرسل إلى نفس نموذج الفلترة)
                    - نموذج الفلاتر (فئات/سعر/حالة/خصائص…)
                ─────────────────────────────────────────────────────────────────────────────── --}}
                <div class="col-xl-3 col-lg-4 col-md-12">

                    {{-- كرت البحث العلوي كما هو --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="input-group">
                                {{-- English: "Bind search input to the same filter form via `form` attribute." --}}
                                <input type="text" class="form-control br-tl-3  br-bl-3" name="name" form="filterForm"
                                       value="{{ request('name') }}" placeholder="بحث">
                                <button type="submit" form="filterForm" class="btn btn-primary br-tr-3  br-br-3">بحث
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- كرت الفلاتر (نفس السلوك) --}}
                    <form id="filterForm" method="get" action="{{ url()->current() }}">
                        <div class="card">

                            {{-- ===== الفئات ===== --}}
                            <div class="card-header">
                                <h3 class="card-title">الفئات</h3>
                            </div>

                            <div class="card-body">
                                <div id="container">
                                    <div class="filter-product-checkboxs">

                                        @php
                                            use App\Models\Type;

                                            // English: "Pull current selections from request to reflect UI state."
                                            // المدخلات القادمة من الرابط/الكويري:
                                            $selTypeRaw   = request('type');              // يمكن أن يكون ID رقمي أو slug نصّي
                                            $selCategory  = (int) request('category_id'); // 1=سكني، 3=ترفيهي

                                            // English: "Normalize type to ID if provided as slug or (partial) name."
                                            // حوّل الـ type إلى ID إن كان slug أو اسم:
                                            $selTypeId = null;
                                            if (is_numeric($selTypeRaw)) {
                                                $selTypeId = (int) $selTypeRaw;
                                            } elseif (!empty($selTypeRaw)) {
                                                $selTypeId = Type::where('slug', $selTypeRaw)
                                                    ->orWhere('name', 'like', '%'.$selTypeRaw.'%')
                                                    ->value('id');
                                            }

                                            // English: "Collect child type IDs for Residential (1) and Recreational (3)."
                                            // كل الأنواع الفرعية للسكني/الترفيهي:
                                            $resiTypeIds  = Type::where('parent_id', 1)->pluck('id')->toArray();
                                            $recreTypeIds = Type::where('parent_id', 3)->pluck('id')->toArray();

                                            // English: "Show room/bath filters for Residential or Recreational groups or when selected type belongs to them."
                                            // إظهار حقول الغرف/الحمامات:
                                            $showRooms = in_array($selCategory, [1,3], true)
                                                || ($selTypeId && (in_array($selTypeId, $resiTypeIds, true)
                                                || in_array($selTypeId, $recreTypeIds, true)));

                                            // English: "List available sub-types as radio (single select) while preserving original look."
                                            // قائمة الأنواع كراديو (نفس الشكل):
                                            $types = Type::withCount('properties')
                                                ->orderBy('name','asc')
                                                ->where('id', '>=', 5)
                                                ->get(['id','name']);
                                        @endphp

                                        @foreach($types as $t)
                                            <label class="custom-control custom-checkbox mb-3">
                                                {{-- English: "Use radio for single type selection; keep original markup classes." --}}
                                                <input type="radio" class="custom-control-input" name="type"
                                                       value="{{ $t->id }}" {{ $selTypeId === (int)$t->id ? 'checked' : '' }}>
                                                <span class="custom-control-label">
                                                    <span class="text-dark">
                                                        {{ $t->name }}
                                                        <span class="label label-secondary float-end">{{ $t->properties_count }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach

                                        {{-- خيار "الكل" --}}
                                        <label class="custom-control custom-checkbox mb-0">
                                            <input type="radio" class="custom-control-input" name="type"
                                                   value="" {{ $selTypeId ? '' : 'checked' }}>
                                            <span class="custom-control-label">
                                                <span class="text-dark">الكل</span>
                                            </span>
                                        </label>

                                    </div>
                                </div>
                            </div>

                            {{-- ===== نطاق السعر (حقلا عدد فقط) ===== --}}
                            <div class="card-header border-top">
                                <h3 class="card-title"> نطاق السعر </h3>
                            </div>
                            <div class="card-body">
                                {{-- English: "Numeric min/max inputs; submitted with the same filter form." --}}
                                <h6><label> نطاق السعر : </label></h6>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="number" class="form-control" name="price_min"
                                               value="{{ request('price_min') }}" placeholder="الحد الأدنى">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="price_max"
                                               value="{{ request('price_max') }}" placeholder="الحد الأعلى">
                                    </div>
                                </div>
                            </div>

                            {{-- ===== الحالة (للبيع/للإيجار) ===== --}}
                            <div class="card-body">
                                <div class="filter-product-checkboxs">
                                    @php $purposes = (array)request('purpose_in',[]); @endphp
                                    {{-- English: "Keep the same checkbox names to align with controller parsing." --}}
                                    <label class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" name="purpose_in[]"
                                               value="rent" {{ in_array('rent',$purposes) ? 'checked' : '' }}>
                                        <span class="custom-control-label">للإيجار</span>
                                    </label>
                                    <label class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" name="purpose_in[]"
                                               value="sale" {{ in_array('sale',$purposes) ? 'checked' : '' }}>
                                        <span class="custom-control-label">للبيع</span>
                                    </label>
                                </div>
                            </div>

                            {{-- ===== الخصائص (غرف/حمامات) — تُعرض للسكني/الترفيهي فقط ===== --}}
                            @if($showRooms)
                                <div class="card-header border-top">
                                    <h3 class="card-title">الخصائص</h3>
                                </div>
                                <div class="card-body">
                                    {{-- English: "Room/bath filters are optional; bedroom is applied in controller. Bathroom kept for UI consistency." --}}
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="number" class="form-control" name="bedroom"
                                                   value="{{ request('bedroom') }}" placeholder="غرف">
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control" name="bathroom"
                                                   value="{{ request('bathroom') }}" placeholder="حمامات">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="card-footer">
                                {{-- English: "Primary submit button for the filter form." --}}
                                <button type="submit" class="btn btn-primary btn-block">تطبيق الفلتر</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--/Left Side Content-->

                {{-- ─────────────────────────── العمود الأيمن: قائمة النتائج ───────────────────────────
                    يحتوي على:
                    - شريط معلومات وعدد النتائج + تبديل العرض (List/Grid)
                    - قائمة العناصر بنمطين (تبويبات): قائمة / شبكة
                    - ترقيم الصفحات (pagination)
                ─────────────────────────────────────────────────────────────────────────────── --}}
                <div class="col-xl-9 col-lg-8 col-md-12">
                    <!--Add lists-->
                    <div class=" mb-lg-0">
                        <div class="">
                            <div class="item2-gl ">

                                {{-- شريط العنوان/الفرز/التبديل --}}
                                <div class=" mb-0">
                                    <div class="">
                                        <div class="p-5 bg-white item2-gl-nav d-flex border br-5">
                                            {{-- English: "Current page count vs total results." --}}
                                            <h6 class="mb-0 mt-2">
                                                عرض {{ $properties->count() }} من {{ $properties->total() }}
                                            </h6>

                                            {{-- English: "Tabs for List/Grid view; classes and anchors kept." --}}
                                            <ul class="nav item2-gl-menu ms-auto mt-2">
                                                <li class="">
                                                    <a href="#tab-11" class="" data-bs-toggle="tab"
                                                       title="List style"><i class="fa fa-list"></i></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-12" data-bs-toggle="tab" class="active show"
                                                       title="Grid"><i class="fa fa-th"></i></a>
                                                </li>
                                            </ul>

                                            {{-- English: "Sort dropdown; preserves existing query string." --}}
                                            <div class="d-flex">
                                                <label class="me-2 mt-1 mb-sm-1 pt-2">ترتيب حسب : </label>
                                                <form method="get" action="{{ url()->current() }}" class="d-flex">
                                                    @foreach(request()->except('sort') as $k=>$v)
                                                        @if(is_array($v))
                                                            @foreach($v as $vv)
                                                                <input type="hidden" name="{{ $k }}[]"
                                                                       value="{{ $vv }}">
                                                            @endforeach
                                                        @else
                                                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                                        @endif
                                                    @endforeach

                                                    <select name="sort" class="form-control select-sm w-75 select2"
                                                            onchange="this.form.submit()">
                                                        <option
                                                            value="newest" {{ request('sort','newest')==='newest' ? 'selected' : '' }}>
                                                            الأحدث
                                                        </option>
                                                        <option
                                                            value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>
                                                            الأقدم
                                                        </option>
                                                        <option
                                                            value="price_asc" {{ request('sort')==='price_asc' ? 'selected' : '' }}>
                                                            السعر : من الأدنى للأعلى
                                                        </option>
                                                        <option
                                                            value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>
                                                            السعر : من الأعلى للأدنى
                                                        </option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ───────── تبويبات العرض (List / Grid) ───────── --}}
                                <div class="tab-content">

                                    {{-- تبويب القائمة (List) --}}
                                    <div class="tab-pane" id="tab-11">
                                        @forelse($properties as $p)
                                            @php
                                                // English: "Resolve type metadata to control which attributes to show."
                                                $type     = optional($p->type);
                                                $parentId = (int) ($type->parent_id ?? 0);

                                                $isResi   = ($parentId === 1); // سكني
                                                $isRecre  = ($parentId === 3); // ترفيهي
                                                $typeName = trim((string) ($type->name ?? ''));

                                                // English: "Rooms displayed for Residential and Recreational only."
                                                $showRooms = $isResi || $isRecre;

                                                // English: "Garage shown only for specific sub-types."
                                                $garageAllowed = ['فيلا','مكتب','محل','محل تجاري','دكان'];
                                                $showGarage = in_array($typeName,$garageAllowed,true);
                                            @endphp

                                            <div class="card overflow-hidden">
                                                <div class="d-md-flex">

                                                    {{-- صورة وبادجات السعر/الغرض --}}
                                                    <div class="item-card9-img">
                                                        <div
                                                            class="arrow-ribbon {{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }}">
                                                            {{ number_format($p->price) }}
                                                        </div>
                                                        <div class="item-card9-imgs">
                                                            <a href="{{ route('property_detail',$p->slug) }}"></a>
                                                            <img src="{{ asset('uploads/'.$p->featured_photo) }}"
                                                                 alt="{{ $p->name }}" class="cover-image">
                                                        </div>
                                                        <div class="item-tags">
                                                            <div
                                                                class="{{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }} tag-option">
                                                                {{ in_array($p->purpose,['rent','إيجار']) ? 'للإيجار' : 'للبيع' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- معلومات العنصر (المساحة/الغرف/الحمامات/المواقف) --}}
                                                    <div class="card border-0 mb-0">
                                                        <div class="card-body ">
                                                            <div class="item-card9">
                                                                <a href="{{ route('property_detail',$p->slug) }}"
                                                                   class="text-dark">
                                                                    <h4 class="font-weight-bold mt-1">{{ $p->name }}</h4>
                                                                </a>
                                                                <ul class="item-card2-list">
                                                                    @if($p->size)
                                                                        <li>
                                                                            <i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }}
                                                                            متر²
                                                                        </li>
                                                                    @endif
                                                                    @if($showRooms && $p->bedroom)
                                                                        <li>
                                                                            <i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }}
                                                                            غرف
                                                                        </li>
                                                                    @endif
                                                                    @if($showRooms && $p->bathroom)
                                                                        <li>
                                                                            <i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }}
                                                                            حمام
                                                                        </li>
                                                                    @endif
                                                                    @if($showGarage && $p->garage)
                                                                        <li>
                                                                            <i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} {{ $p->garage==1?'موقف':'مواقف' }}
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        {{-- أسفل الكرت: العنوان + تاريخ الإضافة --}}
                                                        <div class="card-footer pt-4 pb-4">
                                                            <div class="item-card9-footer d-flex">
                                                                <div class="item-card9-cost">
                                                                    <span>
                                                                        <i class="fa fa-map-marker text-muted me-1"></i> {{ $p->address }}
                                                                    </span>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <span>
                                                                        <i class="fa fa-calendar-o text-muted me-1"></i> {{ optional($p->created_at)->diffForHumans() }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        @empty
                                            {{-- رسالة لا نتائج --}}
                                            <div class="card">
                                                <div class="card-body text-center py-6">لا توجد نتائج مطابقة لبحثك.</div>
                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- تبويب الشبكة (Grid) --}}
                                    <div class="tab-pane active" id="tab-12">
                                        <div class="row">
                                            @forelse ($properties as $p)
                                                @php
                                                    // English: "Same logic as List view for flags and visibility."
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

                                                        {{-- صورة وبادجات السعر + أزرار الأمنية (Wishlist) --}}
                                                        <div class="item-card9-img">
                                                            <div
                                                                class="arrow-ribbon {{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }}">{{ number_format($p->price) }}</div>
                                                            <div class="item-card9-imgs">
                                                                <a href="{{ route('property_detail',$p->slug) }}"></a>
                                                                <img src="{{ asset('uploads/'.$p->featured_photo) }}"
                                                                     alt="{{ $p->name }}" class="cover-image">
                                                            </div>
                                                            <div class="item-card2-icons ">
                                                                {{-- English: "Wishlist button include; expects 'wishlisted' flag if provided by query scope." --}}
                                                                @include('components.wish.btn', [
                                                                    'propertyId' => $p->id,
                                                                    'wished'     => (bool)($p->wishlisted ?? 0)
                                                                ])
                                                            </div>
                                                            <div class="item-tags">
                                                                <div
                                                                    class="{{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }} tag-option">
                                                                    {{ in_array($p->purpose,['rent','إيجار']) ? 'للإيجار' : 'للبيع' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- تفاصيل مختصرة (Grid) --}}
                                                        <div class="card-body">
                                                            <div class="item-card9">
                                                                <a href="{{ route('property_detail',$p->slug) }}"
                                                                   class="text-dark">
                                                                    <h4 class="font-weight-bold mt-1">{{ $p->name }}</h4>
                                                                </a>
                                                                <ul class="item-card2-list">
                                                                    @if($p->size)
                                                                        <li>
                                                                            <i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }}
                                                                            متر²
                                                                        </li>
                                                                    @endif
                                                                    @if($showRooms && $p->bedroom)
                                                                        <li>
                                                                            <i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }}
                                                                            غرف
                                                                        </li>
                                                                    @endif
                                                                    @if($showRooms && $p->bathroom)
                                                                        <li>
                                                                            <i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }}
                                                                            حمام
                                                                        </li>
                                                                    @endif
                                                                    @if($showGarage && $p->garage)
                                                                        <li>
                                                                            <i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} {{ $p->garage==1?'موقف':'مواقف' }}
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        {{-- أسفل الكرت: العنوان + تاريخ الإضافة --}}
                                                        <div class="card-footer">
                                                            <div class="item-card9-footer d-flex">
                                                                <div class="item-card9-cost">
                                                                    <span>
                                                                        <i class="fa fa-map-marker text-muted me-1"></i> {{ $p->address }}
                                                                    </span>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <span>
                                                                        <i class="fa fa-calendar-o text-muted me-1"></i> {{ optional($p->created_at)->diffForHumans() }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            @empty
                                                {{-- رسالة لا نتائج (Grid) --}}
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body text-center py-6">
                                                            لا توجد نتائج مطابقة لبحثك.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ترقيم الصفحات (Pagination) --}}
                            <div class="center-block text-center">{{ $properties->onEachSide(1)->links() }}</div>
                        </div>
                    </div>
                    <!--/Add lists-->
                </div>

            </div>
        </div>
    </section>
    <!--/Add Listings-->
@endsection
