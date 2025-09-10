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
                            <li class="breadcrumb-item active text-white" aria-current="page">الدفع</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Payments-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row g-0">

                <!-- الشريط الجانبي -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي -->

                <!-- المحتوى -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="card-title">إدارة الإشتراك</h3>
                        </div>

                        <div class="card-body">
                            {{-- نحدّد افتراضيًا أول باقة إن وُجدت --}}
                            @php
                                $selectedId = old('package_id') ?? ($packages->first()->id ?? null);
                            @endphp

                                <!-- الصف العلوي: الخطة الحالية + تغيير الخطة -->
                            <div class="row g-4 align-items-stretch">

                                <!-- الخطة الحالية -->
                                <div class="col-lg-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header bg-transparent border-0">
                                            <h6 class="mb-0">الخطة الحالية</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($current_order)
                                                <div class="pricingTable2 pink text-center">
                                                    <div class="pricingTable2-header">
                                                        <h3 class="mb-1">{{ $current_order->package->name ?? '—' }}</h3>
                                                        <span class="text-muted">
                                                            @if($days_left > 0)
                                                                {{ (int)$days_left }} يوم متبقّي
                                                            @else
                                                                منتهية
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="pricing-plans d-flex justify-content-center align-items-end gap-2 my-3">
                                                        <span class="price-value1">
                                                            <i class="fa fa-usd"></i>
                                                            <span>{{ $current_order->package->price ?? 0 }}</span>
                                                        </span>
                                                        <span class="month"> / شهر</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    لا تملك اشتراكًا فعّالًا حاليًا.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- تغيير الخطة (من جدول packages) -->
                                <div class="col-lg-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">تغيير الخطة</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                @foreach($packages as $pkg)
                                                    <div class="col-12">
                                                        <input type="radio"
                                                               class="btn-check"
                                                               name="plan_radio"
                                                               id="plan-{{ $pkg->id }}"
                                                               value="{{ $pkg->id }}"
                                                            @checked($selectedId == $pkg->id)>
                                                        <label class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center rounded-3"
                                                               for="plan-{{ $pkg->id }}">
                                                            <span class="fw-semibold">{{ $pkg->name }}</span>
                                                            <span class="small text-muted">{{ $pkg->price }}$ / شهر</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @php
                                                // منطق الرسالة الإرشادية حسب مقارنة السعر
                                                $infoText = 'بعد اختيار الخطة وطريقة الدفع اضغط “زر الدفع”.';
                                                if ($current_order && $current_order->package) {
                                                    $cur = $current_order->package->price;
                                                    $sel = optional($packages->firstWhere('id',$selectedId))->price;
                                                    if ($sel !== null) {
                                                        if ($sel > $cur) $infoText = 'ملاحظة: الترقية تُفعَّل فورًا مع احتساب رصيد الأيام المتبقية كأيام إضافية.';
                                                        elseif ($sel == $cur) $infoText = 'ملاحظة: التجديد لنفس الخطة يبدأ تلقائيًا من نهاية اشتراكك الحالي.';
                                                        else $infoText = 'ملاحظة: التخفيض إلى خطة أرخص سيبدأ تلقائيًا عند نهاية اشتراكك الحالي.';
                                                    }
                                                }
                                            @endphp

                                            <div id="plan-hint" class="alert alert-info py-2 mt-3 mb-0 small">
                                                {{ $infoText }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- طريقة الدفع -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">طريقة الدفع</h3>
                                </div>
                                <div class="card-body">
                                    <div class="card-pay">
                                        <ul class="tabs-menu nav justify-content-center align-items-center" role="tablist">
                                            <li class=""><a href="#tab1" class="active" data-bs-toggle="tab"><i class="fa fa-cc-stripe"></i> سترايب</a></li>
                                            <li><a href="#tab2" data-bs-toggle="tab"><i class="fa fa-paypal"></i> باي بال</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active show" id="tab1">
                                                <form action="{{ route('agent_stripe') }}" method="post" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="package_id" id="selected_package_id" value="{{ $selectedId }}">
                                                    <button type="submit" class="btn btn-secondary btn-sm buy-button">ادفع عبر سترايب</button>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="tab2">
                                                <form action="{{ route('agent_paypal') }}" method="post" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="package_id" id="selected_package_id_pp" value="{{ $selectedId }}">
                                                    <button type="submit" class="btn btn-secondary btn-sm buy-button">ادفع عبر باي بال</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/طريقة الدفع -->
                        </div>
                    </div>
                    <!-- /المحتوى -->
                </div>
            </div>
        </div>
    </section>

    <script>
        // مزامنة الراديو مع الحقول المخفية + تلميح الحالة
        const radios = document.querySelectorAll('input[name="plan_radio"]');
        const hid1 = document.getElementById('selected_package_id');
        const hid2 = document.getElementById('selected_package_id_pp');
        const hint = document.getElementById('plan-hint');

        const priceMap = @json($packages->map(fn($p)=>['id'=>$p->id,'price'=>$p->price])->keyBy('id'));
        const currentPrice = @json($current_order->package->price ?? null);

        function updateHint() {
            const selId = document.querySelector('input[name="plan_radio"]:checked')?.value;
            if (!selId || currentPrice === null) return;
            const selPrice = priceMap[selId]?.price ?? null;
            if (selPrice === null) return;
            if (selPrice > currentPrice) hint.innerText = 'ملاحظة: الترقية تُفعَّل فورًا مع احتساب رصيد الأيام المتبقية كأيام إضافية.';
            else if (selPrice === currentPrice) hint.innerText = 'ملاحظة: التجديد لنفس الخطة يبدأ تلقائيًا من نهاية اشتراكك الحالي.';
            else hint.innerText = 'ملاحظة: التخفيض إلى خطة أرخص سيبدأ تلقائيًا عند نهاية اشتراكك الحالي.';
        }

        radios.forEach(r => {
            r.addEventListener('change', () => {
                hid1.value = r.value;
                hid2.value = r.value;
                updateHint();
            });
        });
    </script>
@endsection
