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
                            <li class="breadcrumb-item active text-white" aria-current="page">إضافة عقار </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Add-properties-->
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
                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">إضافة عقار</h3>
                        </div>

                        <div class="card-body">
                            <div class="ads-tabs">
                                <div class="tabs-menus">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs">
                                        <li class=""><a href="#tab-sale" class="active" data-bs-toggle="tab">للبيع</a></li>
                                        <li><a href="#tab-rent" data-bs-toggle="tab">للإيجار</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">

                                    <!-- ===================== تبويب البيع ===================== -->
                                    <div class="tab-pane active userprof-tab" id="tab-sale">
                                        <form action="{{ route('agent_property_store') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="purpose" value="buy">

                                            <!-- صورة الغلاف -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">الصورة المميزة *</label>
                                                <input type="file" class="form-control" name="featured_photo" required>
                                            </div>

                                            <!-- الاسم / السلاج / السعر -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">اسم العقار *</label>
                                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">الرابط المختصر (Slug) *</label>
                                                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">السعر *</label>
                                                        <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- الوصف -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">الوصف</label>
                                                <textarea class="form-control" name="description" rows="8">{{ old('description') }}</textarea>
                                            </div>

                                            <!-- الموقع / النوع / المنطقة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">الموقع *</label>
                                                        <select name="location_id" class="form-control select2 form-select" required>
                                                            @foreach($locations as $location)
                                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">النوع *</label>
                                                        <select name="type_id" class="form-control select2 form-select" required>
                                                            @foreach($types->skip(4) as $type)
                                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">المنطقة / الحي</label>
                                                        <input type="text" name="area" class="form-control" value="{{ old('area') }}" placeholder="مثال: سرمدا - الحي الشمالي">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- غرف / حمامات / المساحة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">عدد الغرف *</label>
                                                        <input type="number" min="0" class="form-control" name="bedroom" value="{{ old('bedroom') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">عدد الحمامات *</label>
                                                        <input type="number" min="0" class="form-control" name="bathroom" value="{{ old('bathroom') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">المساحة (قدم²) *</label>
                                                        <input type="number" min="0" class="form-control" name="size" value="{{ old('size') }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- طابق / كراج / شرفة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">الطابق</label>
                                                        <input type="text" class="form-control" name="floor" value="{{ old('floor') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">كراج</label>
                                                        <input type="text" class="form-control" name="garage" value="{{ old('garage') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">شرفة</label>
                                                        <input type="text" class="form-control" name="balcony" value="{{ old('balcony') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- العنوان / سنة البناء / مميز؟ -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">العنوان *</label>
                                                        <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">سنة البناء</label>
                                                        <input type="number" min="1800" class="form-control" name="built_year" value="{{ old('built_year') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">هل هو مميز؟</label>
                                                        <select name="is_featured" class="form-control select2 form-select">
                                                            <option value="Yes" {{ old('is_featured') == 'Yes' ? 'selected' : '' }}>نعم</option>
                                                            <option value="No"  {{ old('is_featured') == 'No'  ? 'selected' : '' }}>لا</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- المرافق -->
                                            <div class="form-group">
                                                <label class="form-label text-dark">المرافق</label>
                                                <div class="row">
                                                    @foreach($amenities as $amenity)
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input name="amenity[]" class="form-check-input" type="checkbox" value="{{ $amenity->id }}" id="sale_amenity_{{ $amenity->id }}">
                                                                <label class="form-check-label" for="sale_amenity_{{ $amenity->id }}">
                                                                    {{ $amenity->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- خريطة الموقع -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">خريطة الموقع</label>
                                                <textarea class="form-control" name="map" rows="5" placeholder='الصق كود خريطة Google (iframe) هنا'>{{ old('map') }}</textarea>
                                            </div>

                                            <!-- حقول قانونية (بيع) -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رقم السجل العقاري</label>
                                                        <input type="text" class="form-control" name="registry_number" value="{{ old('registry_number') }}" placeholder="REG-5-1001">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رقم المنطقة العقارية</label>
                                                        <input type="text" class="form-control" name="registry_zone" value="{{ old('registry_zone') }}" placeholder="المنطقة/المخطط">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رقم رخصة البناء</label>
                                                        <input type="text" class="form-control" name="building_permit_no" value="{{ old('building_permit_no') }}" placeholder="BN-5-2004">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">نوع الملكية</label>
                                                        <select name="ownership_type" class="form-control select2 form-select">
                                                            <option value="">اختر نوع الملكية</option>
                                                            <option value="full">كامل</option>
                                                            <option value="masha">مشاع</option>
                                                            <option value="hikr">حكر</option>
                                                            <option value="waqf">وقف</option>
                                                            <option value="usufruct">حق الانتفاع</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">التصنيف التنظيمي</label>
                                                        <input type="text" class="form-control" name="zoning_class" value="{{ old('zoning_class') }}" placeholder="سكني/تجاري/...">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">مطابقة الكود</label>
                                                        <select name="build_code_compliance" class="form-control select2 form-select">
                                                            <option value="1">مطابق</option>
                                                            <option value="0">غير مطابق</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">مقاومة الزلازل</label>
                                                        <select name="earthquake_resistance" class="form-control select2 form-select">
                                                            <option value="1">مقاوم</option>
                                                            <option value="0">غير مقاوم</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ملاحظات قانونية -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">ملاحظات قانونية</label>
                                                <textarea name="legal_notes" class="form-control" rows="4" placeholder="أي ملاحظات قانونية / توضيحات...">{{ old('legal_notes') }}</textarea>
                                            </div>

                                            <!-- رابط فيديو اختياري -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">رابط فيديو (اختياري)</label>
                                                <input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url') }}">
                                            </div>

                                            <!-- رفع صور متعددة (اختياري) -->
                                            <div class="form-group mb-4">
                                                <label class="form-label text-dark">رفع صور إضافية (اختياري)</label>
                                                <input id="gallery_sale" type="file" name="gallery_photos[]" accept=".jpg,.jpeg,.png,.gif,.svg,image/*" multiple class="form-control">
                                            </div>

                                            <div class="card-footer px-0">
                                                <button type="submit" class="btn btn-primary">إضافة العقار (بيع)</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- ===================== تبويب الإيجار ===================== -->
                                    <div class="tab-pane userprof-tab" id="tab-rent">
                                        <form action="{{ route('agent_property_store') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="purpose" value="rent">

                                            <!-- صورة الغلاف -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">الصورة المميزة *</label>
                                                <input type="file" class="form-control" name="featured_photo" required>
                                            </div>

                                            <!-- الاسم / السلاج / الإيجار الشهري -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">اسم العقار *</label>
                                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">الرابط المختصر (Slug) *</label>
                                                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">الإيجار الشهري *</label>
                                                        <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- الوصف -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">الوصف</label>
                                                <textarea class="form-control" name="description" rows="8">{{ old('description') }}</textarea>
                                            </div>

                                            <!-- الموقع / النوع / المنطقة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">الموقع *</label>
                                                        <select name="location_id" class="form-control select2 form-select" required>
                                                            @foreach($locations as $location)
                                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">النوع *</label>
                                                        <select name="type_id" class="form-control select2 form-select" required>
                                                            @foreach($types as $type)
                                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">المنطقة / الحي</label>
                                                        <input type="text" name="area" class="form-control" value="{{ old('area') }}" placeholder="مثال: سرمدا - الحي الشمالي">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- غرف / حمامات / المساحة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">عدد الغرف *</label>
                                                        <input type="number" min="0" class="form-control" name="bedroom" value="{{ old('bedroom') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">عدد الحمامات *</label>
                                                        <input type="number" min="0" class="form-control" name="bathroom" value="{{ old('bathroom') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">المساحة (قدم²) *</label>
                                                        <input type="number" min="0" class="form-control" name="size" value="{{ old('size') }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- طابق / كراج / شرفة -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">الطابق</label>
                                                        <input type="text" class="form-control" name="floor" value="{{ old('floor') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">كراج</label>
                                                        <input type="text" class="form-control" name="garage" value="{{ old('garage') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">شرفة</label>
                                                        <input type="text" class="form-control" name="balcony" value="{{ old('balcony') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- العنوان / سنة البناء / مميز؟ -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">العنوان *</label>
                                                        <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">سنة البناء</label>
                                                        <input type="number" min="1800" class="form-control" name="built_year" value="{{ old('built_year') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">هل هو مميز؟</label>
                                                        <select name="is_featured" class="form-control select2 form-select">
                                                            <option value="Yes" {{ old('is_featured') == 'Yes' ? 'selected' : '' }}>نعم</option>
                                                            <option value="No"  {{ old('is_featured') == 'No'  ? 'selected' : '' }}>لا</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- المرافق -->
                                            <div class="form-group">
                                                <label class="form-label text-dark">المرافق</label>
                                                <div class="row">
                                                    @foreach($amenities as $amenity)
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input name="amenity[]" class="form-check-input" type="checkbox" value="{{ $amenity->id }}" id="rent_amenity_{{ $amenity->id }}">
                                                                <label class="form-check-label" for="rent_amenity_{{ $amenity->id }}">
                                                                    {{ $amenity->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- خريطة الموقع -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">خريطة الموقع</label>
                                                <textarea class="form-control" name="map" rows="5" placeholder='الصق كود خريطة Google (iframe) هنا'>{{ old('map') }}</textarea>
                                            </div>

                                            <!-- قواعد الإيجار -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-dark">دورية الدفع</label>
                                                        <select name="rental_rule[payment_cycle]" class="form-control select2 form-select">
                                                            <option value="monthly">شهري</option>
                                                            <option value="quarterly">فصلي</option>
                                                            <option value="semiannual">نصف سنوي</option>
                                                            <option value="annual">سنوي</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">قيمة التأمين</label>
                                                        <input type="text" class="form-control" name="rental_rule[deposit_amount]" placeholder="مثال: 500000 SYP">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">رسوم الطوابع/التسجيل</label>
                                                        <input type="text" class="form-control" name="rental_rule[stamp_fee]" placeholder="مثال: 10000 SYP">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">سياسة الأضرار</label>
                                                        <input type="text" class="form-control" name="rental_rule[damages_policy]" placeholder="مثال: الإصلاح على المستأجر إن كان متسبباً">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">موعد التسليم</label>
                                                        <input type="text" class="form-control" name="rental_rule[handover_time]" placeholder="مثال: خلال 3 أيام بعد الدفع">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- رابط فيديو اختياري -->
                                            <div class="form-group mb-4">
                                                <label class="form-label">رابط فيديو (اختياري)</label>
                                                <input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url') }}">
                                            </div>

                                            <!-- رفع صور متعددة (اختياري) -->
                                            <div class="form-group mb-4">
                                                <label class="form-label text-dark">رفع صور إضافية (اختياري)</label>
                                                <input id="gallery_rent" type="file" name="gallery_photos[]" accept=".jpg,.jpeg,.png,.gif,.svg,image/*" multiple class="form-control">
                                            </div>

                                            <div class="card-footer px-0">
                                                <button type="submit" class="btn btn-primary">إضافة العقار (إيجار)</button>
                                            </div>
                                        </form>
                                    </div>

                                </div><!-- /tab-content -->
                            </div><!-- /ads-tabs -->
                        </div><!-- /card-body -->

                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Add-properties-->
@endsection
