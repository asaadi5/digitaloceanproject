@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customer Message Create</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content user-panel">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="card">
                    @include('user.sidebar.index')
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <a href="{{ route('message') }}" class="btn btn-primary btn-sm mb_10">All Messages</a>
                <form action="{{ route('message_store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="">Subject *</label>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="">Message *</label>
                        <div class="form-group">
                            <textarea name="message" class="form-control h-200" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="">Select Agent *</label>
                        <div class="form-group">
                            <select name="agent_id" class="form-select">
                                @foreach ($agents as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}, {{ $item->company }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection