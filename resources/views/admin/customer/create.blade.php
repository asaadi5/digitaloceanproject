@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Create Customer</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_customer_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_customer_store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Photo *</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Email *</label>
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Confirm Password *</label>
                                    <input type="password" class="form-control" name="confirm_password">
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