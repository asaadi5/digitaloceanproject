@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Customer</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_customer_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_customer_update',$customer->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Photo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$customer->photo) }}" alt="" class="w_100">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Photo</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Email *</label>
                                    <input type="text" class="form-control" name="email" value="{{ $customer->email }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Password</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Status *</label>
                                    <select name="status" class="form-select">
                                        <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="2" {{ $customer->status == 2 ? 'selected' : '' }}>Suspended</option>
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