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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرسائل</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">الرد على عميل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Message-->
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
                            <h3 class="card-title">رسائل العملاء</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('agent_message_reply_submit',[$message->id,$message->user_id]) }}" method="post" class="mb_30">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">الرد *</label>
                                    <textarea name="reply" class="form-control h-150" cols="30" rows="5" required>{{ old('reply') }}</textarea>
                                    @error('reply')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm">إرسال</button>
                                    <a href="{{ route('agent_message') }}" class="btn btn-light btn-sm">رجوع</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <style>
                                    /* محلي للصفحة فقط */
                                    .msg-avatar   { width:95px; }
                                    .msg-meta     { width:180px; }
                                    .msg-avatar img{
                                        width:95px;height:95px;object-fit:cover;border-radius:50%;
                                    }
                                </style>

                                <table class="table table-bordered">
                                    <tbody>
                                    {{-- الرسالة الأصلية --}}
                                    <tr>
                                        <td class="msg-avatar align-top">
                                            @php
                                                $uPhoto = optional($message->user)->photo;
                                                $uSrc   = $uPhoto ? asset('uploads/'.$uPhoto) : asset('assets/images/users/default.png');
                                            @endphp
                                            <img src="{{ $uSrc }}" alt="" class="mb_10">
                                        </td>
                                        <td class="msg-meta align-top">
                                            <b>{{ optional($message->user)->name ?? 'مستخدم' }}</b><br>
                                            بتاريخ: {{ $message->created_at?->locale('ar')->translatedFormat('d F Y') }}<br>
                                            <span class="badge bg-success">العميل</span>
                                        </td>
                                        <td class="align-top">{!! nl2br(e($message->message)) !!}</td>
                                    </tr>

                                    {{-- الردود --}}
                                    @foreach ($replies as $item)
                                        @php
                                            $isCustomer = ($item->sender === 'Customer');
                                            $avatar = $isCustomer
                                                ? (optional($item->user)->photo ? asset('uploads/'.optional($item->user)->photo) : asset('assets/images/users/default.png'))
                                                : (optional($item->agent)->photo ? asset('uploads/'.optional($item->agent)->photo) : asset('assets/images/users/default.png'));
                                            $displayName = $isCustomer
                                                ? (optional($item->user)->name ?? 'العميل')
                                                : (optional($item->agent)->name ?? 'الوكيل');
                                        @endphp
                                        <tr>
                                            <td class="msg-avatar align-top">
                                                <img src="{{ $avatar }}" alt="" class="mb_10">
                                            </td>
                                            <td class="msg-meta align-top">
                                                <b>{{ $displayName }}</b><br>
                                                بتاريخ: {{ $item->created_at?->locale('ar')->translatedFormat('d F Y') }}<br>
                                                @if($isCustomer)
                                                    <span class="badge bg-success">العميل</span>
                                                @else
                                                    <span class="badge bg-primary">الوكيل</span>
                                                @endif
                                            </td>
                                            <td class="align-top">
                                                {!! nl2br(e($item->reply)) !!}
                                                @if (Route::has('agent_message_reply_delete') && !$isCustomer)
                                                    <div class="mt-2">
                                                        <form action="{{ route('agent_message_reply_delete', $item->id) }}" method="post" class="d-inline" onsubmit="return confirm('حذف هذا الرد؟');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash"></i> حذف الرد
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Message-->
@endsection
