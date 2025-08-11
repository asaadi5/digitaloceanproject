@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $post->title }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12">
                <div class="featured-photo">
                    <img src="{{ asset('uploads/'.$post->photo) }}" alt="">
                </div>
                <div class="sub">
                    <div class="item">
                        <b><i class="fa fa-clock-o"></i></b>
                        {{ $post->created_at->format('d M, Y') }}
                    </div>
                    <div class="item">
                        <b><i class="fa fa-eye"></i></b>
                        {{ $post->total_views }}
                    </div>
                </div>
                <div class="main-text">
                    {!! $post->description !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection