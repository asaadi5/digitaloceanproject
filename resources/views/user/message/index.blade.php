@extends('user.profile.shell', ['activeTab' => 'messages', 'counts' => $counts ?? []])

@section('profile_tab_content')
    <div class="table-responsive userprof-tab">
        <table class="table table-bordered table-hover mb-0 text-nowrap">
            <thead>
            <tr class="text-center">
                <th style="width:60px">#</th>
                <th>الموضوع</th>
                <th>الوكيل</th>
                <th>التاريخ والوقت</th>
                <th style="width:150px">الإجراء</th>
            </tr>
            </thead>
            <tbody>
            @forelse($messages as $i => $msg)
                <tr>
                    <td class="text-center">{{ $i+1 }}</td>
                    <td>{{ $msg->subject }}</td>
                    <td>
                        {{ optional($msg->agent)->name }}<br>
                        <small class="text-muted">{{ optional($msg->agent)->email }}</small>
                    </td>
                    <td class="text-nowrap">{{ ar_datetime($msg->created_at) }}</td>
                    <td class="text-center">
                        <a href="{{ route('message_reply',$msg->id) }}" class="btn btn-primary btn-sm text-white"
                           title="عرض"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('message_reply',$msg->id) }}#reply" class="btn btn-success btn-sm text-white"
                           title="رد"><i class="fa fa-reply"></i></a>
                        <a href="{{ route('message_delete',$msg->id) }}" class="btn btn-danger btn-sm text-white"
                           onclick="return confirm('حذف الرسالة؟')" title="حذف"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">لا توجد رسائل</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            <a href="{{ route('message_create') }}" class="btn btn-primary btn-sm">إنشاء رسالة جديدة</a>
        </div>
    </div>
@endsection
