@extends('user.profile.shell')

@section('profile_tab_content')
    @php($u = $user ?? Auth::user())
    <div class="profile-log-switch">
        <div class="media-heading">
            <h3 class="card-title mb-3 font-weight-bold">البيانات الشخصية</h3>
        </div>

        <ul class="usertab-list mb-0">
            <li>
                <span class="font-weight-semibold">الاسم الكامل :</span>
                {{ $u->name ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">اسم المستخدم :</span>
                {{ $u->username ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">البريد الإلكتروني :</span>
                {{ $u->email ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">رقم الهاتف :</span>
                {{ $u->phone ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">المدينة :</span>
                {{ $u->city ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">العنوان :</span>
                {{ $u->address ?? '—' }}
            </li>
            <li>
                <span class="font-weight-semibold">مستخدم منذ :</span>
                {{ optional($u->created_at)->translatedFormat('d F Y') }}
            </li>
        </ul>

        <div class="mt-4">
            <a href="{{ route('profile', ['edit' => 1]) }}" class="btn btn-primary btn-sm">تعديل الملف الشخصي</a>
            <a href="{{ route('wishlist') }}" class="btn btn-light btn-sm">العقارات المفضلة</a>
            <a href="{{ route('message') }}" class="btn btn-light btn-sm">الرسائل</a>
        </div>
    </div>
@endsection
