@extends('user.profile.shell', ['activeTab' => 'messages', 'counts' => $counts ?? []])

@section('profile_tab_content')
    <h5 class="mb-3">الرد على: {{ $message->subject }}</h5>

    <form id="reply" action="{{ route('message_reply_submit',[$message->id,$message->agent_id]) }}" method="post"
          class="mb-4">
        @csrf
        <label class="form-label">الرد *</label>
        <textarea name="reply" class="form-control" rows="4" required>{{ old('reply') }}</textarea>
        @error('reply')
        <div class="text-danger small mt-1">{{ $message }}</div>@enderror

        <div class="mt-3">
            <button class="btn btn-primary btn-sm">إرسال</button>
            <a href="{{ route('message') }}" class="btn btn-light btn-sm">رجوع</a>
        </div>
    </form>

    <div class="table-responsive">
        <style>
            .msg-avatar {
                width: 95px
            }

            .msg-meta {
                width: 180px
            }

            .msg-avatar img {
                width: 95px;
                height: 95px;
                object-fit: cover;
                border-radius: 50%
            }
        </style>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td class="msg-avatar align-top">
                    @php $u = optional($message->user); $uPhoto = $u->photo ? asset('uploads/'.$u->photo) : asset('assets/images/users/default.png'); @endphp
                    <img src="{{ $uPhoto }}" alt="">
                </td>
                <td class="msg-meta align-top">
                    <b>{{ $u->name }}</b><br>
                    بتاريخ: {{ ar_datetime($message->created_at) }}<br>
                    <span class="badge bg-success">العميل</span>
                </td>
                <td class="align-top">{!! nl2br(e($message->message)) !!}</td>
            </tr>

            @foreach($replies as $item)
                @php
                    $isCustomer = $item->sender === 'Customer';
                    $avatar = $isCustomer
                        ? (optional($item->user)->photo ? asset('uploads/'.optional($item->user)->photo) : asset('assets/images/users/default.png'))
                        : (optional($item->agent)->photo ? asset('uploads/'.optional($item->agent)->photo) : asset('assets/images/users/default.png'));
                    $display = $isCustomer ? (optional($item->user)->name ?? 'العميل') : (optional($item->agent)->name ?? 'الوكيل');
                @endphp
                <tr>
                    <td class="msg-avatar align-top"><img src="{{ $avatar }}" alt=""></td>
                    <td class="msg-meta align-top">
                        <b>{{ $display }}</b><br>
                        بتاريخ: {{ ar_datetime($item->created_at) }}<br>
                        <span
                            class="badge {{ $isCustomer?'bg-success':'bg-primary' }}">{{ $isCustomer?'العميل':'الوكيل' }}</span>
                    </td>
                    <td class="align-top">{!! nl2br(e($item->reply)) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
