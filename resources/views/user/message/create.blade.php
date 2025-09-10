@extends('user.profile.shell', ['activeTab' => 'messages', 'counts' => $counts ?? []])

@section('profile_tab_content')
    <h5 class="mb-3">إنشاء رسالة جديدة</h5>
    <form action="{{ route('message_store') }}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">اختر الوكيل *</label>
                <select name="agent_id" class="form-control" required>
                    <option value="">— اختر —</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" @selected(old('agent_id')==$ag->id)>{{ $ag->name }}
                            — {{ $ag->email }}</option>
                    @endforeach
                </select>
                @error('agent_id')
                <div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">الموضوع *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                @error('subject')
                <div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label">نص الرسالة *</label>
                <textarea name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                @error('message')
                <div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary btn-sm">إرسال</button>
            <a href="{{ route('message') }}" class="btn btn-light btn-sm">رجوع</a>
        </div>
    </form>
@endsection
