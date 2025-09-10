@extends('user.profile.shell', ['activeTab' => 'profile', 'counts' => $counts ?? []])

@section('profile_tab_content')
    @php
        $u = auth()->user();

    @endphp

    <h5 class="mb-3">البيانات الشخصية</h5>
    <ul class="usertab-list mb-0">
        <li><span class="font-weight-semibold">الاسم الكامل:</span> {{ $u?->name }}</li>
        <li><span class="font-weight-semibold">اسم المستخدم:</span> {{ $u?->username ?? '—' }}</li>
        <li><span class="font-weight-semibold">البريد الإلكتروني:</span> {{ $u?->email }}</li>
        <li><span class="font-weight-semibold">الهاتف:</span> {{ $u?->phone ?? '—' }}</li>
        <li><span class="font-weight-semibold">المدينة:</span> {{ $u?->city ?? '—' }}</li>
        <li><span class="font-weight-semibold">العنوان:</span> {{ $u?->address ?? '—' }}</li>

        <li><span class="font-weight-semibold">مستخدم منذ:</span> <span class="date-ar">{{ar_date($u?->created_at)}}</span></li>

    </ul>
@endsection
