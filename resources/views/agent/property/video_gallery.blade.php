@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent Property Video Gallery</h2>
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
                <h4>Add Video</h4>
                <form action="{{ route('agent_property_video_gallery_store',$property->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <input type="text" name="video" class="form-control" placeholder="YouTube Video Id">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-sm" value="Submit">
                        </div>
                    </div>
                </form>

                <h4 class="mt-4">Existing Videos</h4>
                <div class="photo-all">
                    <div class="row">
                        @if($videos->isEmpty())
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    No videos found.
                                </div>
                            </div>
                        @else
                            @foreach($videos as $item)
                            <div class="col-md-6 col-lg-3">
                                <div class="item item-delete">
                                    <a class="video-button" href="http://www.youtube.com/watch?v={{ $item->video }}">
                                        <img src="http://img.youtube.com/vi/{{ $item->video }}/0.jpg" alt="">
                                        <div class="icon">
                                            <i class="far fa-play-circle"></i>
                                        </div>
                                        <div class="bg"></div>
                                    </a>
                                </div>
                                <a href="{{ route('agent_property_video_gallery_delete',$item->id) }}" class="badge bg-danger mb_20" onClick="return confirm('Are you sure?');">Delete</a>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection