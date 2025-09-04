@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Subject: {{ $message->subject }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content user-panel">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="card">
                    @include('agent.sidebar.index')
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <form action="{{ route('agent_message_reply_submit',[$message->id,$message->user_id]) }}" method="post" class="mb_30">
                    @csrf
                    <div class="mb-3">
                        <label for="">Reply *</label>
                        <div class="form-group">
                            <textarea name="reply" class="form-control h-150" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-sm" value="Submit">
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class="w-100">
                                    <img src="{{ asset('uploads/'.$message->user->photo) }}" alt="" class="w-100 rounded-circle mb_10">
                                </td>
                                <td class="w-200">
                                    <b>{{ $message->user->name }}</b><br>
                                    Posted On: {{ $message->created_at->format('d M Y') }}<br>
                                    <span class="badge bg-success">Customer</span>
                                </td>
                                <td>
                                    {{ $message->message }}
                                </td>
                            </tr>
                            @foreach ($replies as $item)
                            <tr>
                                <td class="w-100">
                                    @if($item->sender == 'Customer')
                                    <img src="{{ asset('uploads/'.$message->user->photo) }}" alt="" class="w-100 rounded-circle mb_10">
                                    @else
                                    <img src="{{ asset('uploads/'.$message->agent->photo) }}" alt="" class="w-100 rounded-circle mb_10">
                                    @endif
                                </td>
                                <td class="w-200">
                                    @if($item->sender == 'Customer')
                                    <b>{{ $message->user->name }}</b><br>
                                    @else
                                    <b>{{ $message->agent->name }}</b><br>
                                    @endif
                                    Posted On: {{ $message->created_at->format('d M Y') }}<br>
                                    @if($item->sender == 'Customer')
                                    <span class="badge bg-success">Customer</span>
                                    @else
                                    <span class="badge bg-primary">Agent</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->reply }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection