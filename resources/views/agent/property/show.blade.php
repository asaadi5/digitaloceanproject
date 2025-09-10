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
                            <li class="breadcrumb-item active text-white" aria-current="page">عرض العقار</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    @php
        // قواعد الإيجار (آمن)
        $rentalRules = isset($rental_rules)
            ? (is_array($rental_rules) ? collect($rental_rules)->keyBy('rule_key') : $rental_rules->keyBy('rule_key'))
            : \App\Models\PropertyRentalRule::where('property_id', $property->id)->get()->keyBy('rule_key');

        // مرافق
        $amenityIds   = $property->amenities ? explode(',', $property->amenities) : [];
        $amenityNames = count($amenityIds)
            ? \App\Models\Amenity::whereIn('id', $amenityIds)->pluck('name')
            : collect();

        // تحويلات عرض
        $yesNo = fn($v) => (string)$v === '1' || $v === 'Yes' ? 'نعم' : ((string)$v === '0' || $v === 'No' ? 'لا' : ($v ?? '-'));
        $ownershipMap = ['full'=>'كامل','waqf'=>'وقف','usufruct'=>'حق الانتفاع','masha'=>'مشاع','hikr'=>'حكر'];
        $ownershipLabel = $property->ownership_type ? ($ownershipMap[$property->ownership_type] ?? $property->ownership_type) : '-';
        $purp = strtolower(trim($property->purpose ?? 'buy'));

        // دالة تضمين الفيديو مع غلاف بدون الاعتماد على ratio من Bootstrap 5
        if (! function_exists('embedVideoHtmlSafe')) {
            function embedVideoHtmlSafe(?string $raw): string {
                $s = trim((string)$raw);
                if ($s === '') return '';

                // iframe جاهز
                if (stripos($s, '<iframe') !== false) {
                    // نلفّه بحاوية نسبة أبعاد حتى يظهر الغلاف أكيد
                    return '<div style="position:relative;width:100%;padding-bottom:56.25%;height:0;overflow:hidden;">'
                        . preg_replace('~<iframe~i', '<iframe style="position:absolute;inset:0;width:100%;height:100%;border:0;"', $s, 1)
                        . '</div>';
                }

                // YouTube (روابط متعددة أو ID)
                if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i', $s, $m)) {
                    $id = $m[1];
                    $src = "https://www.youtube.com/embed/$id";
                    return '<div style="position:relative;width:100%;padding-bottom:56.25%;height:0;overflow:hidden;">
                                <iframe src="'.$src.'" title="YouTube" allowfullscreen loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        style="position:absolute;inset:0;width:100%;height:100%;border:0;"></iframe>
                            </div>';
                }
                if (preg_match('~^[A-Za-z0-9_-]{8,64}$~', $s)) {
                    $src = "https://www.youtube.com/embed/$s";
                    return '<div style="position:relative;width:100%;padding-bottom:56.25%;height:0;overflow:hidden;">
                                <iframe src="'.$src.'" title="YouTube" allowfullscreen loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        style="position:absolute;inset:0;width:100%;height:100%;border:0;"></iframe>
                            </div>';
                }

                // Vimeo
                if (preg_match('~vimeo\.com/(\d+)~i', $s, $m)) {
                    $id = $m[1];
                    $src = "https://player.vimeo.com/video/$id";
                    return '<div style="position:relative;width:100%;padding-bottom:56.25%;height:0;overflow:hidden;">
                                <iframe src="'.$src.'" title="Vimeo" allowfullscreen loading="lazy"
                                        style="position:absolute;inset:0;width:100%;height:100%;border:0;"></iframe>
                            </div>';
                }

                // ملفات فيديو مباشرة
                if (preg_match('~\.(mp4|webm|ogg)(\?.*)?$~i', $s)) {
                    $url = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
                    return '<video class="w-100" controls preload="metadata" style="max-height:420px;"><source src="'.$url.'"></video>';
                }

                // افتراضي
                $url = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
                return '<a class="btn btn-primary" target="_blank" rel="noopener" href="'.$url.'">فتح</a>';
            }
        }
    @endphp

        <!--Show-property-->
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

                    <!-- بطاقة معلومات أساسية + زر تعديل -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">{{ $property->name }}</h3>
                            <a href="{{ route('agent_property_edit', $property->id) }}" class="btn btn-warning text-white">
                                <i class="fa fa-pencil me-1"></i> تعديل
                            </a>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row g-3">

                                <!-- صف 1 -->
                                <div class="col-md-3">
                                    <label class="form-label">الغرض</label>
                                    <input class="form-control" value="{{ $property->purpose ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">السعر</label>
                                    <input class="form-control" value="{{ $property->price ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الحالة</label>
                                    <input class="form-control" value="{{ $property->status ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">مميّز؟</label>
                                    <input class="form-control" value="{{ $yesNo($property->is_featured) }}" readonly>
                                </div>

                                <!-- صف 2 -->
                                <div class="col-md-3">
                                    <label class="form-label">الموقع</label>
                                    <input class="form-control" value="{{ $property->location->name ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">النوع</label>
                                    <input class="form-control" value="{{ $property->type->name ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المنطقة / الحي</label>
                                    <input class="form-control" value="{{ $property->area ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">سنة البناء</label>
                                    <input class="form-control" value="{{ $property->built_year ?? '-' }}" readonly>
                                </div>

                                <!-- صف 3 -->
                                <div class="col-md-3">
                                    <label class="form-label">الغرف</label>
                                    <input class="form-control" value="{{ $property->bedroom ?? 0 }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الحمامات</label>
                                    <input class="form-control" value="{{ $property->bathroom ?? 0 }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المساحة</label>
                                    <input class="form-control" value="{{ $property->size ?? 0 }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الطابق</label>
                                    <input class="form-control" value="{{ $property->floor ?? '-' }}" readonly>
                                </div>

                                <!-- صف 4 -->
                                <div class="col-md-6">
                                    <label class="form-label">العنوان</label>
                                    <input class="form-control" value="{{ $property->address ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">كراج</label>
                                    <input class="form-control" value="{{ $property->garage ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">شرفة</label>
                                    <input class="form-control" value="{{ $property->balcony ?? '-' }}" readonly>
                                </div>

                                <!-- الوصف -->
                                <div class="col-12">
                                    <label class="form-label">الوصف</label>
                                    <textarea class="form-control" rows="4" readonly>{{ $property->description ?? '' }}</textarea>
                                </div>

                                <!-- المرافق -->
                                <div class="col-12">
                                    <label class="form-label d-block">المرافق</label>
                                    @if($amenityNames->count())
                                        @foreach($amenityNames as $n)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $n }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">لا توجد مرافق محددة.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- خريطة الموقع -->
                    @if(!empty($property->map))
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">خريطة الموقع</h3>
                            </div>
                            <div class="card-body">
                                {!! $property->map !!}
                            </div>
                        </div>
                    @endif

                    <!-- تبويبات الصور والفيديو -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">المعرض</h3>
                        </div>
                        <div class="card-body">
                            <div class="ads-tabs">
                                <div class="tabs-menus">
                                    <ul class="nav panel-tabs">
                                        <li class=""><a href="#tab-photos" class="active" data-bs-toggle="tab">الصور</a></li>
                                        <li><a href="#tab-videos" data-bs-toggle="tab">الفيديو</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <!-- الصور -->
                                    <div class="tab-pane active" id="tab-photos">
                                        @if(isset($photos) && $photos->count())
                                            <div class="row">
                                                @foreach($photos as $p)
                                                    <div class="col-md-3 mb-3">
                                                        <div class="border rounded p-1">
                                                            <img src="{{ asset('uploads/'.$p->photo) }}" class="w-100" alt="photo">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">لا توجد صور.</div>
                                        @endif
                                    </div>

                                    <!-- الفيديو -->
                                    <div class="tab-pane" id="tab-videos">
                                        @if(isset($videos) && $videos->count())
                                            <div class="row">
                                                @foreach($videos as $v)
                                                    <div class="col-md-6 mb-3">
                                                        {!! embedVideoHtmlSafe($v->video) !!}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">لا توجد فيديوهات.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل إضافية حسب الغرض -->
                    @if($purp === 'rent')
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">قواعد الإيجار</h3>
                            </div>
                            <div class="card-body text-dark">
                                @if($rentalRules && $rentalRules->count())
                                    <div class="row">
                                        @foreach($rentalRules as $rk => $rule)
                                            <div class="col-md-4 mb-2">
                                                <strong>{{ $rk }}:</strong>
                                                <span class="ms-1">{{ $rule->rule_value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">لا توجد قواعد مسجّلة لهذا العقار.</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">تفاصيل البيع القانونية</h3>
                            </div>
                            <div class="card-body text-dark">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">رقم السجل العقاري</label>
                                        <input class="form-control" value="{{ $property->registry_number ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">منطقة/قيد</label>
                                        <input class="form-control" value="{{ $property->registry_zone ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">رخصة البناء</label>
                                        <input class="form-control" value="{{ $property->building_permit_no ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">نوع الملكية</label>
                                        <input class="form-control" value="{{ $ownershipLabel }}" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">التصنيف التنظيمي</label>
                                        <input class="form-control" value="{{ $property->zoning_class ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">مطابقة الكود</label>
                                        <input class="form-control" value="{{ $yesNo($property->build_code_compliance) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">مقاومة الزلازل</label>
                                        <input class="form-control" value="{{ $yesNo($property->earthquake_resistance) }}" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">ملاحظات قانونية</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $property->legal_notes ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- زر تعديل أسفل الصفحة أيضًا -->
                    <div class="text-end mb-3">
                        <a href="{{ route('agent_property_edit', $property->id) }}" class="btn btn-warning text-white">
                            <i class="fa fa-pencil me-1"></i> تعديل
                        </a>
                    </div>

                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Show-property-->
@endsection
