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
                            <li class="breadcrumb-item active text-white" aria-current="page">الرسائل</li>
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
                            <div class="table-responsive userprof-tab">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>التسلسل</th>
                                        <th>الموضوع</th>
                                        <th>العميل</th>
                                        <th>التاريخ والوقت</th>
                                        <th class="text-center">الإجراء</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($messages as $index => $msg)
                                        <tr>
                                            <td>{{ $messages->firstItem() + $index }}</td>
                                            <td>{{ $msg->subject ?? '—' }}</td>
                                            <td>
                                                {{ optional($msg->user)->name ?? 'مستخدم' }}<br>
                                                <small>
                                                    {{ optional($msg->user)->email ?? '—' }}
                                                    @if(optional($msg->user)->phone)
                                                        • {{ $msg->user->phone }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>{{ $msg->created_at?->format('Y-m-d H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('agent_message_reply', $msg->id) }}" class="btn btn-primary btn-sm text-white" title="عرض / رد">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if (Route::has('agent_message_delete'))
                                                    <form action="{{ route('agent_message_delete', $msg->id) }}" method="post" class="d-inline" onsubmit="return confirm('حذف الرسالة وجميع ردودها؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm text-white" title="حذف">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد رسائل حالياً</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                @if (method_exists($messages, 'links'))
                                    <div class="mt-3">
                                        {{ $messages->withQueryString()->links() }}
                                    </div>
                                @endif

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
