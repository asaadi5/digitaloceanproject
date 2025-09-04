@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Location: {{ $location->name }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="property">
    <div class="container">
        <div class="row">
            @if($properties->count() == 0)
                <div class="text-danger mt_30 mb_30">
                    No property found in this location.
                </div>
            @else
                @foreach($properties as $item)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="item">
                        <div class="photo">
                            <img class="main" src="{{ asset('uploads/'.$item->featured_photo) }}" alt="">
                            <div class="top">
                                @if($item->purpose == 'Sale')
                                <div class="status-sale">
                                    For Sale
                                </div>
                                @else
                                <div class="status-rent">
                                    For Rent
                                </div>
                                @endif
                                @if($item->is_featured == 'Yes')
                                <div class="featured">
                                    Featured
                                </div>
                                @endif
                            </div>
                            <div class="price">${{ $item->price }}</div>
                            <div class="wishlist"><a href=""><i class="far fa-heart"></i></a></div>
                        </div>
                        <div class="text">
                            <h3><a href="{{ route('property_detail',$item->slug) }}">{{ $item->name }}</a></h3>
                            <div class="detail">
                                <div class="stat">
                                    <div class="i1">{{ $item->size }} sqft</div>
                                    <div class="i2">{{ $item->bedroom }} Bed</div>
                                    <div class="i3">{{ $item->bathroom }} Bath</div>
                                </div>
                                <div class="address">
                                    <i class="fas fa-map-marker-alt"></i> {{ $item->address }}
                                </div>
                                <div class="type-location">
                                    <div class="i1">
                                        <i class="fas fa-edit"></i> {{ $item->type->name }}
                                    </div>
                                    <div class="i2">
                                        <i class="fas fa-location-arrow"></i> {{ $item->location->name }}
                                    </div>
                                </div>
                                <div class="agent-section">
                                    @if($item->agent->photo != null)
                                    <img class="agent-photo" src="{{ asset('uploads/'.$item->agent->photo) }}" alt="">
                                    @else
                                    <img class="agent-photo" src="{{ asset('uploads/default.png') }}" alt="">
                                    @endif
                                    <a href="{{ route('agent',$item->agent->id) }}">{{ $item->agent->name }} ({{ $item->agent->company }})</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

@endsection
