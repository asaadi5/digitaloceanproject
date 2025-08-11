@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $property->name }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="property-result pt_50 pb_50">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="left-item">
                    <div class="main-photo">
                        <img src="{{ asset('uploads/'.$property->featured_photo) }}" alt="">
                    </div>
                    <h2>
                        Description
                    </h2>
                    {!! $property->description !!}
                </div>
                <div class="left-item">
                    <h2>
                        Photos
                    </h2>
                    <div class="photo-all">
                        <div class="row">
                            @if($property->photos->count() == 0)
                                <span class="text-danger">No Photos Available</span>
                            @else
                                @foreach($property->photos as $photo)
                                <div class="col-md-6 col-lg-4">
                                    <div class="item">
                                        <a href="{{ asset('uploads/'.$photo->photo) }}" class="magnific">
                                            <img src="{{ asset('uploads/'.$photo->photo) }}" alt="">
                                            <div class="icon">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                            <div class="bg"></div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="left-item">
                    <h2>
                        Videos
                    </h2>
                    <div class="video-all">
                        <div class="row">
                            @if($property->videos->count() == 0)
                                <span class="text-danger">No Videos Available</span>
                            @else
                                @foreach($property->videos as $video)
                                <div class="col-md-6 col-lg-4">
                                    <div class="item">
                                        <a class="video-button" href="http://www.youtube.com/watch?v={{ $video->video }}">
                                            <img src="http://img.youtube.com/vi/{{ $video->video }}/0.jpg" alt="" />
                                            <div class="icon">
                                                <i class="far fa-play-circle"></i>
                                            </div>
                                            <div class="bg"></div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="left-item mb_50">
                    <h2>Share</h2>
                    <div class="share">
                        @php
                        $url = url('property/'.$property->slug);
                        $photo = asset('uploads/'.$property->featured_photo);
                        @endphp
                        <a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}&picture={{ $photo }}" target="_blank">
                            Facebook
                        </a>
                        <a class="twitter" href="https://twitter.com/share?url={{ $url }}&text={{ $property->name }}" target="_blank">
                            Twitter
                        </a>
                        <a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ $url }}&title={{ $property->name }}&summary={{ $property->description }}" target="_blank">
                            LinkedIn
                        </a>
                    </div>
                </div>


                <div class="left-item">
                    <h2>
                        Related Properties
                    </h2>
                    <div class="property related-property pt_0 pb_0">
                        <div class="row">
                            @php
                            $related_properties = \App\Models\Property::where('type_id', $property->type_id)
                                ->where('id', '!=', $property->id)
                                ->orderBy('id', 'desc')
                                ->where('status', 'Active')
                                ->take(2)
                                ->get();    
                            @endphp
                            @if($related_properties->count() == 0)
                                <span class="text-danger">No Related Properties Found</span>
                            @else
                                @foreach($related_properties as $item)
                                <div class="col-lg-6 col-md-6 col-sm-12">
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
                                                    <a href="">{{ $item->agent->name }} ({{ $item->agent->company }})</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">

                <div class="right-item">
                    <h2>Agent</h2>
                    <div class="agent-right d-flex justify-content-start">
                        <div class="left">
                            @if($property->agent->photo != null)
                            <img src="{{ asset('uploads/'.$property->agent->photo) }}" alt="">
                            @else
                            <img src="{{ asset('uploads/default.png') }}" alt="">
                            @endif
                        </div>
                        <div class="right">
                            <h3><a href="">{{ $property->agent->name }}</a></h3>
                            <h4>{{ $property->agent->designation }}</h4>
                        </div>
                    </div>
                    <div class="table-responsive mt_25">
                        <table class="table table-bordered">
                            <tr>
                                <td>Posted On: </td>
                                <td>
                                    {{ $property->created_at->format('d M, Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Email: </td>
                                <td>{{ $property->agent->email }}</td>
                            </tr>

                            @if($property->agent->phone != '')
                            <tr>
                                <td>Phone: </td>
                                <td>{{ $property->agent->phone }}</td>
                            </tr>
                            @endif

                            @if($property->agent->facebook != '' || $property->agent->twitter != '' || $property->agent->instagram != '' || $property->agent->linkedin != '')
                            <tr>
                                <td>Social: </td>
                                <td>
                                    <ul class="agent-ul">
                                        @if($property->agent->facebook != '')
                                        <li><a href="{{ $property->agent->facebook }}"><i class="fab fa-facebook-f"></i></a></li>
                                        @endif
                                        @if($property->agent->twitter != '')
                                        <li><a href="{{ $property->agent->twitter }}"><i class="fab fa-twitter"></i></a></li>
                                        @endif
                                        @if($property->agent->instagram != '')
                                        <li><a href="{{ $property->agent->instagram }}"><i class="fab fa-instagram"></i></a></li>
                                        @endif
                                        @if($property->agent->linkedin != '')
                                        <li><a href="{{ $property->agent->linkedin }}"><i class="fab fa-linkedin-in"></i></a></li>
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="right-item">
                    <h2>Features</h2>
                    <div class="summary">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><b>Price</b></td>
                                    <td>${{ $property->price }}</td>
                                </tr>
                                <tr>
                                    <td><b>Location</b></td>
                                    <td>
                                        {{ $property->location->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Type</b></td>
                                    <td>
                                        {{ $property->type->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Purpose</b></td>
                                    <td>{{ $property->purpose }}</td>
                                </tr>
                                <tr>
                                    <td><b>Bedroom:</b></td>
                                    <td>{{ $property->bedroom }}</td>
                                </tr>
                                <tr>
                                    <td><b>Bathroom:</b></td>
                                    <td>{{ $property->bathroom }}</td>
                                </tr>
                                <tr>
                                    <td><b>Size:</b></td>
                                    <td>{{ $property->size }} sqft</td>
                                </tr>
                                <tr>
                                    <td><b>Floor:</b></td>
                                    <td>{{ $property->floor }}</td>
                                </tr>
                                <tr>
                                    <td><b>Garage:</b></td>
                                    <td>{{ $property->garage }}</td>
                                </tr>
                                <tr>
                                    <td><b>Balcony:</b></td>
                                    <td>{{ $property->balcony }}</td>
                                </tr>
                                <tr>
                                    <td><b>Address:</b></td>
                                    <td>{{ $property->address }}</td>
                                </tr>
                                <tr>
                                    <td><b>Built Year:</b></td>
                                    <td>{{ $property->built_year }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="right-item">
                    <h2>Amenities</h2>
                    <div class="amenity">
                        <ul class="amenity-ul">
                            @php
                            $amenity_arr = explode(',', $property->amenities);
                            $amenity = \App\Models\Amenity::whereIn('id', $amenity_arr)->get();
                            @endphp
                            @foreach($amenity as $item)
                                <li><i class="fas fa-check-square"></i> {{ $item->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if($property->map != '')
                <div class="right-item">
                    <h2>Location Map</h2>
                    <div class="location-map">
                        {!! $property->map !!}
                    </div>
                </div>
                @endif

                <div class="right-item">
                    <h2>Enquery Form</h2>
                    <div class="enquery-form">
                        <form action="{{ route('property_send_message',$property->id) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <input name="name" type="text" class="form-control" placeholder="Full Name">
                            </div>
                            <div class="mb-3">
                                <input name="email" type="email" class="form-control" placeholder="Email Address">
                            </div>
                            <div class="mb-3">
                                <input name="phone" type="text" class="form-control" placeholder="Phone Number">
                            </div>
                            <div class="mb-3">
                                <textarea name="message" class="form-control h-150" rows="3" placeholder="Message"></textarea>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection