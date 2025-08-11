@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Create Agent</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_agent_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_agent_store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Photo *</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Name *</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Email *</label>
                                            <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Company *</label>
                                            <input type="text" class="form-control" name="company" value="{{ old('company') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Designation *</label>
                                            <input type="text" class="form-control" name="designation" value="{{ old('designation') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>State</label>
                                            <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Zip</label>
                                            <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Facebook</label>
                                            <input type="text" class="form-control" name="facebook" value="{{ old('facebook') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Twitter</label>
                                            <input type="text" class="form-control" name="twitter" value="{{ old('twitter') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Linkedin</label>
                                            <input type="text" class="form-control" name="linkedin" value="{{ old('linkedin') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Instagram</label>
                                            <input type="text" class="form-control" name="instagram" value="{{ old('instagram') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Website</label>
                                    <input type="text" class="form-control" name="website" value="{{ old('website') }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Password *</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Confirm Password *</label>
                                            <input type="password" class="form-control" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Biography</label>
                                    <textarea name="biography" class="form-control h_200" cols="30" rows="10">{{ old('biography') }}</textarea>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label>Status *</label>
                                    <select name="status" class="form-select">
                                        <option value="0">Pending</option>
                                        <option value="1">Active</option>
                                        <option value="2">Suspended</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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