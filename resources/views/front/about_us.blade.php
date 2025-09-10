@extends('front.layouts.master')

@section('main_content')
    @php
        // صور من dist-front/uploads
        $img = fn($file) => asset('dist-front/uploads/'.$file);

        // زر الدعوة للعمل (ينشر إعلاناً)
        $isAgent = Auth::guard('agent')->check();
        $createUrl = \Illuminate\Support\Facades\Route::has('agent_property_create')
            ? route('agent_property_create')
            : url('agent/property/create');

        // لو مش وكيل: يوجّه لتسجيل دخول الوكلاء مع redirect للعودة لصفحة الإنشاء
        $ctaUrl = $isAgent
            ? $createUrl
            : (\Illuminate\Support\Facades\Route::has('agent_login')
                ? route('agent_login', ['redirect' => $createUrl])
                : url('agent/login?redirect='.urlencode($createUrl)));

        // فورماتر أرقام (يستخدم num() إن وُجد)
        $fmt = fn($n) => function_exists('num') ? num($n) : number_format((int)$n);

        // الإحصائيات: نقرأ من $stats إن وُجدت، وإلا نجلب من الداتا… مع أرقام افتراضية كـ fallback
        $agentsCount = $stats['agents'] ?? (class_exists('\App\Models\Agent')    ? \App\Models\Agent::where('status',1)->count() : 0);
        $dealsCount  = $stats['deals']  ?? (class_exists('\App\Models\Order')    ? \App\Models\Order::count() : 0);
        $propsCount  = $stats['properties'] ?? (class_exists('\App\Models\Property') ? \App\Models\Property::where('status','active')->count() : 0);
        $happyCount  = $stats['happy']  ?? (class_exists('\App\Models\User')     ? \App\Models\User::count() : 0);
    @endphp

        <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3 sptb-2" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">من نحن</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">من نحن</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--section-->
    <section class="sptb">
        <div class="container">
            <div class="text-justify">
                <h2 class="mb-4">لماذا عقارات الشمال؟</h2>
                <h4 class="leading-normal">منصّة عقارية سورية موثوقة لبيع وشراء وتأجير العقارات في جميع المحافظات</h4>
                <p class="leading-normal">في <strong>عقارات الشمال</strong> نوحّد السوق العقارية على مستوى سوريا، ونوفّر للمستخدمين عروضًا دقيقة ومحدّثة تغطي دمشق، حلب، حمص، حماة، اللاذقية، طرطوس، السويداء، دير الزور، الرقة، الحسكة، إدلب وسائر المدن والبلدات.</p>
                <p class="leading-normal">نلتزم بالشفافية والجودة: مراجعة إعلانات، بيانات ملكية وترخيص واضحة، تحديد دقيق للموقع والخدمات القريبة، وقنوات تواصل آمنة لضمان تجربة موثوقة لكل الأطراف.</p>
                <p class="leading-normal mb-0">نرافقك من أول زيارة وحتى إتمام العقد—بحث ذكي، إشعارات فورية، دعم فني واستشارات متخصصة—لتنجز صفقتك بثقة وسرعة.</p>
            </div>
        </div>
    </section>
    <!--/section-->

    <!--How to work-->
    <section class="sptb bg-white">
        <div class="container">
            <div class="section-title center-block text-center">
                <h2>كيف يعمل؟</h2>
                <p>أربع خطوات عملية لنشر إعلانك والوصول إلى المهتمين في كل سوريا</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="">
                        <div class="mb-lg-0 mb-4">
                            <div class="service-card text-center">
                                <div class="bg-white icon-bg box-shadow icon-service  about">
                                    <img src="../assets/images/products/about/employees.png" alt="صورة">
                                </div>
                                <div class="servic-data mt-3">
                                    <h4 class="font-weight-semibold mb-2">سجّل</h4>
                                    <p class="text-muted mb-0">أنشئ حسابك خلال دقائق عبر البريد أو رقم الهاتف وابدأ استخدام المنصّة بسهولة.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="">
                        <div class="mb-lg-0 mb-4">
                            <div class="service-card text-center">
                                <div class="bg-white icon-bg box-shadow icon-service  about">
                                    <img src="../assets/images/products/about/megaphone.png" alt="صورة">
                                </div>
                                <div class="servic-data mt-3">
                                    <h4 class="font-weight-semibold mb-2">أكمل ملفك</h4>
                                    <p class="text-muted mb-0">وثّق بياناتك وحدّد المحافظات والأحياء المفضّلة لتصلك العروض المناسبة فورًا.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="">
                        <div class="mb-sm-0 mb-4">
                            <div class="service-card text-center">
                                <div class="bg-white icon-bg box-shadow icon-service  about">
                                    <img src="../assets/images/products/about/pencil.png" alt="صورة">
                                </div>
                                <div class="servic-data mt-3">
                                    <h4 class="font-weight-semibold mb-2">أضف عقارك</h4>
                                    <p class="text-muted mb-0">أدخل تفاصيل دقيقة، صورًا عالية الجودة، موقعًا على الخريطة، وسعرًا واضحًا ثم انشر الإعلان.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="">
                        <div class="">
                            <div class="service-card text-center">
                                <div class="bg-white icon-bg box-shadow icon-service  about">
                                    <img src="../assets/images/products/about/coins.png" alt="صورة">
                                </div>
                                <div class="servic-data mt-3">
                                    <h4 class="font-weight-semibold mb-2">استقبل العروض</h4>
                                    <p class="text-muted mb-0">تواصل بأمان، حدّد مواعيد المعاينة، وأتمم البيع أو الإيجار بمرونة وسرعة.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/How to work-->

    <!--post section-->

    <section>
        <div class="cover-image sptb bg-background-color" data-bs-image-src="{{ $img('banner4.jpg') }}">
            <div class="content-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h2 class="mb-2 display-5">جاهز لنشر إعلانك على عقارات الشمال؟</h2>
                        <p>انشر إعلانك مجانًا الآن ليصل إلى آلاف الباحثين عن العقار في عموم الجمهورية العربية السورية.</p>
                        <div class="mt-5">
                            <a href="{{ $ctaUrl }}" class="btn btn-secondary btn-lg">إعلان عقاري مجاني</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--/post section-->

    <!--section-->
    <section class="sptb">
        <div class="container">
            <div class="section-title center-block text-center">
                <h2>لماذا تختارنا؟</h2>
                <p>خبرة محلية بتغطية وطنية وتقنيات تسويق حديثة لضمان تجربة موثوقة</p>
            </div>
            <div class="row ">
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-success  mb-3">
                                    <i class="fa fa-university  text-white"></i>
                                </div>
                                <h3>تسهيلات تمويلية</h3>
                                <p>ربطٌ مع بنوك وشركاء تمويل يقدمون حلولًا مناسبة للعقارات السكنية والتجارية في مختلف المحافظات.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-primary mb-3">
                                    <i class="fa fa-pencil-square-o  text-white"></i>
                                </div>
                                <h3>تسجيل فوري</h3>
                                <p>إنشاء حساب وتفعيل الإعلانات مباشرةً مع لوحة تحكّم سهلة وتقارير أداء واضحة.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-secondary mb-3">
                                    <i class="fa fa-handshake-o  text-white"></i>
                                </div>
                                <h3>استشارات استثمارية</h3>
                                <p>تحليلات تسعير ومؤشرات العائد حسب المدينة والحي مع توصيات مدعومة بالبيانات.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card mb-lg-0">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-warning mb-3">
                                    <i class="fa fa-cubes   text-white"></i>
                                </div>
                                <h3>خدمات البناء والصيانة</h3>
                                <p>شبكة تنفيذ وصيانة موثوقة لأعمال الإكساء، الكهرباء، والميكانيك بإشراف مختصين.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card mb-lg-0 mb-md-0">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-danger mb-3">
                                    <i class="fa fa-cogs   text-white"></i>
                                </div>
                                <h3>خدمة عملاء مخصّصة</h3>
                                <p>دعم عبر الهاتف والواتساب والبريد، ومتابعة حتى إغلاق الصفقة بنجاح.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 features">
                    <div class="card mb-0">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-info mb-3">
                                    <i class="fa fa-flask  text-white"></i>
                                </div>
                                <h3>إدارة وصيانة العقار</h3>
                                <p>إدارة أملاك، تحصيل إيجارات، وتقارير دورية للحفاظ على قيمة استثمارك.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/section-->

    <!--Statistics-->
    <!--Statistics-->
    <section>
        <div class="about-1 cover-image sptb bg-background-color" data-bs-image-src="{{ $img('banner5.jpg') }}">
            <div class="content-text mb-0 text-white info">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status md-mb-0">
                                <div class="counter-icon text-danger">
                                    <i class="icon icon-people"></i>
                                </div>
                                <h5>الوكلاء المعتمدون</h5>
                                <h2 class="counter mb-0 font-weight-bold">{{ $fmt($agentsCount) }}</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status-1 md-mb-0">
                                <div class="counter-icon text-warning">
                                    <i class="icon icon-rocket"></i>
                                </div>
                                <h5>الصفقات المنجزة</h5>
                                <h2 class="counter mb-0 font-weight-bold">{{ $fmt($dealsCount) }}</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status md-mb-0">
                                <div class="counter-icon text-primary">
                                    <i class="icon icon-docs"></i>
                                </div>
                                <h5>العقارات المدرجة</h5>
                                <h2 class="counter mb-0 font-weight-bold">{{ $fmt($propsCount) }}</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status">
                                <div class="counter-icon text-success">
                                    <i class="icon icon-emotsmile"></i>
                                </div>
                                <h5>العملاء السعداء</h5>
                                <h2 class="counter font-weight-bold">{{ $fmt($happyCount) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Statistics-->

@endsection
