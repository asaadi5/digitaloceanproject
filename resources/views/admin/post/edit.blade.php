@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Post</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_post_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_post_update',$post->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Existing Photo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$post->photo) }}" alt="" class="w_200">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Change Photo</label>
                                    <div><input type="file" name="photo"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Title *</label>
                                    <input type="text" class="form-control" name="title" value="{{ $post->title }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Slug *</label>
                                    <input type="text" class="form-control" name="slug" value="{{ $post->slug }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Short Description *</label>
                                    <textarea name="short_description" class="form-control h_100" cols="30" rows="10">{{ $post->short_description }}</textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Description *</label>
                                    <textarea name="description" class="form-control editor" cols="30" rows="10">{{ $post->description }}</textarea>
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