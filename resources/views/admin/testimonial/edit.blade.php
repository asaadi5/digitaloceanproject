@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Testimonial</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_testimonial_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_testimonial_update',$testimonial->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Photo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$testimonial->photo) }}" alt="" class="w_100">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Photo</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ $testimonial->name }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Designation *</label>
                                    <input type="text" class="form-control" name="designation" value="{{ $testimonial->designation }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Comment *</label>
                                    <textarea name="comment" class="form-control h_100" cols="30" rows="10">{{ $testimonial->comment }}</textarea>
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