@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent: {{ $agent->name }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="agent-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="inner">
                    <div class="photo">
                        <img src="{{ asset('uploads/'.$agent->photo) }}" alt="">
                    </div>
                    <div class="detail">
                        <h3>{{ $agent->name }} ({{ $agent->company }})</h3>
                        <h4>{{ $agent->designation }}</h4>
                        
                        @if($agent->biography != '')
                        {!! $agent->biography !!}
                        @endif

                        <div class="contact d-flex justify-content-center">

                            @if($agent->address != '' || $agent->city != '' || $agent->state != '' || $agent->country != '' || $agent->zip != '')
                            <div class="item"><i class="fas fa-map-marker-alt"></i> {{ $agent->address }}, {{ $agent->city }}, {{ $agent->state }}, {{ $agent->country }}, {{ $agent->zip }}</div>
                            @endif

                            @if($agent->phone != '')
                            <div class="item"><i class="fas fa-phone"></i> {{ $agent->phone }}</div>
                            @endif

                            <div class="item"><i class="far fa-envelope"></i> {{ $agent->email }}</div>

                            @if($agent->website != '')
                            <div class="item"><i class="fas fa-globe"></i> {{ $agent->website }}</div>
                            @endif
                        </div>
                        
                        @if($agent->facebook != '' || $agent->twitter != '' || $agent->instagram != '' || $agent->linkedin != '')
                        <ul class="agent-detail-ul">
                            @if($agent->facebook != '')
                            <li><a href="{{ $agent->facebook }}"><i class="fab fa-facebook-f"></i></a></li>
                            @endif
                            @if($agent->twitter != '')
                            <li><a href="{{ $agent->twitter }}"><i class="fab fa-twitter"></i></a></li>
                            @endif
                            @if($agent->instagram != '')
                            <li><a href="{{ $agent->instagram }}"><i class="fab fa-instagram"></i></a></li>
                            @endif
                            @if($agent->linkedin != '')
                            <li><a href="{{ $agent->linkedin }}"><i class="fab fa-linkedin-in"></i></a></li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="property">
    <div class="container">
        <div class="row">

            @if($properties->count() == 0)
                <div class="text-danger mt_30 mb_30">
                    No property found for this agent.
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
                @endforeach
                <div class="col-md-12">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection