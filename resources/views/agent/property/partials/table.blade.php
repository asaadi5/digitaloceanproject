<table class="table table-bordered table-hover mb-0 text-nowrap">
    <thead>
    <tr>
        <th>#</th>
        <th>الصورة المميّزة</th>
        <th>الاسم</th>
        <th>الوكيل</th>
        <th>الموقع</th>
        <th>النوع</th>
        <th>الغرض</th>
        <th>هل مميّز؟</th>
        <th>الحالة</th>
        <th>الخيارات</th>
        <th>الإجراءات</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $property)
        @php
            $purposeRaw = strtolower($property->purpose);
            $purposeAr  = $purposeRaw === 'sale' ? 'بيع' : ($purposeRaw === 'rent' ? 'إيجار' : $property->purpose);
            $isFeatured = strtolower($property->is_featured) === 'yes';
            $st         = strtolower($property->status);
            $editTab    = $purposeRaw === 'rent' ? 'tab-rent' : 'tab-sale'; // تبويب التعديل المناسب
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <img src="{{ asset('uploads/' . $property->featured_photo) }}" alt="صورة"
                     style="width:120px;height:auto;">
            </td>
            <td>{{ $property->name }}</td>
            <td>{{ optional($property->agent)->name }}</td>
            <td>{{ optional($property->location)->name }}</td>
            <td>{{ optional($property->type)->name }}</td>
            <td>{{ $purposeAr }}</td>
            <td>
                @if($isFeatured)
                    <span class="badge bg-success">نعم</span>
                @else
                    <span class="badge bg-danger">لا</span>
                @endif
            </td>
            <td>
                <span class="badge {{ $st === 'active' ? 'bg-success' : ($st === 'sold' ? 'bg-secondary' : 'bg-warning') }}">
                    {{ $st === 'active' ? 'نشط' : ($st === 'sold' ? 'مباع' : 'معلّق') }}
                </span>
            </td>
            <td>
                <a href="{{ route('agent_property_photo_gallery',$property->id) }}" class="btn btn-primary btn-sm">معرض الصور</a>
                <a href="{{ route('agent_property_video_gallery',$property->id) }}" class="btn btn-primary btn-sm">معرض الفيديو</a>
            </td>
            <td class="text-nowrap">
                {{-- عرض التفاصيل --}}
                <a href="{{ route('agent_property_show', $property->id) }}"
                   class="btn btn-info btn-sm text-white" title="عرض التفاصيل">
                    <i class="fa fa-eye"></i>
                </a>

                {{-- تحرير: يفتح تبويب مطابق للغرض --}}
                @php
                    $mtab = (strtolower($property->purpose) === 'rent') ? 'rent' : 'buy';
                @endphp
                <a href="{{ route('agent_property_edit', ['id' => $property->id, 'mtab' => $mtab]) }}"
                   class="btn btn-warning btn-sm text-white" title="تحرير">
                    <i class="fa fa-pencil"></i>
                </a>

                {{-- حذف --}}
                <a href="{{ route('agent_property_delete', $property->id) }}"
                   class="btn btn-danger btn-sm text-white" title="حذف"
                   onclick="return confirm('هل أنت متأكد من الحذف؟');">
                    <i class="fa fa-trash-o"></i>
                </a>
            </td>

        </tr>
    @empty
        <tr>
            <td colspan="11" class="text-center text-muted">لا توجد بيانات</td>
        </tr>
    @endforelse
    </tbody>
</table>
