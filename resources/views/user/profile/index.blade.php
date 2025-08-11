@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customer Profile</h2>
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
                <form action="{{ route('profile_submit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">Existing Photo</label>
                            <div class="form-group">
                                @if(Auth::guard('web')->user()->photo == null)
                                <img src="{{ asset('uploads/default.png') }}" alt="" class="user-photo">
                                @else
                                <img src="{{ asset('uploads/'.Auth::guard('web')->user()->photo) }}" alt="" class="user-photo">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Change Photo</label>
                            <div class="form-group">
                                <input type="file" name="photo">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Name *</label>
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" value="{{ Auth::guard('web')->user()->name }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Email *</label>
                            <div class="form-group">
                                <input type="text" name="email" class="form-control" value="{{ Auth::guard('web')->user()->email }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Password</label>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Confirm Password</label>
                            <div class="form-group">
                                <input type="password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Update">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection