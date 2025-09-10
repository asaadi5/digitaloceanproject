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
                            <li class="breadcrumb-item active text-white" aria-current="page">تعديل عقار</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    @php
        // تطبيع الغرض (old أولاً ثم قيمة الجدول) -> rent/buy فقط
        $rawPurpose = old('purpose', $property->purpose ?? 'buy');

        // المرافق المختارة مسبقاً (من الكونترولر عبر pivot)
        $existingAmenities = $existing_amenities ?? [];

        // قواعد الإيجار للعرض
        $rental = $rental_rules ?? collect();
        $rr = fn($key,$default='') => old("rental_rule.$key", optional($rental->get($key))->rule_value ?? $default);

        // القيمة الفعلية من قاعدة البيانات (موحَّدة حروفًا)
    $purp = strtolower((string)($property->purpose ?? 'buy'));

    // إن وُجد ?mtab=rent|buy نستخدمه للتحكّم في العرض الأولي فقط (لا يغيّر قيمة الحقل)
    $viewPurp = in_array(strtolower(request('mtab','')), ['buy','rent'], true)
        ? strtolower(request('mtab'))
        : $purp;

    @endphp

    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row g-0">

                <!-- الشريط الجانبي -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>

                <!-- المحتوى -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card mb-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">تعديل عقار</h3>
                            <a href="{{ route('agent_property_index') }}" class="btn btn-outline-secondary">رجوع</a>
                        </div>

                        <form action="{{ route('agent_property_update', $property->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body text-dark">

                                {{-- الصورة المميزة --}}
                                <div class="form-group mb-4">
                                    <label class="form-label">الصورة المميزة</label>
                                    <input type="file" class="form-control" name="featured_photo">
                                    @if($property->featured_photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/'.$property->featured_photo) }}" alt="" style="max-width:180px;height:auto;">
                                        </div>
                                    @endif
                                </div>

                                {{-- الاسم / السلاج / السعر --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">اسم العقار *</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $property->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">الرابط المختصر (Slug) *</label>
                                            <input type="text" class="form-control" name="slug" value="{{ old('slug', $property->slug) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <span class="d-inline-block purpose-sale">السعر *</span>
                                                <span class="d-inline-block purpose-rent">الإيجار (حسب الدورة) *</span>
                                            </label>
                                            <input type="text" class="form-control" name="price" value="{{ old('price', $property->price) }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- الوصف --}}
                                <div class="form-group mb-4">
                                    <label class="form-label">الوصف</label>
                                    <textarea class="form-control" name="description" rows="8">{{ old('description', $property->description) }}</textarea>
                                </div>

                                {{-- الموقع / النوع / الغرض --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label text-dark">الموقع *</label>
                                            <select name="location_id" class="form-control select2 form-select" required>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}" @selected(old('location_id', $property->location_id)==$location->id)>{{ $location->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label text-dark">النوع *</label>
                                            <select name="type_id" class="form-control select2 form-select" required>
                                                @foreach($types->skip(4) as $type)
                                                    <option value="{{ $type->id }}" @selected(old('type_id', $property->type_id)==$type->id)>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label text-dark">الغرض *</label>
                                        <select name="purpose" id="purpose" class="form-control select2 form-select" required>
                                            <option value="buy"  {{ $purp === 'buy'  ? 'selected' : '' }}>بيع</option>
                                            <option value="rent" {{ $purp === 'rent' ? 'selected' : '' }}>إيجار</option>
                                        </select>
                                    </div>

                                </div>

                                {{-- المنطقة / العنوان --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">المنطقة / الحي</label>
                                            <input type="text" class="form-control" name="area" value="{{ old('area', $property->area) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">العنوان *</label>
                                            <input type="text" class="form-control" name="address" value="{{ old('address', $property->address) }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- غرف / حمامات / المساحة --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">عدد الغرف *</label>
                                            <input type="number" min="0" class="form-control" name="bedroom" value="{{ old('bedroom', $property->bedroom) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">عدد الحمامات *</label>
                                            <input type="number" min="0" class="form-control" name="bathroom" value="{{ old('bathroom', $property->bathroom) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">المساحة (قدم²) *</label>
                                            <input type="number" min="0" class="form-control" name="size" value="{{ old('size', $property->size) }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- طابق / كراج / شرفة --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">الطابق</label>
                                            <input type="text" class="form-control" name="floor" value="{{ old('floor', $property->floor) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">كراج</label>
                                            <input type="text" class="form-control" name="garage" value="{{ old('garage', $property->garage) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">شرفة</label>
                                            <input type="text" class="form-control" name="balcony" value="{{ old('balcony', $property->balcony) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- سنة البناء / مميز؟ --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">سنة البناء</label>
                                            <input type="number" min="1800" class="form-control" name="built_year" value="{{ old('built_year', $property->built_year) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label text-dark">هل هو مميز؟</label>
                                            <select name="is_featured" class="form-control select2 form-select">
                                                <option value="Yes" @selected(old('is_featured',$property->is_featured)=='Yes')>نعم</option>
                                                <option value="No"  @selected(old('is_featured',$property->is_featured)=='No')>لا</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- المرافق (Pivot) --}}
                                <div class="form-group">
                                    <label class="form-label text-dark">المرافق</label>
                                    <div class="row">
                                        @foreach($amenities as $amenity)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input name="amenity[]" class="form-check-input"
                                                           type="checkbox"
                                                           value="{{ $amenity->id }}"
                                                           id="amenity_{{ $amenity->id }}"
                                                        {{ in_array($amenity->id, old('amenity', $existingAmenities)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                                        {{ $amenity->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- خريطة الموقع --}}
                                <div class="form-group mb-4">
                                    <label class="form-label">خريطة الموقع</label>
                                    <textarea class="form-control" name="map" rows="5">{{ old('map', $property->map) }}</textarea>
                                </div>

                                {{-- ====== قسم البيع ====== --}}
                                <div id="box-sale" class="{{ $viewPurp === 'rent' ? 'd-none' : '' }}">                                    <div class="card border">
                                        <div class="card-header">
                                            <h3 class="card-title">تفاصيل البيع</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رقم السجل العقاري</label>
                                                        <input type="text" class="form-control" name="registry_number" value="{{ old('registry_number', $property->registry_number) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">منطقة/قيد</label>
                                                        <input type="text" class="form-control" name="registry_zone" value="{{ old('registry_zone', $property->registry_zone) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رخصة البناء</label>
                                                        <input type="text" class="form-control" name="building_permit_no" value="{{ old('building_permit_no', $property->building_permit_no) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">نوع الملكية</label>
                                                        <select name="ownership_type" class="form-control select2 form-select">
                                                            <option value="">اختر نوع الملكية</option>
                                                            <option value="full"     @selected(old('ownership_type',$property->ownership_type)=='full')>كامل</option>
                                                            <option value="waqf"     @selected(old('ownership_type',$property->ownership_type)=='waqf')>وقف</option>
                                                            <option value="usufruct" @selected(old('ownership_type',$property->ownership_type)=='usufruct')>حق الانتفاع</option>
                                                            <option value="masha"    @selected(old('ownership_type',$property->ownership_type)=='masha')>مشاع</option>
                                                            <option value="hikr"     @selected(old('ownership_type',$property->ownership_type)=='hikr')>حكر</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">التصنيف التنظيمي</label>
                                                        <input type="text" class="form-control" name="zoning_class" value="{{ old('zoning_class', $property->zoning_class) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">مطابقة الكود</label>
                                                        <select name="build_code_compliance" class="form-control select2 form-select">
                                                            <option value="1" @selected(old('build_code_compliance',$property->build_code_compliance)==1)>مطابق</option>
                                                            <option value="0" @selected(old('build_code_compliance',$property->build_code_compliance)==0)>غير مطابق</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">مقاومة الزلازل</label>
                                                        <select name="earthquake_resistance" class="form-control select2 form-select">
                                                            <option value="1" @selected(old('earthquake_resistance',$property->earthquake_resistance)==1)>مقاوم</option>
                                                            <option value="0" @selected(old('earthquake_resistance',$property->earthquake_resistance)==0)>غير مقاوم</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="form-label">ملاحظات قانونية</label>
                                                <textarea name="legal_notes" class="form-control" rows="3">{{ old('legal_notes', $property->legal_notes) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ====== قسم الإيجار ====== --}}
                                <div id="box-rent" class="{{ $viewPurp === 'rent' ? '' : 'd-none' }}">                                    <div class="card border">
                                        <div class="card-header">
                                            <h3 class="card-title">قواعد الإيجار</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">دورية الدفع</label>
                                                        @php $cycle = $rr('payment_cycle','monthly'); @endphp
                                                        <select name="rental_rule[payment_cycle]" class="form-control select2 form-select">
                                                            <option value="monthly"    {{ $cycle=='monthly' ? 'selected' : '' }}>شهري</option>
                                                            <option value="quarterly"  {{ $cycle=='quarterly' ? 'selected' : '' }}>فصلي</option>
                                                            <option value="semiannual" {{ $cycle=='semiannual' ? 'selected' : '' }}>نصف سنوي</option>
                                                            <option value="annual"     {{ $cycle=='annual' ? 'selected' : '' }}>سنوي</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">قيمة التأمين</label>
                                                        <input type="text" class="form-control" name="rental_rule[deposit_amount]" value="{{ $rr('deposit_amount') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رسوم الطوابع/التسجيل</label>
                                                        <input type="text" class="form-control" name="rental_rule[stamp_fee]" value="{{ $rr('stamp_fee') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">سياسة الأضرار</label>
                                                        <input type="text" class="form-control" name="rental_rule[damages_policy]" value="{{ $rr('damages_policy') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">موعد التسليم</label>
                                                        <input type="text" class="form-control" name="rental_rule[handover_time]" value="{{ $rr('handover_time') }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const purpose = document.getElementById('purpose');
            const boxSale = document.getElementById('box-sale');
            const boxRent = document.getElementById('box-rent');

            const params = new URLSearchParams(window.location.search);
            const initialTab = (params.get('mtab') || '').toLowerCase();
            const override = (initialTab === 'rent' || initialTab === 'buy') ? initialTab : null;

            function apply(mode) {
                const isRent = (mode || '').toLowerCase() === 'rent';
                if (isRent) {
                    boxRent && boxRent.classList.remove('d-none');
                    boxSale && boxSale.classList.add('d-none');
                } else {
                    boxSale && boxSale.classList.remove('d-none');
                    boxRent && boxRent.classList.add('d-none');
                }
                document.querySelectorAll('.purpose-sale').forEach(e => e.style.display = isRent ? 'none' : 'inline-block');
                document.querySelectorAll('.purpose-rent').forEach(e => e.style.display = isRent ? 'inline-block' : 'none');
            }

            // تطبيق العرض الأولي (أولوية للـ mtab إن وُجد)
            apply(override ?? (purpose?.value || ''));

            // التبديل عند تغيير المستخدم للحقل
            purpose && purpose.addEventListener('change', () => apply(purpose.value || ''));
        });
    </script>

@endsection
