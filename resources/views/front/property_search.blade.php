@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
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

    <!--Add listing-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <!--Left Side Content-->
                <div class="col-xl-3 col-lg-4 col-md-12">
                    {{-- كرت البحث العلوي كما هو --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control br-tl-3  br-bl-3" name="name" form="filterForm" value="{{ request('name') }}" placeholder="بحث">
                                <button type="submit" form="filterForm" class="btn btn-primary br-tr-3  br-br-3">بحث</button>
                            </div>
                        </div>
                    </div>

                    {{-- كرت الفلاتر --}}
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

                                            // النوع المختار حالياً
                                            $selType = request('type');

                                            // جلب كل الأنواع الفرعية مع عدد العقارات
                                            // يمكنك تقييدها بأنواع محددة لو لزم
                                            $types = Type::withCount('properties')->orderBy('name','asc')->where('id', '>=', 5)->get(['id','name']);

                                            // مجموعات أنواع لإظهار حقول الغرف/الحمامات
                                            // السكني parent_id=1 ، الترفيهي parent_id=3 (عدّل الأرقام إذا مختلفة لديك)
                                            $resiTypeIds  = Type::where('parent_id', 1)->pluck('id')->toArray();
                                            $recreTypeIds = Type::where('parent_id', 3)->pluck('id')->toArray();

                                            $showRooms = $selType && (in_array((int)$selType, $resiTypeIds, true) || in_array((int)$selType, $recreTypeIds, true));
                                        @endphp

                                        @foreach($types as $t)
                                            <label class="custom-control custom-checkbox mb-3">
                                                {{-- نحافظ على الشكل، ونستعمل راديو لاختيار نوع واحد (الشكل يبقى كما هو) --}}
                                                <input type="radio" class="custom-control-input" name="type" value="{{ $t->id }}" {{ (string)$selType === (string)$t->id ? 'checked' : '' }}>
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
                                            <input type="radio" class="custom-control-input" name="type" value="" {{ $selType ? '' : 'checked' }}>
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
                                <h6><label> نطاق السعر : </label></h6>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="number" class="form-control" name="price_min" value="{{ request('price_min') }}" placeholder="الحد الأدنى">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="price_max" value="{{ request('price_max') }}" placeholder="الحد الأعلى">
                                    </div>
                                </div>
                            </div>

                            {{-- ===== الحالة ===== --}}
                            <div class="card-body">
                                <div class="filter-product-checkboxs">
                                    @php $purposes = (array)request('purpose_in',[]); @endphp
                                    <label class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" name="purpose_in[]" value="rent" {{ in_array('rent',$purposes) ? 'checked' : '' }}>
                                        <span class="custom-control-label">للإيجار</span>
                                    </label>
                                    <label class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" name="purpose_in[]" value="sale" {{ in_array('sale',$purposes) ? 'checked' : '' }}>
                                        <span class="custom-control-label">للبيع</span>
                                    </label>

                                </div>
                            </div>

                            {{-- ===== الغرف/الحمامات (تظهر فقط للسكني أو الترفيهي) ===== --}}
                            @if($showRooms)
                                <div class="card-header border-top">
                                    <h3 class="card-title">الخصائص</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="number" class="form-control" name="bedroom" value="{{ request('bedroom') }}" placeholder="غرف">
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control" name="bathroom" value="{{ request('bathroom') }}" placeholder="حمامات">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">تطبيق الفلتر</button>
                            </div>
                        </div>
                    </form>


                </div>

                <!--/Left Side Content-->

                <div class="col-xl-9 col-lg-8 col-md-12">
                    <!--Add lists-->
                    <div class=" mb-lg-0">
                        <div class="">
                            <div class="item2-gl ">
                                <div class=" mb-0">
                                    <div class="">
                                        <div class="p-5 bg-white item2-gl-nav d-flex border br-5">
                                            <h6 class="mb-0 mt-2">
                                                عرض {{ $properties->count() }} من {{ $properties->total() }}
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

                                                // المواقف فقط للفيلا + المكتب + المحل/الدكان
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
                                                                <a href="{{ route('property_detail',$p->slug) }}" class="text-dark"><h4 class="font-weight-bold mt-1">{{ $p->name }}</h4></a>
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
                                                                <div class="item-card9-cost"><span><i class="fa fa-map-marker text-muted me-1"></i> {{ $p->address }}</span></div>
                                                                <div class="ms-auto"><span><i class="fa fa-calendar-o text-muted me-1"></i> {{ optional($p->created_at)->diffForHumans() }}</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="card"><div class="card-body text-center py-6">لا توجد نتائج مطابقة لبحثك.</div></div>
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
                                                            <div class="item-tags">
                                                                <div class="{{ in_array($p->purpose,['rent','إيجار']) ? 'bg-info':'bg-success' }} tag-option">
                                                                    {{ in_array($p->purpose,['rent','إيجار']) ? 'للإيجار' : 'للبيع' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="item-card9">
                                                                <a href="{{ route('property_detail',$p->slug) }}" class="text-dark"><h4 class="font-weight-bold mt-1">{{ $p->name }}</h4></a>
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
                                                    <div class="card"><div class="card-body text-center py-6">لا توجد نتائج مطابقة لبحثك.</div></div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
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
