@extends('user.profile.shell', ['activeTab' => 'wishlist', 'counts' => $counts ?? []])

@section('profile_tab_content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 text-nowrap">
            <thead>
            <tr class="text-center">
                <th style="width:60px">#</th>
                <th>رقم</th>
                <th>العقار</th>
                <th>الموقع</th>
                <th>النوع</th>
                <th>غرف</th>
                <th>حمامات</th>
                <th>المساحة</th>
                <th>السعر</th>
                <th>الحالة</th>
                <th style="width:140px">الإجراء</th>
            </tr>
            </thead>
            <tbody>
            @forelse($wishlists as $i=>$row)
                @php $p = $row->property; @endphp
                <tr>
                    <td class="text-center">{{ $i+1 }}</td>
                    <td class="text-center">{{ $p?->id }}</td>
                    <td>
                        @if($p)
                            <a class="text-dark" href="{{ route('property_detail',$p->slug) }}" target="_blank">
                                <strong>{{ $p->name }}</strong>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ optional($p->location)->name }}</td>
                    <td>{{ optional($p->type)->name }}</td>
                    <td class="text-center">{{ $p?->bedroom }}</td>
                    <td class="text-center">{{ $p?->bathroom }}</td>
                    <td class="text-center">{{ $p?->size }} م²</td>
                    <td class="text-center">{{ $p?->price }}</td>
                    <td class="text-center">{{ $p?->status }}</td>
                    <td class="text-center">
                        @if($p)
                            <a href="{{ route('property_detail',$p->slug) }}" class="btn btn-primary btn-sm text-white"
                               title="عرض"><i class="fa fa-eye"></i></a>
                        @endif
                        <a href="{{ route('wishlist_delete',$row->id) }}" class="btn btn-danger btn-sm text-white"
                           onclick="return confirm('إزالة من المفضلة؟')" title="حذف">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">لا توجد عناصر</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
