@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Subscriber</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_subscriber_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_subscriber_update',$subscriber->id) }}" method="post">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Email *</label>
                                    <input type="text" class="form-control" name="email" value="{{ $subscriber->email }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Status *</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $subscriber->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $subscriber->status == 0 ? 'selected' : '' }}>Inactive</option>
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