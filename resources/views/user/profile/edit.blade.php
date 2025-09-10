@extends('user.profile.shell', ['activeTab' => 'profile_edit', 'counts' => $counts ?? []])

@section('profile_tab_content')
    @php $u = auth()->user(); @endphp
    <h5 class="mb-3">تعديل الملف الشخصي</h5>

    <form action="{{ route('profile_submit') }}" method="post" enctype="multipart/form-data" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">الاسم الكامل *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$u?->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">اسم المستخدم</label>
            <input type="text" name="username" class="form-control" value="{{ old('username',$u?->username) }}">
            @error('username')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">البريد الإلكتروني *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$u?->email) }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone',$u?->phone) }}">
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">المدينة</label>
            <input type="text" name="city" class="form-control" value="{{ old('city',$u?->city) }}">
            @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">العنوان</label>
            <input type="text" name="address" class="form-control" value="{{ old('address',$u?->address) }}">
            @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">كلمة المرور الجديدة</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">تأكيد كلمة المرور</label>
            <input type="password" name="confirm_password" class="form-control" autocomplete="new-password">
            @error('confirm_password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">صورة الملف</label>
            <input type="file" name="photo" class="form-control">
            @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 mt-2">
            <button class="btn btn-primary btn-sm">حفظ</button>
            <a href="{{ route('profile') }}" class="btn btn-light btn-sm">رجوع</a>
        </div>
    </form>
@endsection
