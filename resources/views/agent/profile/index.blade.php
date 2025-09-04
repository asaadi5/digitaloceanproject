@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent Profile</h2>
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
                <form action="{{ route('agent_profile_submit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">Existing Photo</label>
                            <div class="form-group">
                                @if(Auth::guard('agent')->user()->photo == null)
                                <img src="{{ asset('uploads/default.png') }}" alt="" class="user-photo">
                                @else
                                <img src="{{ asset('uploads/'.Auth::guard('agent')->user()->photo) }}" alt="" class="user-photo">
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
                                <input type="text" name="name" class="form-control" value="{{ Auth::guard('agent')->user()->name }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Email *</label>
                            <div class="form-group">
                                <input type="text" name="email" class="form-control" value="{{ Auth::guard('agent')->user()->email }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Company *</label>
                            <div class="form-group">
                                <input type="text" name="company" class="form-control" value="{{ Auth::guard('agent')->user()->company }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Designation *</label>
                            <div class="form-group">
                                <input type="text" name="designation" class="form-control" value="{{ Auth::guard('agent')->user()->designation }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Phone</label>
                            <div class="form-group">
                                <input type="text" name="phone" class="form-control" value="{{ Auth::guard('agent')->user()->phone }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Address</label>
                            <div class="form-group">
                                <input type="text" name="address" class="form-control" value="{{ Auth::guard('agent')->user()->address }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Country</label>
                            <div class="form-group">
                                <input type="text" name="country" class="form-control" value="{{ Auth::guard('agent')->user()->country }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">State</label>
                            <div class="form-group">
                                <input type="text" name="state" class="form-control" value="{{ Auth::guard('agent')->user()->state }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">City</label>
                            <div class="form-group">
                                <input type="text" name="city" class="form-control" value="{{ Auth::guard('agent')->user()->city }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Zip</label>
                            <div class="form-group">
                                <input type="text" name="zip" class="form-control" value="{{ Auth::guard('agent')->user()->zip }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Facebook</label>
                            <div class="form-group">
                                <input type="text" name="facebook" class="form-control" value="{{ Auth::guard('agent')->user()->facebook }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Twitter</label>
                            <div class="form-group">
                                <input type="text" name="twitter" class="form-control" value="{{ Auth::guard('agent')->user()->twitter }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Linkedin</label>
                            <div class="form-group">
                                <input type="text" name="linkedin" class="form-control" value="{{ Auth::guard('agent')->user()->linkedin }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Instagram</label>
                            <div class="form-group">
                                <input type="text" name="instagram" class="form-control" value="{{ Auth::guard('agent')->user()->instagram }}">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Website</label>
                            <div class="form-group">
                                <input type="text" name="website" class="form-control" value="{{ Auth::guard('agent')->user()->website }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-12 mb-3">
                            <label for="">Biography</label>
                            <div class="form-group">
                                <textarea name="biography" class="form-control h-300" rows="5">{{ Auth::guard('agent')->user()->biography }}</textarea>
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