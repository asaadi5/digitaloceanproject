@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit FAQ</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_faq_index') }}" class="btn btn-primary"><i class="fas fa-eye"></i> View All</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_faq_update',$faq->id) }}" method="post">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Question *</label>
                                    <input type="text" class="form-control" name="question" value="{{ $faq->question }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Answer *</label>
                                    <textarea name="answer" class="form-control h_200" cols="30" rows="10">{{ $faq->answer }}</textarea>
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