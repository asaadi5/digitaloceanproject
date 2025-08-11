@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Agent</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_agent_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_agent_update',$agent->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Photo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$agent->photo) }}" alt="" class="w_100">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Photo</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Name *</label>
                                            <input type="text" class="form-control" name="name" value="{{ $agent->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Email *</label>
                                            <input type="text" class="form-control" name="email" value="{{ $agent->email }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Company *</label>
                                            <input type="text" class="form-control" name="company" value="{{ $agent->company }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Designation *</label>
                                            <input type="text" class="form-control" name="designation" value="{{ $agent->designation }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ $agent->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address" value="{{ $agent->address }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ $agent->country }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>State</label>
                                            <input type="text" class="form-control" name="state" value="{{ $agent->state }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city" value="{{ $agent->city }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Zip</label>
                                            <input type="text" class="form-control" name="zip" value="{{ $agent->zip }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Facebook</label>
                                            <input type="text" class="form-control" name="facebook" value="{{ $agent->facebook }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Twitter</label>
                                            <input type="text" class="form-control" name="twitter" value="{{ $agent->twitter }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Linkedin</label>
                                            <input type="text" class="form-control" name="linkedin" value="{{ $agent->linkedin }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Instagram</label>
                                            <input type="text" class="form-control" name="instagram" value="{{ $agent->instagram }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Website</label>
                                    <input type="text" class="form-control" name="website" value="{{ $agent->website }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Password</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Biography</label>
                                    <textarea name="biography" class="form-control h_200" cols="30" rows="10">{{ $agent->biography }}</textarea>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label>Status *</label>
                                    <select name="status" class="form-select">
                                        <option value="0" {{ $agent->status == 0 ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ $agent->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="2" {{ $agent->status == 2 ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection