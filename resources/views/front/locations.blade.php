@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Locations</h2>
            </div>
        </div>
    </div>
</div>
<div class="location pb_40">
    <div class="container">
        <div class="row">
            @foreach($locations as $item)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="item">
                    <div class="photo">
                        <a href="{{ route('location',$item->slug) }}"><img src="{{ asset('uploads/'.$item->photo) }}" alt=""></a>
                    </div>
                    <div class="text">
                        <h2><a href="{{ route('location',$item->slug) }}">{{ $item->name }}</a></h2>
                        <h4>({{ $item->properties_count }} Properties)</h4>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                {{ $locations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection