@extends('front.layouts.master')

@section('main_content')

    <!--Sliders Section-->
    <section>
        <div class="banner-1 cover-image sptb-2 sptb-tab bg-background2" data-bs-image-src="{{asset('uploads/'.$global_setting->banner)}}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="mb-1">اعثر على منزل أحلامك</h1>
                        <p class="distracted">هناك حقيقة مثبتة منذ زمن طويل أن المحتوى المقروء سيلهي القارئ عن التركيز.</p>
                    </div>
                    <div class="row">
                        <div class="col-xl-10 col-lg-12 col-md-12 d-block mx-auto">
                            <form action="{{ route('property_search') }}" method="GET" id="searchForm">
                                <div class="item-search-tabs">
                                    <div class="item-search-menu">
                                        <ul class="nav">
                                            <li class=""><a href="#" class="active prim" data-bs-toggle="tab"
                                                            onclick="document.getElementById('purposeInput').value='sale'">للبيع</a></li>
                                            <li><a href="#" data-bs-toggle="tab"
                                                   onclick="document.getElementById('purposeInput').value='rent'">للإيجار</a></li>
                                        </ul>
                                    </div>

                                    <div class="tab-content">
                                        <div class="item-search-menu">
                                            <ul class="nav  justify-content-center mb-3">
                                                <li class=""><a href="#tab1" class="active" data-bs-toggle="tab"
                                                                onclick="document.getElementById('categoryInput').value=1">سكني</a></li>
                                                <li><a href="#tab2" data-bs-toggle="tab"
                                                       onclick="document.getElementById('categoryInput').value=2">تجاري وخدمي</a></li>
                                                <li><a href="#tab3" data-bs-toggle="tab"
                                                       onclick="document.getElementById('categoryInput').value=3">ترفيهي</a></li>
                                                <li><a href="#tab4" data-bs-toggle="tab"
                                                       onclick="document.getElementById('categoryInput').value=4">أراضي</a></li>
                                            </ul>
                                        </div>

                                        <!-- سكني -->
                                        <div class="tab-pane active" id="tab1">
                                            <div class="search-background bg-transparent">
                                                <div class="form row no-gutters ">
                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-tr-md-0 br-br-md-0" id="text20"
                                                               name="name" placeholder="اعثر على عقار">
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <!-- النوع (IDs من قاعدة البيانات) -->
                                                        <select id="select-Categories11" class="form-control form-select br-md-0 select2" name="type">
                                                            <option value="">النوع</option>
                                                            <option value="5">شقة</option>
                                                            <option value="6">بيت عربي</option>
                                                            <option value="7">فيلا</option>
                                                            <option value="8">ملحق</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <!-- المساحة كنطاق يُفك بالكونترولر -->
                                                        <select id="select-Categories12" class="form-control form-select br-md-0 select2" name="area_range">
                                                            <option value="">المساحة</option>
                                                            <option value="0-100">أقل من 100 متر مربع</option>
                                                            <option value="100-200">بين 100 و 200 متر مربع</option>
                                                            <option value="200-">أكثر من 200 متر مربع</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-md-0" id="text21"
                                                               name="city_text" placeholder="المدينة">
                                                        <span><i class="fa fa-map-marker location-gps me-1"></i> </span>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <!-- المحافظة كنصّ (نفلتر على address LIKE) -->
                                                        <select id="select-Categories13" class="form-control form-select br-md-0 select2" name="province_text">
                                                            <option value="">المحافظة</option>
                                                            <option>ادلب</option>
                                                            <option>دمشق</option>
                                                            <option>ريف دمشق</option>
                                                            <option>درعا</option>
                                                            <option>حلب</option>
                                                            <option>حمص</option>
                                                            <option>حماة</option>
                                                            <option>اللاذقية</option>
                                                            <option>طرطوس</option>
                                                            <option>دير الزور</option>
                                                            <option>الرقة</option>
                                                            <option>الحسكة</option>
                                                            <option>القنيطرة</option>
                                                            <option>السويداء</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <a href="#" class="br-tl-md-0 br-bl-md-0 btn btn-lg btn-block btn-primary"
                                                           onclick="document.getElementById('searchForm').submit()">بحث</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- تجاري وخدمي -->
                                        <div class="tab-pane" id="tab2">
                                            <div class="search-background bg-transparent">
                                                <div class="form row no-gutters ">
                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-tr-md-0 br-br-md-0" placeholder="اعثر على عقار" name="name">
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories15" class="form-control form-select br-md-0 select2" name="type">
                                                            <option value="">النوع</option>
                                                            <option value="9">مكتب</option>
                                                            <option value="10">محل</option>
                                                            <option value="11">مستودع</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories14" class="form-control form-select br-md-0 select2" name="area_range">
                                                            <option value="">المساحة</option>
                                                            <option value="0-50">اقل من 50 متر مربع</option>
                                                            <option value="50-100">بين 50 و100 متر مربع</option>
                                                            <option value="100-200">بين 100 و200 متر مربع</option>
                                                            <option value="200-">أكثر من 200 متر مربع</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-md-0" placeholder="المدينة" name="city_text">
                                                        <span><i class="fa fa-map-marker location-gps me-1"></i> </span>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories16" class="form-control form-select br-md-0 select2" name="province_text">
                                                            <option value="">المحافظة</option>
                                                            <!-- نفس القائمة السابقة -->
                                                            <option>ادلب</option><option>دمشق</option><option>ريف دمشق</option>
                                                            <option>درعا</option><option>حلب</option><option>حمص</option><option>حماة</option>
                                                            <option>اللاذقية</option><option>طرطوس</option><option>دير الزور</option>
                                                            <option>الرقة</option><option>الحسكة</option><option>القنيطرة</option><option>السويداء</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <a href="#" class="br-tl-md-0 br-bl-md-0 btn btn-lg btn-block btn-primary"
                                                           onclick="document.getElementById('searchForm').submit()">بحث</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ترفيهي -->
                                        <div class="tab-pane" id="tab3">
                                            <div class="search-background bg-transparent">
                                                <div class="form row no-gutters ">
                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-tr-md-0 br-br-md-0" placeholder="اعثر على عقار" name="name">
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories17" class="form-control form-select br-md-0 select2" name="type">
                                                            <option value="">النوع</option>
                                                            <option value="12">شاليه</option>
                                                            <option value="13">مزرعة/مسبح</option> <!-- إن كانت عندك «مزرعة» فقط فاختَر 13 -->
                                                            <option value="14">استراحة/مسبح</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <!-- السعر كنطاق -->
                                                        <select id="select-Categories18" class="form-control form-select br-md-0 select2" name="price_range">
                                                            <option value="">السعر</option>
                                                            <option value="0-100">تحت 100 دولار</option>
                                                            <option value="100-">فوق 100 دولار</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-md-0" placeholder="المدينة" name="city_text">
                                                        <span><i class="fa fa-map-marker location-gps me-1"></i> </span>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories19" class="form-control form-select br-md-0 select2" name="province_text">
                                                            <option value="">المحافظة</option>
                                                            <!-- نفس القائمة -->
                                                            <option>ادلب</option><option>دمشق</option><option>ريف دمشق</option>
                                                            <option>درعا</option><option>حلب</option><option>حمص</option><option>حماة</option>
                                                            <option>اللاذقية</option><option>طرطوس</option><option>دير الزور</option>
                                                            <option>الرقة</option><option>الحسكة</option><option>القنيطرة</option><option>السويداء</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <a href="#" class="br-tl-md-0 br-bl-md-0 btn btn-lg btn-block btn-primary"
                                                           onclick="document.getElementById('searchForm').submit()">بحث</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- أراضي -->
                                        <div class="tab-pane" id="tab4">
                                            <div class="search-background bg-transparent">
                                                <div class="form row no-gutters ">
                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-tr-md-0 br-br-md-0" placeholder="اعثر على ارض" name="name">
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories21" class="form-control form-select br-md-0 select2" name="type">
                                                            <option value="">النوع</option>
                                                            <option value="15">زراعية</option>
                                                            <option value="16">للبناء</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories20" class="form-control form-select br-md-0 select2" name="area_range">
                                                            <option value="">المساحة</option>
                                                            <option value="0-200">أقل من 200 متر مربع</option>
                                                            <option value="200-500">بين 200 ل 500 متر مربع</option>
                                                            <option value="500-1000">بين 500 ل 1000 متر مربع</option>
                                                            <option value="1000-">أكثر من 1000 متر مربع</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group  col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <input type="text" class="form-control input-lg br-md-0" placeholder="المدينة" name="city_text">
                                                        <span><i class="fa fa-map-marker location-gps me-1"></i> </span>
                                                    </div>

                                                    <div class="form-group col-xl-2 col-lg-2 col-md-12 select2-lg mb-0">
                                                        <select id="select-Categories22" class="form-control form-select br-md-0 select2" name="province_text">
                                                            <option value="">المحافظة</option>
                                                            <!-- نفس القائمة -->
                                                            <option>ادلب</option> <option>دمشق</option> <option>ريف دمشق</option>
                                                            <option>درعا</option> <option>حلب</option> <option>حمص</option> <option>حماة</option>
                                                            <option>اللاذقية</option> <option>طرطوس</option> <option>دير الزور</option>
                                                            <option>الرقة</option> <option>الحسكة</option> <option>القنيطرة</option> <option>السويداء</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-2 col-lg-2 col-md-12 mb-0">
                                                        <a href="#" class="br-tl-md-0 br-bl-md-0 btn btn-lg btn-block btn-primary"
                                                           onclick="document.getElementById('searchForm').submit()">بحث</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /tab-content -->
                                </div><!-- /item-search-tabs -->

                                <!-- حقول مخفية مشتركة  -->
                                <input type="hidden" name="purpose" id="purposeInput" value="sale">
                                <input type="hidden" name="category_id" id="categoryInput" value="1">
                            </form>

                        </div>
                    </div>
                </div>
            </div><!-- /header-text -->
        </div>
    </section>
    <!--Sliders Section-->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const enableOnlyActiveTab = () => {
                document.querySelectorAll('.tab-pane input, .tab-pane select').forEach(el => el.disabled = true);
                const active = document.querySelector('.tab-pane.active.show') || document.querySelector('.tab-pane.active');
                if (active) active.querySelectorAll('input, select').forEach(el => el.disabled = false);
            };
            enableOnlyActiveTab();
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(link => {
                link.addEventListener('shown.bs.tab', enableOnlyActiveTab);
            });
        });
    </script>


    <!--Categories-->
    <section class="categories">
        <div class="container">
            <div class="card mb-0 box-shadow-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 d-catmb mb-4 mb-lg-0">
                            <div class="d-flex">
                                <div>
            <span class="bg-primary-transparent icon-service1 text-primary">
              <i class="fa fa-map-o"></i>
            </span>
                                </div>
                                <div class="ms-4 mt-1">
                                    <h3 class="mb-0 font-weight-bold">{{ number_format($counts['lands'] ?? 0) }}</h3>
                                    <p class="mb-0 text-muted">أراضي</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 d-catmb mb-4 mb-lg-0">
                            <div class="d-flex">
                                <div>
            <span class="bg-secondary-transparent icon-service1 text-secondary">
              <i class="fa fa-home"></i>
            </span>
                                </div>
                                <div class="ms-4 mt-1">
                                    <h3 class="mb-0 font-weight-bold">{{ number_format($counts['recre'] ?? 0) }}</h3>
                                    <p class="mb-0 text-muted">مزارع وشاليهات</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 d-catmb mb-4 mb-sm-0">
                            <div class="d-flex">
                                <div>
            <span class="bg-warning-transparent icon-service1 text-warning">
              <i class="fa fa-object-group"></i>
            </span>
                                </div>
                                <div class="ms-4 mt-1">
                                    <h3 class="mb-0 font-weight-bold">{{ number_format($counts['commercial'] ?? 0) }}</h3>
                                    <p class="mb-0 text-muted">متاجر ومكاتب</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="d-flex">
                                <div>
            <span class="bg-info-transparent icon-service1 text-info">
              <i class="fa fa-building-o"></i>
            </span>
                                </div>
                                <div class="ms-4 mt-1">
                                    <h3 class="mb-0 font-weight-bold">{{ number_format($counts['residential'] ?? 0) }}</h3>
                                    <p class="mb-0 text-muted">سكني</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!--Agents-->
        <section class="sptb">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>الوكلاء</h2>
                    <p>تعرف على وكلائنا المميزين</p>
                </div>

                <div id="small-categories" class="owl-carousel owl-carousel-icons2">
                    @foreach($agents as $item)
                        <div class="item">
                            <div class="card mb-0">
                                <div class="item-card">
                                    <div class="item-card-desc">
                                        <a href="{{ route('agent',$item->id) }}"></a>

                                        <div class="item-card-img">
                                            <img
                                                src="{{ $item->photo ? asset('uploads/'.$item->photo) : asset('uploads/default.png') }}"
                                                alt="{{ $item->name }}"
                                                class="br-tr-7 br-tl-7">
                                        </div>
                                        <div class="item-card-text">
                                            <h4 class="mb-0">
                                                <a href="{{ route('agent',$item->id) }}" class="text-white" > {{ $item->name }}</a>
                                            </h4>
                                            <span class="badge rounded-pill badge-primary w-30">
                                                {{ $item->properties_count ?? 0 }}عقار
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('agents') }}" class="btn btn-secondary btn-lg">عرض كل الوكلاء</a>
            </div>
        </section>
        <!--/Agents-->


      {{--
      <!--Locations-->
        <section class="sptb">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>المحافظات</h2>
                    <p>اختر المحافظة التي تناسبك</p>
                </div>

                <div id="small-categories" class="owl-carousel owl-carousel-icons2">
                    @forelse($locations as $loc)
                        <div class="item">
                            <div class="card mb-0">
                                <div class="item-card">
                                    <div class="item-card-desc">
                                        <a href="{{ route('location', $loc->slug) }}"></a>

                                        <div class="item-card-img">
                                            <img
                                                src="{{ $loc->photo ? asset('uploads/'.$loc->photo) : asset('uploads/default-location.jpg') }}"
                                                alt="{{ $loc->name }}"
                                                class="br-tr-7 br-tl-7"
                                                >
                                        </div>

                                        <div class="item-card-text">
                                            <h4 class="mb-0">{{ $loc->name }}</h4>
                                            <span class="badge rounded-pill badge-primary w-30">
                    {{ number_format($loc->properties_count ?? 0) }} عقار
                  </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center w-100 py-5">لا توجد محافظات لعرضها حالياً.</div>
                    @endforelse
                </div>
            </div>
        </section>
        <!--/Locations-->

                            --}}

        <!--Categories-->
        <section class="sptb">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>الفئات</h2>
                    <p>سدّ الفجوة بين العرض والطلب مع أفضل العروض العقارية</p>
                </div>

                <div id="small-categories" class="owl-carousel owl-carousel-icons2">
                    @forelse($subtypes as $type)
                        <div class="item">
                            <div class="card mb-0">
                                <div class="item-card">
                                    <div class="item-card-desc">
                                        {{-- رابط إلى بحث العقارات بالـ type --}}
                                        <a href="{{ route('property_search', ['type' => $type->id]) }}"></a>

                                        <div class="item-card-img">
                                            <img
                                                src="{{ $type->image ? asset('uploads/'.$type->image) : asset('uploads/default-type.jpg') }}"
                                                alt="{{ $type->name }}"
                                                class="br-tr-7 br-tl-7">

                                        </div>

                                        <div class="item-card-text">
                                            <h4 class="mb-0">{{ $type->name }}</h4>
                                            <span class="badge rounded-pill badge-primary w-30">
                                                {{ number_format($type->properties_count ?? 0) }} عقار
                                            </span>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center w-100 py-5">لا توجد فئات متاحة حالياً.</div>
                    @endforelse
                </div>
            </div>
        </section>
        <!--/Categories-->
        <!--Statistics-->
        <section>
            <div class="about-1 cover-image sptb bg-background2" data-bs-image-src="../assets/images/banners/banner5.jpg">
                <div class="content-text mb-0 text-white info">
                    <div class="container">
                        <div class="row text-center">

                            {{-- 1) إجمالي الوكلاء --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="counter-status md-mb-0">
                                    <div class="counter-icon">
                                        <i class="icon icon-people"></i>
                                    </div>
                                    <h5>إجمالي الوكلاء</h5>
                                    <h2 class="counter mb-0 font-weight-bold">{{ number_format($agents_total) }}</h2>
                                </div>
                            </div>

                            {{-- 2) إجمالي الطلبات --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="counter-status status-1 md-mb-0">
                                    <div class="counter-icon text-warning">
                                        <i class="icon icon-rocket"></i>
                                    </div>
                                    <h5>إجمالي الطلبات</h5>
                                    <h2 class="counter mb-0 font-weight-bold">{{ number_format($orders_total) }}</h2>
                                </div>
                            </div>

                            {{-- 3) إجمالي العقارات --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="counter-status status md-mb-0">
                                    <div class="counter-icon text-primary">
                                        <i class="icon icon-docs"></i>
                                    </div>
                                    <h5>إجمالي العقارات</h5>
                                    <h2 class="counter mb-0 font-weight-bold">{{ number_format($properties_total) }}</h2>
                                </div>
                            </div>

                            {{-- 4) إجمالي المستخدمين --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="counter-status status">
                                    <div class="counter-icon text-success">
                                        <i class="icon icon-emotsmile"></i>
                                    </div>
                                    <h5>إجمالي المستخدمين</h5>
                                    <h2 class="counter font-weight-bold mb-0">{{ number_format($users_total) }}</h2>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--/Statistics-->
        <!--Latest Ads-->
        <section class="sptb bg-white">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>أحدث العقارات</h2>
                    <p>سدّ الفجوة بين العرض والطلب مع أفضل العروض العقارية</p>
                </div>

                <div id="myCarousel1" class="owl-carousel owl-carousel-icons2">
                    @foreach($latest_properties as $p)
                        @php

                                // تحديد فئة المستوى الأعلى بالأرقام (حسب جدول types)
                                $topParentId = $p->type->parent_id; // 1 سكني، 2 تجاري/خدمي، 3 ترفيهي، 4 أراضي
                                $isFeatured  = ($p->is_featured === 'Yes');
                                $isNew       = $p->created_at && $p->created_at->gt(now()->subDays(14));

                                // شارة "للبيع/للإيجار"
                                $purposeText = $p->purpose === 'rent' ? 'للإيجار' : 'للبيع';
                                // شارة ثانوية
                                $secondTag   = $isNew ? 'جديد' : ($isFeatured ? 'مميز' : null);

                                // صورة الغلاف
                                $img = $p->featured_photo ? asset('uploads/'.$p->featured_photo) : asset('uploads/default.png');

                                // رابط التفاصيل
                                $detailsUrl = route('property_detail', $p->slug);

                                // رابط المفضلة حسب حالة المستخدم
                                if(Auth::guard('web')->check()){
                                    $wishUrl = route('wishlist_add', $p->id);
                                } else {
                                    $wishUrl = route('login');
                                }

                                // تنسيق السعر (وإضافة "شهرياً" عند الإيجار)
                                $price = number_format((float)$p->price, 0, '.', ',');
                                $priceSuffix = $p->purpose === 'Rent' ? 'شهرياً' : '';

                                // نص الموقع (سوريا + المحافظة + من العنوان إن وجد)
                                $locName = optional($p->location)->name;
                                $addressShort = $p->address ? '، '.$p->address : '';
                                $locationText = 'سوريا'.($locName ? '، '.$locName : '').$addressShort;
                        @endphp

                        <div class="item @if($p->status === 'Sold') sold-out @endif">
                            @if($p->status === 'Sold')
                                <div class="ribbon sold-ribbon ribbon-top-left text-danger"><span class="bg-danger">نفد</span></div>
                            @endif

                            <div class="card mb-0">
                                @if($isFeatured)
                                    <div class="power-ribbon power-ribbon-top-left text-warning">
                                        <span class="bg-warning"><i class="fa fa-bolt"></i></span>
                                    </div>
                                @endif

                                <div class="item-card2-img">
                                    <a href="{{ $detailsUrl }}"></a>
                                    <img src="{{ $img }}" alt="صورة" class="cover-image">
                                    <div class="tag-text">
                                        <span class="bg-danger tag-option">{{ $purposeText }}</span>
                                        @if($secondTag)
                                            <span class="bg-pink tag-option">{{ $secondTag }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="item-card2-icons">
                                    <a href="{{ $detailsUrl }}" class="item-card2-icons-l bg-primary">
                                        <i class="fa fa-home"></i>
                                    </a>
                                    @include('components.wish.btn', [
                                        'propertyId' => $p->id,
                                        'wished'     => (bool)($p->wishlisted ?? 0) // أو استخدم دالتك/كاشك إن متوفر
                                            ])

                                </div>

                                <div class="card-body">
                                    <div class="item-card2">
                                        <div class="item-card2-text">
                                            <a href="{{ $detailsUrl }}" class="text-dark">
                                                <h4 class="">{{ $p->type->name }}</h4>
                                            </a>
                                            <p class="mb-2">
                                                <i class="fa fa-map-marker text-danger me-1"></i>
                                                {{ $locationText }}
                                            </p>
                                            <h5 class="font-weight-bold mb-3">
                                                ${{ $price }}
                                                @if($priceSuffix)
                                                    <span class="fs-12 font-weight-normal">{{ $priceSuffix }}</span>
                                                @endif
                                            </h5>
                                        </div>

                                        {{-- تفاصيل حسب الفئة العليا --}}
                                        @if($topParentId == 1) {{-- سكني --}}
                                        <ul class="item-card2-list">
                                            @if($p->size)<li><a href="javascript:void(0);"><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</a></li>@endif
                                            @if($p->bedroom)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }} غرف</a></li>@endif
                                            @if($p->bathroom)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }} حمام</a></li>@endif
                                            @if($p->garage)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} موقف</a></li>@endif
                                        </ul>
                                        @elseif($topParentId == 2) {{-- تجاري/خدمي --}}
                                        <ul class="item-card2-list">
                                            @if($p->size)<li><a href="javascript:void(0);"><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</a></li>@endif
                                            @if($p->garage)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} مواقف</a></li>@endif
                                        </ul>
                                        @elseif($topParentId == 3) {{-- ترفيهي --}}
                                        <ul class="item-card2-list">
                                            @if($p->size)<li><a href="javascript:void(0);"><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</a></li>@endif
                                            @if($p->bedroom)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-bed text-muted me-1"></i> {{ $p->bedroom }} غرف</a></li>@endif
                                            @if($p->bathroom)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-bath text-muted me-1"></i> {{ $p->bathroom }} حمام</a></li>@endif
                                            @if($p->garage)<li><a href="javascript:void(0);" class="icons"><i class="fa fa-car text-muted me-1"></i> {{ $p->garage }} موقف</a></li>@endif
                                        </ul>
                                        @elseif($topParentId == 4) {{-- أراضي --}}
                                        <ul class="item-card2-list">
                                            @if($p->size)<li><a href="javascript:void(0);"><i class="fa fa-arrows-alt text-muted me-1"></i> {{ $p->size }} متر²</a></li>@endif
                                        </ul>
                                        @endif
                                        {{-- /تفاصيل حسب الفئة --}}
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="footerimg d-flex mt-0 mb-0">
                                        <div class="d-flex footerimg-l mb-0">
                                            <img src="{{ $p->agent && $p->agent->photo ? asset('uploads/'.$p->agent->photo) : asset('uploads/default.png') }}"
                                                 alt="صورة" class="avatar brround me-2">
                                            <h5 class="time-title text-muted p-0 leading-normal my-auto">
                                                {{ optional($p->agent)->name }}
                                                <i class="si si-check text-success fs-12 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="موثّق"></i>
                                            </h5>
                                        </div>
                                        <div class="my-auto footerimg-r ms-auto">
                                            <small class="text-muted">{{ $p->created_at? $p->created_at->diffForHumans() : '' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('property_search') }}" class="btn btn-secondary btn-lg">عرض كل العقارات</a>
                </div>
            </div>
        </section>
        <!--Latest Ads-->

        <!--Featured Ads-->
        <section class="sptb bg-patterns">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>عقارات مميزة</h2>
                    <p>سدّ الفجوة بين العرض والطلب مع أفضل العروض العقارية</p>
                </div>

                <div id="myCarousel2" class="owl-carousel owl-carousel-icons2">
                    @forelse($featured_properties as $p)
                        @php
                            // شارة بيع/إيجار + ألوان الشريط
                            $purposeText = (trim($p->purpose) === 'إيجار' || strtolower((string)$p->purpose) === 'rent') ? 'للإيجار' : 'للبيع';
                            $ribbonClass = ($purposeText === 'للبيع') ? 'bg-primary' : 'bg-purple';

                            // صورة الغلاف
                            $img = $p->featured_photo ? asset('uploads/'.$p->featured_photo) : asset('uploads/default.png');

                            // روابط
                            $detailsUrl = route('property_detail', $p->slug);
                            $wishUrl    = Auth::guard('web')->check() ? route('wishlist_add', $p->id) : route('login');

                            // تنسيق السعر
                            $price = number_format((float)$p->price, 0, '.', ',');
                            $isRent = ($purposeText === 'للإيجار');

                            // تحديد مجموعة النوع (سكني/تجاري/ترفيهي/أراضي)
                            $TYPE_RESIDENTIAL = 1;
                            $TYPE_COMMERCIAL  = 2;
                            $TYPE_RECREATION  = 3;
                            $TYPE_LANDS       = 4;

                            // بعض الأنواع تكون parent مباشرة وبعضها subtype؛ نأخذ parent_id إن وجد وإلا id
                            $groupId = $p->type ? ($p->type->parent_id ?: $p->type->id) : null;

                            // نفس الكلاسات المستعملة في الأمثلة: سكني استخدم item-cards7-ic، تجاري/خدمي استخدم item-card2-list
                            $ulClass = ($groupId === $TYPE_COMMERCIAL) ? 'item-card2-list' : 'item-cards7-ic mb-0';

                            // عنوان وموقع
                            $title = $p->name ?: ($p->type->name ?? 'عقار');
                            $address = $p->address ?: ($p->location->name ?? '');
                        @endphp

                        <div class="item">
                            <div class="card mb-0">
                                <div class="arrow-ribbon {{ $ribbonClass }}">{{ $purposeText }}</div>

                                <!-- الصورة -->
                                <div class="item-card7-imgs">
                                    <a href="{{ $detailsUrl }}"></a>
                                    <img src="{{ $img }}" alt="صورة" class="cover-image">
                                </div>

                                <!-- أيقونة المفضلة -->
                                <div class="item-card2-icons">
                                    <a href="{{ $wishUrl }}" class="item-card2-icons-r bg-secondary">
                                        <i class="fa fa fa-heart-o"></i>
                                    </a>
                                </div>

                                <!-- وسم "مميز" -->
                                <div class="item-card7-overlaytext">
                                    <a href="{{ $detailsUrl }}" class="text-white">مميز</a>
                                </div>

                                <!-- جسم الكرت -->
                                <div class="card-body">
                                    <div class="item-card7-desc">
                                        <div class="item-card7-text">
                                            <a href="{{ $detailsUrl }}" class="text-dark"><h4 class="">{{ $title }}</h4></a>
                                            @if($address)
                                                <p class=""><i class="icon icon-location-pin text-muted me-1"></i>{{ $address }}</p>
                                            @endif

                                            <h5 class="font-weight-bold mb-0">
                                                ${{ $price }}
                                                @if($isRent)
                                                    <span class="fs-12 font-weight-normal">شهريًا</span>
                                                @endif
                                            </h5>
                                        </div>

                                        {{-- تفاصيل حسب الفئة + عناصر مشتركة --}}
                                        <ul class="{{ $ulClass }}">
                                            {{-- مشترك: المساحة إن وُجدت --}}
                                            @if(!is_null($p->size))
                                                <li>
                                                    <a href="javascript:void(0);">
                                                        <i class="fa fa-arrows-alt text-muted me-1"></i>
                                                        {{ $p->size }} متر²
                                                    </a>
                                                </li>
                                            @endif

                                            @if($groupId === $TYPE_RESIDENTIAL || $groupId === $TYPE_RECREATION)
                                                {{-- سكني/ترفيهي: غرف + حمامات + موقف (إن وجدت) --}}
                                                @if(!is_null($p->bedroom))
                                                    <li>
                                                        <a href="javascript:void(0);" class="icons">
                                                            <i class="fa fa-bed text-muted me-1"></i>
                                                            {{ $p->bedroom }} غرف
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(!is_null($p->bathroom))
                                                    <li>
                                                        <a href="javascript:void(0);" class="icons">
                                                            <i class="fa fa-bath text-muted me-1"></i>
                                                            {{ $p->bathroom }} حمامات
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(!is_null($p->garage))
                                                    <li>
                                                        <a href="javascript:void(0);" class="icons">
                                                            <i class="fa fa-car text-muted me-1"></i>
                                                            {{ $p->garage }} {{ $p->garage == 1 ? 'موقف' : 'مواقف' }}
                                                        </a>
                                                    </li>
                                                @endif

                                            @elseif($groupId === $TYPE_COMMERCIAL)
                                                {{-- تجاري/خدمي: موقف (إن وجد) --}}
                                                @if(!is_null($p->garage))
                                                    <li>
                                                        <a href="javascript:void(0);" class="icons">
                                                            <i class="fa fa-car text-muted me-1"></i>
                                                            {{ $p->garage }} {{ $p->garage == 1 ? 'موقف' : 'مواقف' }}
                                                        </a>
                                                    </li>
                                                @endif

                                            @elseif($groupId === $TYPE_LANDS)
                                                {{-- أراضي: عادةً تكفي المساحة (عناصر إضافية حسب بياناتك إن لزم) --}}
                                                {{-- لا شيء إضافي هنا للحفاظ على نفس الشكل --}}
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <!-- ذيل الكرت -->
                                <div class="card-footer">
                                    <div class="d-flex mb-0">
                                <span class="fs-12">
                                    <i class="icon icon-event me-2 mt-1"></i>
                                    {{ optional($p->created_at)->translatedFormat('d F Y') }}
                                </span>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="مشاركة العقار">
                                                <i class="icon icon-share text-muted"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="item">
                            <div class="card mb-0">
                                <div class="card-body text-center py-6">
                                    لا توجد عقارات مميزة الآن.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('properties_featured') }}" class="btn btn-secondary btn-lg">عرض كل العقارات</a>
                </div>
            </div>
        </section>
        <!--/Featured Ads-->

        <!--Testimonials-->
        <section class="sptb position-relative pattern">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h1 class="text-white position-relative">آراء العملاء</h1>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="myCarousel" class="owl-carousel testimonial-owl-carousel">
                            <div class="item text-center">
                                <div class="row">
                                    <div class="col-xl-8 col-md-12 d-block mx-auto">
                                        <div class="testimonia">
                                            <div class="owl-controls clickable">
                                                <div class="owl-pagination">
                                                    <div class="owl-page active">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page ">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page">
                                                        <span class=""></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-white-80">
                                                <i class="fa fa-quote-left text-white-80"></i> ياخي أهم شي خلصتونا من علق بنقطة ليصلك السعر،وفقكم الله وسدد خطاكم.
                                            </p>
                                            <h3 class="title">سفيان من إدلب</h3>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item text-center">
                                <div class="row">
                                    <div class="col-xl-8 col-md-12 d-block mx-auto">
                                        <div class="testimonia">
                                            <div class="owl-controls clickable">
                                                <div class="owl-pagination">
                                                    <div class="owl-page ">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page active">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page">
                                                        <span class=""></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-white-80"><i class="fa fa-quote-left"></i> خيارات لا محدودة وشفافية في العرض بيض الله وجهكم والله يوفقكم.</p>
                                            <div class="testimonia-data">
                                                <h3 class="title">فادية من إدلب</h3>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item text-center">
                                <div class="row">
                                    <div class="col-xl-8 col-md-12 d-block mx-auto">
                                        <div class="testimonia">
                                            <div class="owl-controls clickable">
                                                <div class="owl-pagination">
                                                    <div class="owl-page ">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page">
                                                        <span class=""></span>
                                                    </div>
                                                    <div class="owl-page active">
                                                        <span class=""></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-white-80"><i class="fa fa-quote-left"></i>موقع فريد من نوعه شكرا للقائمين عليه من أعماق القلب</p>
                                            <div class="testimonia-data">
                                                <h3 class="title">محمد من حلب</h3>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--/Testimonials-->

        <!--Blogs-->
        <section class="sptb bg-white">
            @php
                // تجهيز البيانات: أول 5 عناصر (4 يسار + 1 يمين)
                $list = $locations instanceof \Illuminate\Support\Collection ? $locations->take(5) : collect([]);
                $firstFour = $list->take(4);
                $lastOne   = $list->slice(4, 1)->first();

                // رابط الصورة (نفس الكلاسات، بس مصدر الصورة ديناميكي)
                $imgUrl = function($loc) {
                    return $loc && $loc->photo
                        ? asset('uploads/'.$loc->photo)
                        : asset('uploads/default-location.jpg');
                };

                // تنسيق الأرقام (اختياري، ما يغيّر الشكل)
                if (class_exists(\NumberFormatter::class)) {
                    $fmt = new \NumberFormatter('ar_EG', \NumberFormatter::DECIMAL);
                    $fmtNum = fn($n) => $fmt->format($n ?? 0);
                } else {
                    $fmtNum = fn($n) => number_format($n ?? 0);
                }
            @endphp

            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>أفضل الأماكن المُدرجة</h2>
                    <p>سدّ الفجوة بين العرض والطلب مع أفضل العروض العقارية</p>
                </div>

                @if($list->isNotEmpty())
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-6">
                            <div class="row">
                                @foreach($firstFour as $loc)
                                    <div class="col-sm-12 col-lg-6 col-md-6 ">
                                        <div class="item-card overflow-hidden">
                                            <div class="item-card-desc">
                                                <div class="card text-center overflow-hidden">
                                                    <div class="card-img">
                                                        <img src="{{ $imgUrl($loc) }}" alt="{{ $loc->name }}" class="cover-image">
                                                    </div>
                                                    <div class="item-tags">
                                                        <div class="{{ $loop->index % 2 === 0 ? 'bg-primary' : 'bg-secondary' }} tag-option">
                                                            <i class="fa fa fa-heart-o me-1"></i> {{ $fmtNum($loc->properties_count ?? 0) }}
                                                        </div>
                                                    </div>
                                                    <div class="item-card-text">
                                                        <h4 class="mb-0">
                                                            <a href="{{ route('location', $lastOne->slug) }}" style="color:inherit; text-decoration:none;">
                                                                {{ $fmtNum($loc->properties_count ?? 0) }}
                                                                <span><i class="fa fa-map-marker me-1 text-primary"></i>
                                                                {{ $loc->name }}
                                                            </span>
                                                            </a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-xl-6 col-sm-12">
                            <div class="item-card overflow-hidden">
                                <div class="item-card-desc">
                                    <div class="card overflow-hidden mb-0">
                                        <div class="card-img">
                                            <img src="{{ $imgUrl($lastOne ?? null) }}" alt="{{ $lastOne->name ?? 'صورة' }}" class="cover-image">
                                        </div>
                                        <div class="item-tags">
                                            <div class="bg-primary tag-option">
                                                <i class="fa fa fa-heart-o me-1"></i> {{ $fmtNum($lastOne->properties_count ?? 0) }}
                                            </div>
                                        </div>
                                        <div class="item-card-text">

                                            <h4 class="mb-0">
                                                <a href="{{ route('location', $lastOne->slug) }}" style="color:inherit; text-decoration:none;">
                                                    {{ $fmtNum($lastOne->properties_count ?? 0) }}
                                                    <span><i class="fa fa-map-marker text-primary me-1"></i>
                                                    {{ $lastOne->name ?? '—' }}</span>
                                                </a>
                                            </h4>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('locations') }}" class="btn btn-secondary btn-lg">عرض كل المحافظات</a>
            </div>

        </section>
        <!--Blogs-->

        <!--Download -->
        <section class="sptb">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>تحميل</h2>
                    <p>حمّل التطبيق الخاص بنا على هاتفك المحمول</p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center text-wrap">
                            <div class="btn-list">
                                <a href="javascript:void(0);" class="btn btn-primary btn-lg mb-sm-0"><i class="fa fa-apple fa-1x me-2"></i> متجر آبل</a>
                                <a href="javascript:void(0);" class="btn btn-secondary btn-lg mb-sm-0"><i class="fa fa-android fa-1x me-2"></i> جوجل بلاي</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Download -->

        <!--Subscribe-->
        <section>
            <div class="cover-image sptb bg-background-color" data-bs-image-src="../assets/images/banners/banner4.jpg">
                <div class="content-text mb-0">
                    <div class="container">
                        <div class="text-center text-white ">
                            <h1 class="mb-2">الإشتراك</h1>
                            <p class="fs-16">إشترك معنا لترى كل جديد</p>
                            <div class="row">
                                <div class="col-lg-8 mx-auto d-block">
                                    <div class="mt-5">
                                        {{-- نستخدم الفورم المرتبط بالـ route --}}
                                        <form action="{{ route('subscriber_send_email') }}" method="post" class="form_subscribe_ajax">
                                            @csrf
                                            <div class="input-group sub-input mt-1">
                                                <input type="text" name="email" class="form-control input-lg" placeholder="ادخل البريد الإلكتروني">
                                                <button type="submit" class="btn btn-secondary btn-lg br-tr-3 br-br-3">
                                                    إشترك
                                                </button>
                                            </div>
                                            {{-- مكان إظهار الخطأ --}}
                                            <span class="text-danger error-text email_error d-block mt-2"></span>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--/Subscribe-->

        <!-- News -->
        <section class="sptb bg-white">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>أحدث المقالات</h2>
                    <p>سدّ الفجوة بين العرض والطلب مع أفضل العروض العقارية</p>
                </div>

                <div id="defaultCarousel" class="owl-carousel Card-owlcarousel owl-carousel-icons">
                    @forelse($latestPosts as $post)
                        @php
                            // إصلاح مسار الصورة: إن كانت القيمة مثل "post1.jpg" نعرضها من public/uploads
                            $img = $post->photo
                                ? asset('uploads/' . ltrim($post->photo, '/'))
                                : asset('assets/images/products/products/ed1.jpg'); // صورة افتراضية عند غياب الصورة
                        @endphp

                        <div class="item">
                            <div class="card mb-0">
                                <div class="item7-card-img">
                                    <a href="{{ route('post', $post->slug) }}"></a>
                                    <img src="{{ $img }}" alt="{{ $post->title }}" class="cover-image">
                                    <div class="item7-card-text">
                                <span class="badge badge-info">
                                    {{ optional($post->type)->name ?? 'بدون تصنيف' }}
                                </span>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <div class="item7-card-desc d-flex mb-2">
                                        <a href="javascript:void(0);" class="text-muted">
                                            <i class="fa fa-calendar-o text-muted me-2"></i>
                                            {{ optional($post->created_at)->format('Y-m-d') }}
                                        </a>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="text-muted">
                                                <i class="fa fa-comment-o text-muted me-2"></i>
                                                {{ number_format($post->comments_count ?? 0) }} تعليقات
                                            </a>
                                        </div>
                                    </div>

                                    <a href="{{ route('post', $post->slug) }}" class="text-dark">
                                        <h4 class="fs-20">{{ $post->title }}</h4>
                                    </a>

                                    <p>{{ \Illuminate\Support\Str::limit($post->short_description, 120) }}</p>

                                    <div class="d-flex align-items-center pt-2 mt-auto">
                                        <div class="ms-auto text-muted">
                                            <a href="javascript:void(0)" class="icon d-none d-md-inline-block ms-3">
                                                <i class="fe fe-eye me-1"></i>
                                            </a>
                                            <span class="ms-1">{{ number_format($post->total_views) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- يظل الكاروسل سليماً بدون عناصر في حال عدم وجود مقالات --}}
                    @endforelse
                </div>
            </div>
        </section>
        <!--News -->

    </section>
    <!--Categories-->

@endsection
